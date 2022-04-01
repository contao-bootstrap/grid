<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\BackendUser;
use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\ImageSizes;
use Contao\DataContainer;
use Contao\Input;
use Contao\Model;
use ContaoBootstrap\Core\Environment;
use Doctrine\DBAL\Connection;

use function assert;
use function sprintf;
use function time;

/**
 * ContentDataContainer helper class.
 */
final class ContentListener extends AbstractWrapperDcaListener
{
    /**
     * Database connection.
     */
    private Connection $connection;

    /**
     * Contao framework.
     */
    private ContaoFramework $framework;

    /**
     * Content Model repository.
     *
     * @var Adapter|ContentModel
     */
    private $repository;

    /**
     * Image sizes.
     */
    private ImageSizes $imageSizes;

    /**
     * Contao backend user.
     *
     * @var BackendUser|Adapter
     */
    private $user;

    /**
     * @param Environment         $environment Bootstrap environment.
     * @param Connection          $connection  Database connection.
     * @param ContaoFramework     $framework   Contao framework.
     * @param ImageSizes          $imageSizes  Image sizes.
     * @param Adapter|BackendUser $user        Contao backend user.
     */
    public function __construct(
        Environment $environment,
        Connection $connection,
        ContaoFramework $framework,
        ImageSizes $imageSizes,
        $user
    ) {
        parent::__construct($environment);

        $this->connection = $connection;
        $this->framework  = $framework;
        $this->repository = $this->framework->getAdapter(ContentModel::class);
        $this->imageSizes = $imageSizes;
        $this->user       = $user;
    }

    /**
     * Initialize the dca.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeDca(): void
    {
        $input = $this->framework->getAdapter(Input::class);
        assert($input instanceof Input);

        if ($input->get('act') !== 'edit') {
            return;
        }

        $model = $this->repository->findByPk(Input::get('id'));
        if (! $model || $model->type !== 'bs_grid_gallery') {
            return;
        }

        $GLOBALS['TL_CSS'][] = 'bundles/contaobootstrapgrid/css/backend.css';

        $GLOBALS['TL_DCA']['tl_content']['fields']['galleryTpl']['options_callback'] = [
            'contao_bootstrap.grid.listeners.dca.content',
            'getGalleryTemplates',
        ];
    }

    /**
     * Get all grid parent options.
     *
     * @return array<int|string,string>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getGridParentOptions(): array
    {
        $columns[] = 'tl_content.type = ?';
        $columns[] = 'tl_content.pid = ?';
        $columns[] = 'tl_content.ptable = ?';

        $values[] = 'bs_gridStart';
        $values[] = CURRENT_ID;
        $values[] = $GLOBALS['TL_DCA']['tl_content']['config']['ptable'];

        $collection = $this->repository->findBy($columns, $values, ['tl_content.sorting']);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bs_grid_name,
                    $model->getRelated('bs_grid')->title
                );
            }
        }

        return $options;
    }

    /**
     * Get all gallery templates.
     *
     * @return list<string>|array<string,list<string>>
     */
    public function getGalleryTemplates(): array
    {
        $adapter = $this->framework->getAdapter(Controller::class);
        assert($adapter instanceof Controller);

        return $adapter->getTemplateGroup('bs_gallery_');
    }

    /**
     * Dynamically add flags to the "multiSRC" field.
     *
     * @param mixed         $value         Given value.
     * @param DataContainer $dataContainer Data Container driver.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setMultiSrcFlags($value, DataContainer $dataContainer)
    {
        if ($dataContainer->activeRecord && $dataContainer->activeRecord->type === 'bs_grid_gallery') {
            $fieldsDca =& $GLOBALS['TL_DCA'][$dataContainer->table]['fields'][$dataContainer->field]['eval'];

            $fieldsDca['isGallery']  = true;
            $fieldsDca['extensions'] = Config::get('validImageTypes');
        }

        return $value;
    }

    /**
     * Get the image sizes.
     *
     * @return string[][]
     */
    public function getImageSizes(): array
    {
        return $this->imageSizes->getOptionsForUser($this->user);
    }

    /**
     * Create a grid element.
     *
     * @param ContentModel $current Current content model.
     * @param string       $type    Type of the content model.
     * @param int          $sorting The sorting value.
     */
    protected function createGridElement($current, string $type, int &$sorting): Model
    {
        $model                 = new ContentModel();
        $model->tstamp         = time();
        $model->pid            = $current->pid;
        $model->ptable         = $current->ptable;
        $model->sorting        = $sorting;
        $model->type           = $type;
        $model->bs_grid_parent = $current->id;
        $model->save();

        return $model;
    }

    /**
     * Get the next content elements.
     *
     * @param ContentModel $current Current content model.
     *
     * @return ContentModel[]
     */
    protected function getNextElements($current): array
    {
        $collection = $this->repository->findBy(
            [
                'tl_content.ptable=?',
                'tl_content.pid=?',
                '(tl_content.type != ? AND tl_content.bs_grid_parent != ?)',
                'tl_content.sorting > ?',
            ],
            [$current->ptable, $current->pid, 'bs_gridStop', $current->id, $current->sorting],
            ['order' => 'tl_content.sorting ASC']
        );

        if ($collection) {
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /**
     * Get related stop element.
     *
     * @param ContentModel $current Current element.
     *
     * @return ContentModel|Model
     */
    protected function getStopElement($current): Model
    {
        $stopElement = $this->repository->findOneBy(
            ['tl_content.type=?', 'tl_content.bs_grid_parent=?'],
            ['bs_gridStop', $current->id]
        );

        if ($stopElement) {
            return $stopElement;
        }

        $nextElements = $this->getNextElements($current);
        $stopElement  = $this->createStopElement($current, (int) $current->sorting);
        $this->updateSortings($nextElements, (int) $stopElement->sorting);

        return $stopElement;
    }
}

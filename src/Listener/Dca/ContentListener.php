<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\BackendUser;
use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\ImageSizes;
use Contao\Database\Result;
use Contao\DataContainer;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use ContaoBootstrap\Core\Environment;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Override;
use stdClass;

use function sprintf;
use function time;

/**
 * ContentDataContainer helper class.
 *
 * @extends AbstractWrapperDcaListener<ContentModel>
 */
final class ContentListener extends AbstractWrapperDcaListener
{
    /**
     * @param Environment     $environment Bootstrap environment.
     * @param ContaoFramework $framework   Contao framework.
     * @param ImageSizes      $imageSizes  Image sizes.
     * @param BackendUser     $user        Contao backend user.
     */
    public function __construct(
        Environment $environment,
        private readonly ContaoFramework $framework,
        private readonly ImageSizes $imageSizes,
        private readonly BackendUser $user,
        RepositoryManager $repositories,
    ) {
        parent::__construct($environment, $repositories);
    }

    /**
     * Initialize the dca.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeDca(): void
    {
        $input = $this->framework->getAdapter(Input::class);
        if ($input->get('act') !== 'edit') {
            return;
        }

        /** @psalm-suppress RiskyCast */
        $model = $this->repositories->getRepository(ContentModel::class)->find((int) $input->get('id'));
        if (! $model || $model->type !== 'bs_grid_gallery') {
            return;
        }

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
    public function getGridParentOptions(DataContainer $dataContainer): array
    {
        $columns = [
            'tl_content.type = ?',
            'tl_content.pid = ?',
            'tl_content.ptable = ?',
        ];

        $values = [
            'bs_gridStart',
            $dataContainer->currentPid,
            $GLOBALS['TL_DCA']['tl_content']['config']['ptable'],
        ];

        $options    = [];
        $collection = $this->repositories
            ->getRepository(ContentModel::class)
            ->findBy($columns, $values, ['order' => '.sorting']);

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                /** @psalm-suppress UndefinedMagicPropertyFetch */
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bs_grid_name,
                    (string) $model->getRelated('bs_grid')?->title,
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
        return $this->framework->getAdapter(Controller::class)->getTemplateGroup('bs_gallery_');
    }

    /**
     * Dynamically add flags to the "multiSRC" field.
     *
     * @param mixed         $value         Given value.
     * @param DataContainer $dataContainer Data Container driver.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setMultiSrcFlags(mixed $value, DataContainer $dataContainer): mixed
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
     * {@inheritDoc}
     */
    #[Override]
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
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    #[Override]
    protected function getNextElements(Model|Result|stdClass $current): array
    {
        $collection = $this->repositories->getRepository(ContentModel::class)->findBy(
            [
                'tl_content.ptable=?',
                'tl_content.pid=?',
                '(tl_content.type != ? AND tl_content.bs_grid_parent != ?)',
                'tl_content.sorting > ?',
            ],
            [$current->ptable, $current->pid, 'bs_gridStop', $current->id, $current->sorting],
            ['order' => 'tl_content.sorting ASC'],
        );

        if ($collection instanceof Collection) {
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    #[Override]
    protected function getStopElement(Model|Result|stdClass $current): Model
    {
        $stopElement = $this->repositories->getRepository(ContentModel::class)->findOneBy(
            ['tl_content.type=?', 'tl_content.bs_grid_parent=?'],
            ['bs_gridStop', $current->id],
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

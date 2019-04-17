<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\Module;

use Contao\Controller;
use Contao\Database\Result;
use Contao\Model;
use Contao\ModuleModel;
use Contao\StringUtil;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class GridModule.
 *
 * @package ContaoBootstrap\Grid\Component
 */
final class GridModule extends AbstractModule
{
    /**
     * Grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * Response Tagger.
     *
     * @var ResponseTagger
     */
    private $responseTagger;

    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'mod_bs_grid';

    /**
     * GridModule constructor.
     *
     * @param Model|Result   $model          Module configuration as model or result.
     * @param TemplateEngine $templateEngine The template engine.
     * @param Translator     $translator     The translator.
     * @param GridProvider   $gridProvider   The grid provider.
     * @param ResponseTagger $responseTagger Response tagger.
     * @param string         $column         Name of the column or section the module is rendered in.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        Translator $translator,
        GridProvider $gridProvider,
        ResponseTagger $responseTagger,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $translator, $column);

        $this->gridProvider   = $gridProvider;
        $this->responseTagger = $responseTagger;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        $data = parent::prepareTemplateData($data);

        $config    = StringUtil::deserialize($this->get('bs_gridModules'), true);
        $moduleIds = $this->getModuleIds($config);
        $modules   = $this->preCompileModules($moduleIds);
        $iterator  = $this->getGridIterator();

        if ($iterator) {
            $iterator->rewind();

            $data['rowClasses']  = $iterator->row();
            $data['firstColumn'] = $iterator->current();
        }

        $data['modules'] = $this->generateModules($config, $modules, $iterator);

        return $data;
    }

    /**
     * Get the grid iterator.
     *
     * @return GridIterator|null
     */
    protected function getGridIterator(): ?GridIterator
    {
        try {
            $iterator = $this->gridProvider->getIterator('ce:' . $this->get('id'), (int) $this->get('bs_grid'));
            $this->responseTagger->addTags(['contao.db.tl_bs_grid.' . $this->get('bs_grid')]);

            return $iterator;
        } catch (\Exception $e) {
            // Do nothing.
            return null;
        }
    }

    /**
     * Generate all modules.
     *
     * @param array             $config   Module config.
     * @param array             $modules  Generated modules.
     * @param GridIterator|null $iterator Grid iterator.
     *
     * @return array
     */
    protected function generateModules(array $config, array $modules, ?GridIterator $iterator = null): array
    {
        $buffer = [];

        foreach ($config as $entry) {
            if ($entry['inactive'] || !$entry['module']) {
                continue;
            }

            if (is_numeric($entry['module'])) {
                if (!empty($modules[$entry['module']])) {
                    $buffer[] = $modules[$entry['module']];
                }

                continue;
            }

            if ($iterator) {
                $iterator->next();

                foreach ($iterator->resets() as $reset) {
                    $buffer[] = '<div class="clearfix w-100 ' . $reset . '"></div>';
                }

                $buffer[] = sprintf(
                    "\n" . '</div>' . "\n" . '<div class="%s">',
                    $iterator->current()
                );
            }
        }

        return $buffer;
    }

    /**
     * Get the module ids.
     *
     * @param array $config Config.
     *
     * @return array
     */
    protected function getModuleIds(array $config): array
    {
        $moduleIds = array_filter(
            array_map(
                function ($item) {
                    return $item['module'];
                },
                array_filter(
                    $config,
                    function ($item) {
                        return $item['inactive'] == '';
                    }
                )
            ),
            'is_numeric'
        );

        return $moduleIds;
    }

    /**
     * Precompile the modules.
     *
     * @param array $moduleIds List of module ids.
     *
     * @return array
     */
    protected function preCompileModules(array $moduleIds): array
    {
        $collection = ModuleModel::findMultipleByIds($moduleIds);
        $modules    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $modules[$model->id] = Controller::getFrontendModule($model, $this->getColumn());
            }
        }

        return $modules;
    }
}

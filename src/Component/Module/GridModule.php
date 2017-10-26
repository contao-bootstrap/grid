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

use Contao\BackendTemplate;
use Contao\Module;
use Contao\ModuleModel;
use ContaoBootstrap\Grid\GridIterator;
use Patchwork\Utf8;

/**
 * Class GridModule.
 *
 * @package ContaoBootstrap\Grid\Component
 */
class GridModule extends Module
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_bs_grid';

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function generate()
    {
        if ($this->isBackendRequest()) {
            $template           = new BackendTemplate('be_wildcard');
            $template->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['bs_grid'][0]) . ' ###';
            $template->title    = $this->headline;
            $template->id       = $this->id;
            $template->link     = $this->name;
            $template->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $template->parse();
        }

        return parent::generate();
    }


    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $config    = \StringUtil::deserialize($this->bs_gridModules, true);
        $moduleIds = $this->getModuleIds($config);
        $modules   = $this->preCompileModules($moduleIds);

        $iterator = $this->getGridIterator();

        if ($iterator) {
            $iterator->rewind();

            $this->Template->rowClasses  = $iterator->row();
            $this->Template->firstColumn = $iterator->current();
        }

        $this->Template->modules = $this->generateModules($config, $modules, $iterator);
    }

    /**
     * Check if we are in backend mode.
     *
     * @return bool
     */
    protected function isBackendRequest(): bool
    {
        $scopeMatcher   = static::getContainer()->get('contao.routing.scope_matcher');
        $currentRequest = static::getContainer()->get('request_stack')->getCurrentRequest();

        return $scopeMatcher->isBackendRequest($currentRequest);
    }
    /**
     * Get the grid iterator.
     *
     * @return GridIterator|null
     */
    protected function getGridIterator():? GridIterator
    {
        $provider = static::getContainer()->get('contao_bootstrap.grid.grid_provider');

        try {
            return $provider->getIterator('ce:' . $this->id, (int) $this->bs_grid);
        } catch (\Exception $e) {
            // Do nothing.
        }

        return null;
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
                $modules[$model->id] = static::getFrontendModule($model, $this->strColumn);
            }
        }

        return $modules;
    }
}

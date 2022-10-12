<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\Module;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Model;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\StringUtil;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Controller\FrontendModule\AbstractFrontendModuleController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_filter;
use function array_map;
use function array_values;
use function is_numeric;
use function sprintf;

/** @FrontendModule("bs_grid", category="miscellaneous") */
final class GridFrontendModuleController extends AbstractFrontendModuleController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        TranslatorInterface $translator,
        private readonly GridProvider $gridProvider,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $router, $translator);
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $config    = StringUtil::deserialize($model->bs_gridModules, true);
        $moduleIds = $this->getModuleIds($config);
        $modules   = $this->preCompileModules($model, $moduleIds);
        $iterator  = $this->getGridIterator($model);

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
     */
    protected function getGridIterator(ModuleModel $model): GridIterator|null
    {
        try {
            $iterator = $this->gridProvider->getIterator('mod:' . $model->id, (int) $model->bs_grid);
            $this->tagResponse('contao.db.tl_bs_grid.' . $model->bs_grid);

            return $iterator;
        } catch (GridNotFound) {
            // Do nothing.
            return null;
        }
    }

    /**
     * Generate all modules.
     *
     * @param array<string,mixed>      $config   Module config.
     * @param array<int|string,string> $modules  Generated modules.
     * @param GridIterator|null        $iterator Grid iterator.
     *
     * @return array<int|string,string>
     */
    protected function generateModules(array $config, array $modules, GridIterator|null $iterator = null): array
    {
        $buffer = [];

        foreach ($config as $entry) {
            if ($entry['inactive'] || ! $entry['module']) {
                continue;
            }

            if (is_numeric($entry['module'])) {
                if (! empty($modules[$entry['module']])) {
                    $buffer[] = $modules[$entry['module']];
                }

                continue;
            }

            if (! $iterator) {
                continue;
            }

            $iterator->next();

            foreach ($iterator->resets() as $reset) {
                $buffer[] = '<div class="clearfix w-100 ' . $reset . '"></div>';
            }

            $buffer[] = sprintf(
                "\n" . '</div>' . "\n" . '<div class="%s">',
                $iterator->current(),
            );
        }

        return $buffer;
    }

    /**
     * Get the module ids.
     *
     * @param array<string,mixed> $config Config.
     *
     * @return list<int|string>
     */
    protected function getModuleIds(array $config): array
    {
        return array_values(
            array_filter(
                array_map(
                    /**
                     * @param array<string,mixed> $item
                     *
                     * @return int|string
                     */
                    static function (array $item) {
                        return $item['module'];
                    },
                    array_filter(
                        $config,
                        /** @param array<string,mixed> $item */
                        static function (array $item): bool {
                            return $item['inactive'] === '';
                        },
                    ),
                ),
                'is_numeric',
            ),
        );
    }

    /**
     * Precompile the modules.
     *
     * @param list<string|int> $moduleIds List of module ids.
     *
     * @return array<string|int,string>
     */
    protected function preCompileModules(ModuleModel $moduleModel, array $moduleIds): array
    {
        $collection = ModuleModel::findMultipleByIds($moduleIds);
        $modules    = [];

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                $modules[$model->id] = Controller::getFrontendModule($model, $moduleModel->inColumn);
            }
        }

        return $modules;
    }
}

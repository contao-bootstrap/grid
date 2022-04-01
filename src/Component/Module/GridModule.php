<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\Module;

use Contao\Controller;
use Contao\Database\Result;
use Contao\Model;
use Contao\ModuleModel;
use Contao\StringUtil;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_filter;
use function array_map;
use function is_numeric;
use function sprintf;

final class GridModule extends AbstractModule
{
    /**
     * Grid provider.
     */
    private GridProvider $gridProvider;

    /**
     * Response Tagger.
     */
    private ResponseTagger $responseTagger;

    /**
     * Template name.
     */
    protected string $templateName = 'mod_bs_grid';

    /**
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
     */
    protected function getGridIterator(): ?GridIterator
    {
        try {
            $iterator = $this->gridProvider->getIterator('mod:' . $this->get('id'), (int) $this->get('bs_grid'));
            $this->responseTagger->addTags(['contao.db.tl_bs_grid.' . $this->get('bs_grid')]);

            return $iterator;
        } catch (GridNotFound $e) {
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
    protected function generateModules(array $config, array $modules, ?GridIterator $iterator = null): array
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
                $iterator->current()
            );
        }

        return $buffer;
    }

    /**
     * Get the module ids.
     *
     * @param array<string,mixed> $config Config.
     *
     * @return array<string,mixed>
     */
    protected function getModuleIds(array $config): array
    {
        return array_filter(
            array_map(
                static function ($item) {
                    return $item['module'];
                },
                array_filter(
                    $config,
                    static function ($item) {
                        return $item['inactive'] === '';
                    }
                )
            ),
            'is_numeric'
        );
    }

    /**
     * Precompile the modules.
     *
     * @param list<string|int> $moduleIds List of module ids.
     *
     * @return array<string|int,string>
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

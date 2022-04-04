<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function assert;

/** @ContentElement("ce_bs_gridStart", category="bs_grid") */
final class GridStartElementController extends AbstractGridElementController
{
    /** {@inheritDoc} */
    protected function preGenerate(Request $request, Model $model, string $section, ?array $classes = null): ?Response
    {
        if (! $this->isBackendRequest($request)) {
            return null;
        }

        assert($model instanceof ContentModel);

        return $this->renderContentBackendView(null, $this->getIterator($model));
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        assert($model instanceof ContentModel);

        $iterator = $this->getIterator($model);
        if ($iterator) {
            $data['rowClasses']    = $iterator->row();
            $data['columnClasses'] = $iterator->current();
        }

        return $data;
    }

    protected function getIterator(ContentModel $model): ?GridIterator
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ce:' . $model->id, (int) $model->bs_grid);
            $this->tagResponse('contao.db.tl_bs_grid.' . $model->bs_grid);

            return $iterator;
        } catch (GridNotFound $e) {
            // Do nothing. In backend view an error is shown anyway.
            return null;
        }
    }
}

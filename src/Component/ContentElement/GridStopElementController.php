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

/** @ContentElement("ce_bs_gridStop", category="bs_grid") */
final class GridStopElementController extends AbstractGridElementController
{
    /** {@inheritDoc} */
    protected function preGenerate(Request $request, Model $model, string $section, ?array $classes = null): ?Response
    {
        assert($model instanceof ContentModel);

        if (! $this->isBackendRequest($request)) {
            $iterator = $this->getIterator($model);
            if ($iterator) {
                $iterator->rewind();
            }
            return null;
        }

        return $this->renderContentBackendView($this->getParent($model));
    }

    /**
     * Get the parent model.
     */
    protected function getParent(ContentModel $model): ?ContentModel
    {
        return ContentModel::findByPk($model->bs_grid_parent);
    }

    protected function getIterator(ContentModel $model): ?GridIterator
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent($model);

        if ($parent) {
            try {
                $iterator = $provider->getIterator('ce:' . $parent->id, (int) $parent->bs_grid);
                $this->tagResponse('contao.db.tl_bs_grid.' . $parent->bs_grid);

                return $iterator;
            } catch (GridNotFound $e) {
                // Do nothing. In backend view an error is shown anyway.
                return null;
            }
        }

        return null;
    }
}

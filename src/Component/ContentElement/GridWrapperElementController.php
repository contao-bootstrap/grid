<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_key_first;

/** @psalm-suppress PropertyNotSetInConstructor */
#[AsContentElement('bs_grid_wrapper', 'bs_grid', nestedFragments: true)]
final class GridWrapperElementController extends AbstractContentElementController
{
    public function __construct(
        private readonly GridProvider $provider,
        private readonly ColorRotate $colorRotate,
    ) {
    }

    #[Override]
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->isBackendScope($request)) {
            $template->setName('backend/grid_wildcard');

            $template->set('title', $model->bs_grid_name);
            $template->set('color', $this->colorRotate->getColor('ce:' . $model->id));

            return $template->getResponse();
        }

        $template->set('iterator', $this->getIterator($model));

        return $template->getResponse();
    }

    protected function getIterator(ContentModel $model): GridIterator|null
    {
        try {
            $iterator = $this->provider->getIterator('ce:' . $model->id, (int) $model->bs_grid);
            $this->tagResponse('contao.db.tl_bs_grid.' . $model->bs_grid);

            return $iterator;
        } catch (GridNotFound) {
            // Do nothing. In backend view an error is shown anyway.
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FrontendUser;
use Contao\Model;
use Contao\StringUtil;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\Gallery\Gallery;
use ContaoBootstrap\Grid\Gallery\GalleryBuilder;
use ContaoBootstrap\Grid\Gallery\Sorting\SortBy;
use ContaoBootstrap\Grid\Gallery\Sorting\SortByDate;
use ContaoBootstrap\Grid\Gallery\Sorting\SortByName;
use ContaoBootstrap\Grid\Gallery\Sorting\SortCustom;
use ContaoBootstrap\Grid\Gallery\Sorting\SortRandom;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\AbstractContentElementController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

use function array_merge;
use function assert;
use function trigger_error;

use const E_USER_DEPRECATED;

/** @ContentElement("bs_grid_gallery", category="media") */
final class GalleryElementController extends AbstractContentElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        private Security $security,
        private GridProvider $gridProvider,
        private ContaoFramework $framework,
        private string $projectDir,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker);
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
        $sources = $this->determineSources($model);
        if ($sources === []) {
            return new Response();
        }

        $galleryBuilder = new GalleryBuilder($this->framework, $this->projectDir);
        $galleryBuilder
            ->addSources($sources)
            ->perPage('page_g' . $model->id, (int) $model->perPage)
            ->limit((int) $model->numberOfItems)
            ->sortBy($this->determineSorting($model));

        $request->attributes->set(Gallery::class, $galleryBuilder->build());

        return null;
    }

    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $gallery = $request->attributes->get(Gallery::class);

        assert($gallery instanceof Gallery);

        $data['pagination'] = $gallery->pagination ? $gallery->pagination->generate("\n") : null;
        $data['images']     = $this->render(
            'fe:' . $this->getGalleryTemplateName($model, $request),
            array_merge(
                $data,
                [
                    'body'     => $gallery->compileImages($model),
                    'grid'     => $this->getGridIterator($model),
                    'headline' => $model->headline,
                ],
            ),
        );

        return $data;
    }

    /** @return list<string> */
    private function determineSources(ContentModel $model): array
    {
        if ($model->useHomeDir && $this->security->isGranted('ROLE_MEMBER')) {
            $user = $this->security->getUser();
            if ($user instanceof FrontendUser && $user->assignDir && $user->homeDir) {
                return [$user->homeDir];
            }

            return [];
        }

        return StringUtil::deserialize($model->multiSRC, true);
    }

    /**
     * Get the gallery template name.
     */
    protected function getGalleryTemplateName(ContentModel $model, Request $request): string
    {
        $templateName = 'bs_gallery_default';

        // Use a custom template
        if ($this->scopeMatcher->isFrontendRequest($request) && $model->galleryTpl !== '') {
            return $model->galleryTpl;
        }

        return $templateName;
    }

    /**
     * Get the grid iterator.
     */
    private function getGridIterator(ContentModel $model): GridIterator|null
    {
        try {
            if ($model->bs_grid) {
                $iterator = $this->gridProvider->getIterator('ce:' . $model->id, (int) $model->bs_grid);
                $this->tagResponse('contao.db.tl_bs_grid.' . $model->bs_grid);

                return $iterator;
            }
        } catch (GridNotFound) {
            // No Grid found, return null.
            return null;
        }

        return null;
    }

    private function determineSorting(ContentModel $model): SortBy
    {
        // Sort array
        switch ($model->sortBy) {
            default:
            case 'name_asc':
                return SortByName::asc();

            case 'name_desc':
                return SortByName::desc();

            case 'date_asc':
                return SortByDate::asc();

            case 'date_desc':
                return SortByDate::desc();

            // Deprecated since Contao 4.0, to be removed in Contao 5.0
            case 'meta':
                @trigger_error(
                    'The "meta" key in ContentGallery::compile() has been deprecated and will no longer work in '
                    . 'Contao 5.0.',
                    E_USER_DEPRECATED,
                );
                // @codingStandardsIgnoreEnd

            // no break here. Handle meta the same as custom.
            case 'custom':
                return new SortCustom(StringUtil::deserialize($model->orderSRC, true));

            case 'random':
                return new SortRandom();
        }
    }
}

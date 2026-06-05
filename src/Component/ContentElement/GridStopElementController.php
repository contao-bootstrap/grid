<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * @deprecated Use GridWrapperElementController with bs_grid_wrapper instead.
 *             Will be removed in a future major version.
 *
 * @ContentElement("bs_gridStop", category="bs_grid", template="ce_bs_gridStop")
 */
final class GridStopElementController extends AbstractGridElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        GridProvider $gridProvider,
        ColorRotate $colorRotate,
        TranslatorInterface $translator,
        private readonly RepositoryManager $repositories,
    ) {
        trigger_error(
            sprintf(
                'Content element "%s" is deprecated. Use "%s" instead. Will be removed in a future major version.',
                'bs_gridStop',
                'bs_grid_wrapper',
            ),
            E_USER_DEPRECATED,
        );

        parent::__construct(
            $templateRenderer,
            $scopeMatcher,
            $responseTagger,
            $tokenChecker,
            $gridProvider,
            $colorRotate,
            $translator,
        );
    }

    /** {@inheritDoc} */
    #[Override]
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
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
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    protected function getParent(ContentModel $model): ContentModel|null
    {
        return $this->repositories->getRepository(ContentModel::class)->find((int) $model->bs_grid_parent);
    }

    protected function getIterator(ContentModel $model): GridIterator|null
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent($model);

        if ($parent) {
            try {
                $iterator = $provider->getIterator('ce:' . $parent->id, (int) $parent->bs_grid);
                $this->tagResponse('contao.db.tl_bs_grid.' . $parent->bs_grid);

                return $iterator;
            } catch (GridNotFound) {
                // Do nothing. In backend view an error is shown anyway.
                return null;
            }
        }

        return null;
    }
}

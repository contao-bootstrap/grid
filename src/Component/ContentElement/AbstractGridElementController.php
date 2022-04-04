<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\AbstractContentElementController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractGridElementController extends AbstractContentElementController
{
    protected GridProvider $gridProvider;

    protected ColorRotate $colorRotate;

    protected TranslatorInterface $translator;

    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        GridProvider $gridProvider,
        ColorRotate $colorRotate,
        TranslatorInterface $translator
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker);

        $this->gridProvider = $gridProvider;
        $this->colorRotate  = $colorRotate;
        $this->translator   = $translator;
    }

    protected function renderContentBackendView(?ContentModel $start, ?GridIterator $iterator = null): Response
    {
        return $this->renderResponse(
            'fe:be_bs_grid',
            [
                'color'   => $start ? $this->rotateColor('ce:' . $start->id) : null,
                'name'    => $start ? $start->bs_grid_name : null,
                'error'   => ! $start
                    ? $this->translator->trans('ERR.bsGridParentMissing', [], 'contao_default')
                    : null,
                'classes' => $iterator ? $iterator->current() : null,
            ]
        );
    }

    /**
     * Rotate the color for an identifier.
     *
     * @param string $identifier The color identifier.
     */
    protected function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }

    /**
     * Get the grid provider.
     */
    protected function getGridProvider(): GridProvider
    {
        return $this->gridProvider;
    }
}

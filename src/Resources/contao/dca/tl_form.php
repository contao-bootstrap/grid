<?php

declare(strict_types=1);

use ContaoBootstrap\Grid\Listener\Dca\FormFixFormFieldParentRelationsListener;

/*
 * Config
 */

$GLOBALS['TL_DCA']['tl_form']['config']['oncopy_callback'][] = [
    FormFixFormFieldParentRelationsListener::class,
    'onCopy',
];

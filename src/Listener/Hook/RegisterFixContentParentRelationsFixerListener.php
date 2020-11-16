<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use ContaoBootstrap\Grid\Listener\Dca\ParentFixContentParentRelationsListener;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use function is_array;

/**
 * The RegisterOnCopyCallbackListener registers an onload_callback for each data container
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
final class RegisterFixContentParentRelationsFixerListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Supported data container drivers.
     *
     * @var array
     */
    private $supportedDrivers;

    /**
     * RegisterOnCopyCallbackListener constructor.
     *
     * @param DcaManager $dcaManager       Data container manager.
     * @param array      $supportedDrivers Supported data container drivers.
     */
    public function __construct(DcaManager $dcaManager, array $supportedDrivers)
    {
        $this->dcaManager       = $dcaManager;
        $this->supportedDrivers = $supportedDrivers;
    }

    /**
     * Handle the loadDataContainer callback.
     *
     * @param string $tableName The name of the table which data container is loaded.
     *
     * @return void
     */
    public function onLoadDataContainer(string $tableName) : void
    {
        try {
            $definition = $this->dcaManager->getDefinition($tableName);
        } catch (AssertionFailed $exception) {
            return;
        }

        if (! in_array($definition->get(['config', 'dataContainer']), $this->supportedDrivers, true)) {
            return;
        }

        if ($definition->get(['config', 'notCopyable'], false)) {
            return;
        }

        $this->registerOnCopyCallbackListener($definition);
    }

    /**
     * Register the oncopy_callback in the given definition.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     */
    private function registerOnCopyCallbackListener(Definition $definition) : void
    {
        $definition->modify(
            ['config', 'oncopy_callback'],
            function ($value) {
                if (!is_array($value)) {
                    $value = [];
                }

                $value[] = [ParentFixContentParentRelationsListener::class, 'onCopy'];

                return $value;
            }
        );
    }
}

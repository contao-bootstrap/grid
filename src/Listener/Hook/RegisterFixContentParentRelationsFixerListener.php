<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use ContaoBootstrap\Grid\Listener\Dca\ParentFixContentParentRelationsListener;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function in_array;
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
     */
    private DcaManager $dcaManager;

    /**
     * Supported data container drivers.
     *
     * @var list<string>
     */
    private array $supportedDrivers;

    /**
     * @param DcaManager   $dcaManager       Data container manager.
     * @param list<string> $supportedDrivers Supported data container drivers.
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
     */
    public function onLoadDataContainer(string $tableName): void
    {
        try {
            $definition = $this->dcaManager->getDefinition($tableName);
        } catch (AssertionFailed) {
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
     */
    private function registerOnCopyCallbackListener(Definition $definition): void
    {
        $definition->modify(
            ['config', 'oncopy_callback'],
            /**
             * @param mixed $value
             *
             * @return list<array<int,string>|callable>
             *
             * @psalm-suppress MoreSpecificReturnType
             * @psalm-suppress LessSpecificReturnStatement
             */
            static function ($value): array {
                if (! is_array($value)) {
                    $value = [];
                }

                $value[] = [ParentFixContentParentRelationsListener::class, 'onCopy'];

                return $value;
            },
        );
    }
}

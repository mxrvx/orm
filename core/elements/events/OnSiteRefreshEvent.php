<?php

declare(strict_types=1);

/**
 * OnSiteRefreshEvent
 * @var modX $modx
 * @var array $scriptProperties
 */

use MXRVX\ORM\Events\OnSiteRefreshEvent;

try {
    (new OnSiteRefreshEvent($modx, $scriptProperties))();
} catch (\Throwable $e) {
    $modx->log(
        \modX::LOG_LEVEL_ERROR,
        \sprintf("\nError: %s\nFile: %s\nTrace: %s", $e->getMessage(), $e->getFile(), \var_export($e->getTrace(), true)),
    );
}

<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

return \MXRVX\CodeStyle\Builder::create()
    ->include(__DIR__ . '/core')
    ->include(__FILE__)
    ->build();

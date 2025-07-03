<?php

declare(strict_types=1);

if (!\class_exists('\\modX') && \class_exists('\\MODX\\Revolution\\modX')) {
    \class_alias('\\MODX\\Revolution\\modX', '\\modX');
}
if (!\class_exists('\\modProcessorResponse') && \class_exists('\\MODX\\Revolution\\Processors\\ProcessorResponse')) {
    \class_alias('\\MODX\\Revolution\\Processors\\ProcessorResponse', '\\modProcessorResponse');
}
if (!\class_exists('\\modManagerController') && \class_exists('\\MODX\\Revolution\\modManagerController')) {
    \class_alias('\\MODX\\Revolution\\modManagerController', '\\modManagerController');
}
if (!\class_exists('\\modExtraManagerController') && \class_exists('\\MODX\\Revolution\\modExtraManagerController')) {
    \class_alias('\\MODX\\Revolution\\modExtraManagerController', '\\modExtraManagerController');
}
if (!\class_exists('\\modLexicon') && \class_exists('\\MODX\\Revolution\\modLexicon')) {
    \class_alias('\\MODX\\Revolution\\modLexicon', '\\modLexicon');
}
if (!\class_exists('\\modUser') && \class_exists('\\MODX\\Revolution\\modUser')) {
    \class_alias('\\MODX\\Revolution\\modUser', '\\modUser');
}
if (!\class_exists('\\modContext') && \class_exists('\\MODX\\Revolution\\modContext')) {
    \class_alias('\\MODX\\Revolution\\modContext', '\\modContext');
}
if (!\class_exists('\\modNamespace') && \class_exists('\\MODX\\Revolution\\modNamespace')) {
    \class_alias('\\MODX\\Revolution\\modNamespace', '\\modNamespace');
}
if (!\class_exists('\\modExtensionPackage') && \class_exists('\\MODX\\Revolution\\modExtensionPackage')) {
    \class_alias('\\MODX\\Revolution\\modExtensionPackage', '\\modExtensionPackage');
}
if (!\class_exists('\\modSystemSetting') && \class_exists('\\MODX\\Revolution\\modSystemSetting')) {
    \class_alias('\\MODX\\Revolution\\modSystemSetting', '\\modSystemSetting');
}
if (!\class_exists('\\modMenu') && \class_exists('\\MODX\\Revolution\\modMenu')) {
    \class_alias('\\MODX\\Revolution\\modMenu', '\\modMenu');
}
if (!\class_exists('\\modResource') && \class_exists('\\MODX\\Revolution\\modResource')) {
    \class_alias('\\MODX\\Revolution\\modResource', '\\modResource');
}
if (!\class_exists('\\modSystemEvent') && \class_exists('\\MODX\\Revolution\\modSystemEvent')) {
    \class_alias('\\MODX\\Revolution\\modSystemEvent', '\\modSystemEvent');
}
if (!\class_exists('\\modPlugin') && \class_exists('\\MODX\\Revolution\\modPlugin')) {
    \class_alias('\\MODX\\Revolution\\modPlugin', '\\modPlugin');
}

if (!\class_exists('\\xPDO') && \class_exists('\\xPDO\\xPDO')) {
    \class_alias('\\xPDO\\xPDO', '\\xPDO');
}
if (!\class_exists('\\xPDOCriteria') && \class_exists('\\xPDO\\Om\\xPDOCriteria')) {
    \class_alias('\\xPDO\\Om\\xPDOCriteria', '\\xPDOCriteria');
}
if (!\class_exists('\\xPDOQuery') && \class_exists('\\xPDO\\Om\\xPDOQuery')) {
    \class_alias('\\xPDO\\Om\\xPDOQuery', '\\xPDOQuery');
}
if (!\class_exists('\\xPDOQueryCondition') && \class_exists('\\xPDO\\Om\\xPDOQueryCondition')) {
    \class_alias('\\xPDO\\Om\\xPDOQueryCondition', '\\xPDOQueryCondition');
}


if (!\class_exists('\\xPDOConnection') && \class_exists('\\xPDO\\xPDOConnection')) {
    \class_alias('\\xPDO\\xPDOConnection', '\\xPDOConnection');
}
if (!\class_exists('\\xPDOSimpleObject') && \class_exists('\\xPDO\\Om\\xPDOSimpleObject')) {
    \class_alias('\\xPDO\\Om\\xPDOSimpleObject', '\\xPDOSimpleObject');
}
if (!\class_exists('\\xPDOObject') && \class_exists('\\xPDO\\Om\\xPDOObject')) {
    \class_alias('\\xPDO\\Om\\xPDOObject', '\\xPDOObject');
}

if (!\class_exists('\\xPDOManager') && \class_exists('\\xPDO\\Om\\xPDOManager')) {
    \class_alias('\\xPDO\\Om\\xPDOManager', '\\xPDOManager');
}
if (!\class_exists('\\xPDOGenerator') && \class_exists('\\xPDO\\Om\\xPDOGenerator')) {
    \class_alias('\\xPDO\\Om\\xPDOGenerator', '\\xPDOGenerator');
}
if (!\class_exists('\\xPDOCacheManager') && \class_exists('\\xPDO\\Cache\\xPDOCacheManager')) {
    \class_alias('\\xPDO\\Cache\\xPDOCacheManager', '\\xPDOCacheManager');
}

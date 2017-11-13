<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'] = [];
        }

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Locking\LockFactory::class] = [
            'className' => \Higidi\Lock\LockFactory::class,
        ];

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'])) {
            $getLockBuilder = function () {
                /** @var \Higidi\Lock\Builder\LockBuilder $lockBuilder */
                $lockBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    \Higidi\Lock\Builder\LockBuilder::class
                );

                return $lockBuilder;
            };
            $lockBuilder = [
                \NinjaMutex\Lock\DirectoryLock::class => function (array $configuration) use ($getLockBuilder) {
                    return $getLockBuilder()->buildDirectoryLock($configuration);
                },
                \NinjaMutex\Lock\FlockLock::class => function (array $configuration) use ($getLockBuilder) {
                    return $getLockBuilder()->buildFlockLock($configuration);
                },
                \NinjaMutex\Lock\MySqlLock::class => function (array $configuration) use ($getLockBuilder) {
                    return $getLockBuilder()->buildMySqlLock($configuration);
                },
                \NinjaMutex\Lock\PhpRedisLock::class => function (array $configuration) use ($getLockBuilder) {
                    return $getLockBuilder()->buildPhpRedisLock($configuration);
                },
                NinjaMutex\Lock\PredisRedisLock::class => function (array $configuration) use ($getLockBuilder) {
                    return $getLockBuilder()->buildPredisRedisLock($configuration);
                },
            ];
            if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationBuilder'])
                && is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationBuilder'])) {
                \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
                    $lockBuilder,
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationBuilder'],
                    true,
                    false
                );
            }
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationBuilder'] = $lockBuilder;

            $lockConfiguration = [
                \NinjaMutex\Lock\DirectoryLock::class => [
                    'path' => PATH_site . 'typo3temp/locks/',
                ],
                \NinjaMutex\Lock\FlockLock::class => [
                    'path' => PATH_site . 'typo3temp/locks/',
                ],
            ];
            if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationConfiguration'])
                && is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationConfiguration'])) {
                \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
                    $lockConfiguration,
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationConfiguration'],
                    true,
                    false
                );
            }
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']['lockImplementationConfiguration'] = $lockConfiguration;
        }
    }
);

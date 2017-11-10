<?php

namespace Higidi\Lock\Builder;

use Higidi\Lock\Builder\Exception\InvalidConfigurationException;
use Higidi\Lock\Builder\Exception\LockCreateException;
use NinjaMutex\Lock;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Lock implementation builder class.
 */
class LockBuilder implements SingletonInterface
{
    /**
     * @param array $configuration
     *
     * @return Lock\DirectoryLock
     * @throws InvalidConfigurationException
     * @throws LockCreateException
     */
    public function buildDirectoryLock(array $configuration)
    {
        $lock = $this->buildPathLock(Lock\DirectoryLock::class, $configuration);

        return $lock;
    }

    /**
     * @param array $configuration
     *
     * @return Lock\FlockLock
     * @throws InvalidConfigurationException
     * @throws LockCreateException
     */
    public function buildFlockLock(array $configuration)
    {
        $lock = $this->buildPathLock(Lock\FlockLock::class, $configuration);

        return $lock;
    }

    /**
     * @param string $className
     * @param array $configuration
     *
     * @return Lock\LockInterface
     * @throws InvalidConfigurationException
     * @throws LockCreateException
     */
    protected function buildPathLock($className, array $configuration)
    {
        $path = isset($configuration['path']) ? (string)$configuration['path'] : null;
        if (empty($path)) {
            throw new InvalidConfigurationException(
                $configuration,
                'Missing or empty lock directory path',
                1510318044
            );
        }
        if (! $this->preparePath($path)) {
            throw new LockCreateException(sprintf('Path %s is not usable (not readable/writeable)', $path), 1510318759);
        }
        $lock = GeneralUtility::makeInstance($className, $path);

        return $lock;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function preparePath($path)
    {
        if (null === $path || ! is_dir($path) && ! GeneralUtility::mkdir($path)) {
            return false;
        }

        return true;
    }
}

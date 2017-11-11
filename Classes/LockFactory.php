<?php

namespace Higidi\Lock;

use Higidi\Lock\Builder\LockBuilder;
use Higidi\Lock\Configuration\Configuration;
use TYPO3\CMS\Core\Locking\Exception\LockCreateException;
use TYPO3\CMS\Core\Locking\LockFactory as CoreLockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory class creates locks.
 */
class LockFactory extends CoreLockFactory
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Configuration|null $configuration The configuration to use
     */
    public function __construct(Configuration $configuration = null, LockBuilder $lockBuilder = null)
    {
        if (null === $configuration) {
            $configuration = GeneralUtility::makeInstance(Configuration::class);
        }
        $this->configuration = $configuration;
    }

    /**
     * Get the configuration.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get best matching locking method
     *
     * @param string $id ID to identify this lock in the system
     * @param int $capabilities LockingStrategyInterface::LOCK_CAPABILITY_* elements combined with bit-wise OR
     *
     * @return LockingStrategyInterface Class name for a locking method
     * @throws LockCreateException if no locker could be created with the requested capabilities
     */
    public function createLocker($id, $capabilities = LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE)
    {
        if (! $this->configuration->isActive()) {
            return parent::createLocker($id, $capabilities);
        }

        $className = $this->configuration->getStrategy();
        $locker = GeneralUtility::makeInstance($className, $id);

        return $locker;
    }
}

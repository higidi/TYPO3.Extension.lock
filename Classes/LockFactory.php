<?php

namespace Higidi\Lock;

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
     * @var LockBuilder
     */
    protected $lockBuilder;

    /**
     * @param Configuration|null $configuration The configuration to use
     * @param LockBuilder|null $lockBuilder The lock implementation builder to use
     */
    public function __construct(Configuration $configuration = null, LockBuilder $lockBuilder = null)
    {
        if (null === $configuration) {
            $configuration = GeneralUtility::makeInstance(Configuration::class);
        }
        if (null === $lockBuilder) {
            $lockBuilder = GeneralUtility::makeInstance(LockBuilder::class);
        }
        $this->configuration = $configuration;
        $this->lockBuilder = $lockBuilder;
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
     * Get lock builder.
     *
     * @return LockBuilder
     */
    public function getLockBuilder()
    {
        return $this->lockBuilder;
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

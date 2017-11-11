<?php

namespace Higidi\Lock;

use Higidi\Lock\Builder\LockBuilder;
use Higidi\Lock\Configuration\Configuration;
use NinjaMutex\Lock\LockInterface;
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
     * @var LockInterface
     */
    protected $lockImplementation;

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

        try {
            $strategyClassName = $this->configuration->getStrategy();
            if ($this->configuration->isMutexStrategy()) {
                $mutexClassName = $this->configuration->getMutex();
                $lockImplementation = $this->getLockImplemenation();
                $mutex = GeneralUtility::makeInstance($mutexClassName, $id, $lockImplementation);
                $locker = GeneralUtility::makeInstance($strategyClassName, $mutex);
            } else {
                $locker = GeneralUtility::makeInstance($strategyClassName, $id);
            }
        } catch (\Exception $e) {
            if ($e instanceof LockCreateException) {
                throw $e;
            }
            throw new LockCreateException('Could not create locker', 1510432762, $e);
        }

        return $locker;
    }

    /**
     * @return LockInterface
     * @throws LockCreateException
     */
    protected function getLockImplemenation()
    {
        if (! $this->lockImplementation) {
            $lockImplementationClassName = $this->configuration->getLockImplementation();
            if (! $lockImplementationClassName) {
                throw new LockCreateException('No lock implementation configured', 1510439606);
            }
            $lockImplementationConfiguration = $this->configuration->getLockImplementationConfiguration(
                $lockImplementationClassName
            );
            $lockImplementationBuilder = $this->configuration->getLockImplementationBuilder(
                $lockImplementationClassName
            );
            if (! is_callable($lockImplementationBuilder)) {
                throw new LockCreateException(
                    sprintf('No callable builder found for lock implementation %s', $lockImplementationClassName),
                    1510432679
                );
            }
            $lockImplementation = call_user_func($lockImplementationBuilder, $lockImplementationConfiguration);
            if (! $lockImplementation instanceof $lockImplementationClassName) {
                throw new LockCreateException(
                    sprintf(
                        'Expected lock implementation instance of %s. Got %s',
                        $lockImplementationClassName,
                        is_object($lockImplementation) ? get_class($lockImplementation) : gettype($lockImplementation)
                    ),
                    1510439540
                );
            }
            $this->lockImplementation = $lockImplementation;
        }

        return $this->lockImplementation;
    }
}

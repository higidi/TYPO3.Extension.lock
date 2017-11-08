<?php

namespace Higidi\Lock\Locking\Strategy;

use Higidi\Lock\Exception\InvalidArgumentException;
use NinjaMutex\Mutex;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireWouldBlockException;
use TYPO3\CMS\Core\Locking\Exception\LockCreateException;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;

/**
 * TYPO3 lock strategy adapter for the arvenil/ninja-mutex library.
 */
class NinjaMutexAdapterStrategy implements LockingStrategyInterface
{
    /**
     * @var Mutex
     */
    protected $mutex;

    /**
     * @param Mutex $mutex ID to identify this lock in the system
     *
     * @throws LockCreateException if the lock could not be created
     */
    public function __construct($mutex)
    {
        if (! $mutex instanceof Mutex) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    Mutex::class,
                    is_object($mutex) ? get_class($mutex) : gettype($mutex)
                ),
                1510158724
            );
        }
        $this->mutex = $mutex;
    }

    /**
     * @return int LOCK_CAPABILITY_* elements combined with bit-wise OR
     */
    public static function getCapabilities()
    {
        return static::LOCK_CAPABILITY_EXCLUSIVE | static::LOCK_CAPABILITY_NOBLOCK;
    }

    /**
     * @return int Returns a priority for the method. 0 to 100, 100 is highest
     */
    public static function getPriority()
    {
        return 0;
    }

    /**
     * Try to acquire a lock
     *
     * @param int $mode LOCK_CAPABILITY_EXCLUSIVE or LOCK_CAPABILITY_SHARED
     *
     * @return bool Returns TRUE if the lock was acquired successfully
     * @throws LockAcquireException if the lock could not be acquired
     * @throws LockAcquireWouldBlockException if the acquire would have blocked and NOBLOCK was set
     */
    public function acquire($mode = self::LOCK_CAPABILITY_EXCLUSIVE)
    {
        $timeout = null;
        if ($mode & static::LOCK_CAPABILITY_NOBLOCK) {
            $timeout = 0;
        }

        return $this->mutex->acquireLock($timeout);
    }

    /**
     * Release the lock
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function release()
    {
        return $this->mutex->releaseLock();
    }

    /**
     * Destroys the resource associated with the lock
     *
     * @return void
     */
    public function destroy()
    {
        $this->release();
    }

    /**
     * Get status of this lock
     *
     * @return bool Returns TRUE if lock is acquired by this locker, FALSE otherwise
     */
    public function isAcquired()
    {
        return $this->mutex->isAcquired();
    }
}

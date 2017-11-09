<?php

namespace Higidi\Lock\Configuration;

use Higidi\Lock\Configuration\Exception\InvalidMutexException;
use Higidi\Lock\Configuration\Exception\InvalidStrategyException;
use Higidi\Lock\Strategy\MutexAdapterStrategy;
use NinjaMutex\Mutex;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Locking\SimpleLockStrategy;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Holds/manage locking configuration.
 */
class Configuration implements SingletonInterface
{
    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var string
     */
    protected $strategy = SimpleLockStrategy::class;

    /**
     * @var string
     */
    protected $mutex = Mutex::class;

    /**
     * @param array|null $configuration
     */
    public function __construct(array $configuration = null)
    {
        if (null === $configuration) {
            $globalConfiguration = isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'])
                ? $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking']
                : null;
            if (is_array($globalConfiguration)) {
                if (! isset($globalConfiguration['active'])) {
                    $globalConfiguration['active'] = true;
                }
                $configuration = $globalConfiguration;
            }
        }
        if (is_array($configuration)) {
            foreach ($configuration as $name => $value) {
                $method = 'set' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = (bool)$active;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param string $strategy Strategy to use (class name, must implement the
     *     \TYPO3\CMS\Core\Locking\LockingStrategyInterface)
     *
     * @return $this
     * @throws InvalidStrategyException
     */
    protected function setStrategy($strategy = null)
    {
        if (! is_a($strategy, LockingStrategyInterface::class, true)) {
            throw new InvalidStrategyException(
                sprintf(
                    '%s only accepts null or classes implementing the %s',
                    __METHOD__,
                    LockingStrategyInterface::class
                ),
                1510177679
            );
        }
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMutexStrategy()
    {
        return $this->isActive() && is_a($this->getStrategy(), MutexAdapterStrategy::class, true);
    }

    /**
     * @return string
     */
    public function getMutex()
    {
        return $this->mutex;
    }

    /**
     * @param string $mutex
     *
     * @return $this
     * @throws InvalidMutexException
     */
    protected function setMutex($mutex)
    {
        if (! is_a($mutex, Mutex::class, true)) {
            throw new InvalidMutexException(
                sprintf('%s only accepts classes extending the %s class', __METHOD__, Mutex::class),
                1510177680
            );
        }
        $this->mutex = $mutex;

        return $this;
    }
}

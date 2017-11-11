<?php

namespace Higidi\Lock\Configuration;

use Higidi\Lock\Configuration\Exception\InvalidLockImplementationException;
use Higidi\Lock\Configuration\Exception\InvalidMutexException;
use Higidi\Lock\Configuration\Exception\InvalidStrategyException;
use Higidi\Lock\Configuration\Exception\NotCallableLockImplemenationBuilderException;
use Higidi\Lock\Strategy\MutexAdapterStrategy;
use NinjaMutex\Lock\LockInterface;
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
     * @var null|string
     */
    protected $lockImplementation;

    /**
     * @var array
     */
    protected $lockImplementationBuilder = [];

    /**
     * @var array
     */
    protected $lockImplementationConfiguration = [];

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

    /**
     * @return string|null
     */
    public function getLockImplementation()
    {
        return $this->lockImplementation;
    }

    /**
     * @param string $lockImplementation
     *
     * @return $this
     * @throws InvalidLockImplementationException
     */
    protected function setLockImplementation($lockImplementation)
    {
        if (! $this->isValidLockImplementation($lockImplementation)) {
            throw new InvalidLockImplementationException(
                sprintf('%s only accepts classes extending the %s class', __METHOD__, LockInterface::class),
                1510268834
            );
        }
        $this->lockImplementation = $lockImplementation;

        return $this;
    }

    /**
     * @param null|string $lockImplemenation
     *
     * @return array|callable|null
     */
    public function getLockImplementationBuilder($lockImplemenation = null)
    {
        if (empty($lockImplemenation)) {
            return $this->lockImplementationBuilder;
        }

        $lockImplemenationBuilder = isset($this->lockImplementationBuilder[$lockImplemenation])
            ? $this->lockImplementationBuilder[$lockImplemenation]
            : null;

        return $lockImplemenationBuilder;
    }

    /**
     * @param array $builder
     *
     * @return $this
     * @throws InvalidLockImplementationException
     * @throws NotCallableLockImplemenationBuilderException
     */
    protected function setLockImplementationBuilder($builder)
    {
        if (! is_array($builder)) {
            $builder = (array)$builder;
        }

        foreach ($builder as $lockImplemenation => $lockImplemenationBuilder) {
            if (! $this->isValidLockImplementation($lockImplemenation)) {
                throw new InvalidLockImplementationException('', 1510436775);
            }
            if (! is_callable($lockImplemenationBuilder)) {
                throw new NotCallableLockImplemenationBuilderException(
                    'Lock implemenation builder needs to be callable',
                    1510438594
                );
            }
            $this->lockImplementationBuilder[$lockImplemenation] = $lockImplemenationBuilder;
        }

        return $this;
    }

    /**
     * @param null|string $lockImplementation
     *
     * @return array
     */
    public function getLockImplementationConfiguration($lockImplementation = null)
    {
        if (empty($lockImplementation)) {
            return $this->lockImplementationConfiguration;
        }

        $lockImplementationConfiguration = isset($this->lockImplementationConfiguration[$lockImplementation])
            ? $this->lockImplementationConfiguration[$lockImplementation]
            : [];

        return $lockImplementationConfiguration;
    }

    /**
     * @param array $configuration
     *
     * @return $this
     * @throws InvalidLockImplementationException
     */
    protected function setLockImplementationConfiguration($configuration)
    {
        if (! is_array($configuration)) {
            $configuration = (array)$configuration;
        }

        foreach ($configuration as $lockImplementation => $lockImplementationConfiguration) {
            if (! $this->isValidLockImplementation($lockImplementation)) {
                throw new InvalidLockImplementationException('', 1510436776);
            }
            $this->lockImplementationConfiguration[$lockImplementation] = (array)$lockImplementationConfiguration;
        }

        return $this;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    protected function isValidLockImplementation($className)
    {
        return is_a($className, LockInterface::class, true);
    }
}

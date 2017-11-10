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
     * @param array $configuration
     *
     * @return Lock\MySqlLock
     * @throws InvalidConfigurationException
     */
    public function buildMySqlLock(array $configuration)
    {
        $host = isset($configuration['host']) ? (string)$configuration['host'] : null;
        $port = isset($configuration['port']) ? (int)$configuration['port'] : 3306;
        $userName = isset($configuration['username']) ? (string)$configuration['username'] : '';
        $password = isset($configuration['password']) ? (string)$configuration['password'] : '';
        $className = isset($configuration['className']) ? (string)$configuration['className'] : '\PDO';

        if (empty($host)) {
            throw new InvalidConfigurationException($configuration, 'Missing or empty mysql host', 1510327148);
        }
        if (! is_a($className, \PDO::class, true)) {
            throw new InvalidConfigurationException($configuration, 'Classname must an instance of \PDO', 1510327151);
        }

        $lock = GeneralUtility::makeInstance(Lock\MySqlLock::class, $userName, $password, $host, $port, $className);

        return $lock;
    }

    /**
     * @param array $configuration
     *
     * @return Lock\PhpRedisLock
     * @throws InvalidConfigurationException
     * @throws LockCreateException
     */
    public function buildPhpRedisLock(array $configuration)
    {
        if (! extension_loaded('redis')) {
            throw new LockCreateException('PHP extension "redis" not loaded', 1510321193);
        }
        $host = isset($configuration['host']) ? (string)$configuration['host'] : null;
        if (empty($host)) {
            throw new InvalidConfigurationException($configuration, 'Missing or empty redis host', 1510321408);
        }
        $port = isset($configuration['port']) ? (int)$configuration['port'] : 6379;
        $timeout = isset($configuration['timeout']) ? (float)$configuration['timeout'] : 0.0;
        $password = isset($configuration['password']) ? (string)$configuration['password'] : null;
        $database = isset($configuration['database']) ? (int)$configuration['database'] : 0;

        /** @var \Redis $redis */
        $errorReporting = error_reporting();
        error_reporting(0);
        $redis = GeneralUtility::makeInstance(\Redis::class);
        if (! $redis->connect($host, $port, $timeout)) {
            throw new LockCreateException(
                sprintf('Could not connect to redis host "%s" on port %d', $host, $port),
                1510321516
            );
        };
        if (! empty($password)) {
            if (! $redis->auth($password)) {
                throw new LockCreateException('Authentication with redis host failed', 1510321753);
            };
        }
        if (! $redis->select($database)) {
            throw new LockCreateException('Switch redis database to %d failed', 1510321791);
        };
        error_reporting($errorReporting);

        $lock = GeneralUtility::makeInstance(Lock\PhpRedisLock::class, $redis);

        return $lock;
    }

    /**
     * @param array $configuration
     *
     * @return Lock\PredisRedisLock
     * @throws InvalidConfigurationException
     */
    public function buildPredisRedisLock(array $configuration)
    {
        $parameters = isset($configuration['parameters']) ? (array)$configuration['parameters'] : null;
        $options = isset($configuration['options']) ? (array)$configuration['options'] : [];
        if (empty($parameters)) {
            throw new InvalidConfigurationException($configuration, 'Missing or empty predis parameters', 1510325325);
        }

        $client = GeneralUtility::makeInstance(\Predis\Client::class, $parameters, $options);
        $lock = GeneralUtility::makeInstance(Lock\PredisRedisLock::class, $client);

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

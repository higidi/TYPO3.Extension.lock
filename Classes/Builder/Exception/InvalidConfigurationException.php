<?php

namespace Higidi\Lock\Builder\Exception;

/**
 * Thrown on any invalid configuration within the builder.
 */
class InvalidConfigurationException extends \Exception
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @link http://php.net/manual/en/exception.construct.php
     *
     * @param array $configuration The invalid configuration array
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param \Exception $previous [optional] The previous throwable used for the exception chaining.
     *
     * @since 5.1.0
     */
    public function __construct(array $configuration, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->configuration = $configuration;
    }

    /**
     * Returns the configuration.
     *
     * @return array The configuration array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}

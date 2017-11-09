<?php

namespace Higidi\Lock\Tests\Unit\Configuration\Exception;

use Higidi\Lock\Configuration\Exception\InvalidConfigurationException;
use Higidi\Lock\Configuration\Exception\InvalidStrategyException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Configuration\Exception\InvalidStrategyException".
 *
 * @covers \Higidi\Lock\Configuration\Exception\InvalidStrategyException
 */
class InvalidStrategyExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheInvalidConfigurationException()
    {
        $sut = new InvalidStrategyException();

        $this->assertInstanceOf(InvalidConfigurationException::class, $sut);
    }
}

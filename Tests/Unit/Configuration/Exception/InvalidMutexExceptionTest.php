<?php

namespace Higidi\Lock\Tests\Unit\Configuration\Exception;

use Higidi\Lock\Configuration\Exception\InvalidConfigurationException;
use Higidi\Lock\Configuration\Exception\InvalidMutexException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Configuration\Exception\InvalidMutexException".
 *
 * @covers \Higidi\Lock\Configuration\Exception\InvalidMutexException
 */
class InvalidMutexExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheInvalidConfigurationException()
    {
        $sut = new InvalidMutexException();

        $this->assertInstanceOf(InvalidConfigurationException::class, $sut);
    }
}

<?php

namespace Higidi\Lock\Tests\Unit\Configuration\Exception;

use Higidi\Lock\Configuration\Exception\InvalidConfigurationException;
use Higidi\Lock\Configuration\Exception\InvalidLockImplementationException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Configuration\Exception\InvalidLockImplementationException".
 *
 * @covers \Higidi\Lock\Configuration\Exception\InvalidLockImplementationException
 */
class InvalidLockImplementationExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheInvalidConfigurationException()
    {
        $sut = new InvalidLockImplementationException();

        $this->assertInstanceOf(InvalidConfigurationException::class, $sut);
    }
}

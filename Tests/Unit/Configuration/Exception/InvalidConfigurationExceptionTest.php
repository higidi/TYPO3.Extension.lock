<?php

namespace Higidi\Lock\Tests\Unit\Configuration\Exception;

use Higidi\Lock\Configuration\Exception\InvalidConfigurationException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Configuration\Exception\InvalidConfigurationException".
 *
 * @covers \Higidi\Lock\Configuration\Exception\InvalidConfigurationException
 */
class InvalidConfigurationExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheGenericException()
    {
        $sut = new InvalidConfigurationException();

        $this->assertInstanceOf(\Exception::class, $sut);
    }
}

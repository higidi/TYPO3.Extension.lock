<?php

namespace Higidi\Lock\Tests\Unit\Configuration\Exception;

use Higidi\Lock\Configuration\Exception\InvalidConfigurationException;
use Higidi\Lock\Configuration\Exception\NotCallableLockImplemenationBuilderException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Configuration\Exception\NotCallableLockImplemenationBuilderException".
 *
 * @covers \Higidi\Lock\Configuration\Exception\NotCallableLockImplemenationBuilderException
 */
class NotCallableLockImplemenationBuilderExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheInvalidConfigurationException()
    {
        $sut = new NotCallableLockImplemenationBuilderException();

        $this->assertInstanceOf(InvalidConfigurationException::class, $sut);
    }
}

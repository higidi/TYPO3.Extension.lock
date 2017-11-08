<?php

namespace Higidi\Lock\Tests\Unit\Exception;

use Higidi\Lock\Exception\InvalidArgumentException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Exception\InvalidArgumentException".
 *
 * @covers \Higidi\Lock\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheGenericInvalidArgumentException()
    {
        $sut = new InvalidArgumentException();

        $this->assertInstanceOf(\InvalidArgumentException::class, $sut);
    }
}

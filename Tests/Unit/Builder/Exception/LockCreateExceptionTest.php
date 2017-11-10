<?php

namespace Higidi\Lock\Tests\Unit\Builder\Exception;

use Higidi\Lock\Builder\Exception\LockCreateException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Builder\Exception\LockCreateException".
 *
 * @covers \Higidi\Lock\Builder\Exception\LockCreateException
 */
class LockCreateExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheGenericException()
    {
        $sut = new LockCreateException();

        $this->assertInstanceOf(\Exception::class, $sut);
    }
}

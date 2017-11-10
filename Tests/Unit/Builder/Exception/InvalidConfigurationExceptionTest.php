<?php

namespace Higidi\Lock\Tests\Unit\Builder\Exception;

use Higidi\Lock\Builder\Exception\InvalidConfigurationException;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\Builder\Exception\InvalidConfigurationException".
 *
 * @covers \Higidi\Lock\Builder\Exception\InvalidConfigurationException
 */
class InvalidConfigurationExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itExtendsTheGenericException()
    {
        $sut = new InvalidConfigurationException([]);

        $this->assertInstanceOf(\Exception::class, $sut);
    }

    /**
     * @test
     */
    public function itHoldsTheConfiguration()
    {
        $configuration = [
            'bla' => 'foo',
        ];

        $sut = new InvalidConfigurationException($configuration);

        $this->assertSame($configuration, $sut->getConfiguration());
    }
}

<?php

namespace Higidi\Lock\Tests\Unit;

use Higidi\Lock\LockBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Test case for "\Higidi\Lock\LockBuilder".
 *
 * @covers \Higidi\Lock\LockBuilder
 */
class LockBuilderTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itIsASingleton()
    {
        $sut = new LockBuilder();

        $this->assertInstanceOf(SingletonInterface::class, $sut);
    }
}

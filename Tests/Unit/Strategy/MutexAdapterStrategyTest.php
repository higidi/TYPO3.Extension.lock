<?php

namespace Higidi\Lock\Tests\Unit\Strategy;

use Higidi\Lock\Strategy\MutexAdapterStrategy;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use NinjaMutex\Mutex;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;

/**
 * Test case for "\Higidi\Lock\Strategy\MutexAdapterStrategy".
 *
 * @covers \Higidi\Lock\Strategy\MutexAdapterStrategy
 */
class MutexAdapterStrategyTest extends UnitTestCase
{
    /**
     * @test
     */
    public function itImplementsTheLockStrategyInterface()
    {
        $mutex = $this->prophesize(Mutex::class);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $this->assertInstanceOf(LockingStrategyInterface::class, $sut);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Exception\InvalidArgumentException
     * @expectedExceptionCode 1510158724
     */
    public function itThrowsAnInvalidArgumentExceptionIfMutexIsNotPassed()
    {
        new MutexAdapterStrategy('blafoo');
    }

    /**
     * @test
     */
    public function itReturnsAnExclusiveCapability()
    {
        $capabilities = MutexAdapterStrategy::getCapabilities();

        $capability = LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE;
        $this->assertSame($capability, $capabilities & $capability);
    }

    /**
     * @test
     */
    public function itReturnsANoBlockCapability()
    {
        $capabilities = MutexAdapterStrategy::getCapabilities();

        $capability = LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK;
        $this->assertSame($capability, $capabilities & $capability);
    }

    /**
     * @test
     */
    public function itReturnsAPriority()
    {
        $priority = MutexAdapterStrategy::getPriority();

        $this->assertSame(0, $priority);
    }

    /**
     * @test
     */
    public function itAcquireALock()
    {
        $mutex = $this->prophesize(Mutex::class);
        $mutex
            ->acquireLock(null)
            ->shouldBeCalled()
            ->willReturn(true);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $this->assertTrue($sut->acquire());
    }

    /**
     * @test
     */
    public function itAcquireANonBlockingLock()
    {
        $mode = LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK;
        $mutex = $this->prophesize(Mutex::class);
        $mutex
            ->acquireLock(0)
            ->shouldBeCalled()
            ->willReturn(true);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $this->assertTrue($sut->acquire($mode));
    }

    /**
     * @test
     */
    public function itReleasesALock()
    {
        $mutex = $this->prophesize(Mutex::class);
        $mutex
            ->releaseLock()
            ->shouldBeCalled()
            ->willReturn(true);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $this->assertTrue($sut->release());
    }

    /**
     * @test
     */
    public function itDestroysALock()
    {
        $mutex = $this->prophesize(Mutex::class);
        $mutex
            ->releaseLock()
            ->shouldBeCalled()
            ->willReturn(true);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $sut->destroy();
    }

    /**
     * @test
     */
    public function itReturnsTheCurrentLockStatus()
    {
        $mutex = $this->prophesize(Mutex::class);
        $mutex
            ->isAcquired()
            ->shouldBeCalled()
            ->willReturn(true);
        $sut = new MutexAdapterStrategy($mutex->reveal());

        $this->assertTrue($sut->isAcquired());
    }
}

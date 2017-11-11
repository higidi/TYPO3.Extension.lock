<?php

namespace Higidi\Lock\Tests\Unit;

use Higidi\Lock\Configuration\Configuration;
use Higidi\Lock\Builder\LockBuilder;
use Higidi\Lock\LockFactory;
use Higidi\Lock\Strategy\MutexAdapterStrategy;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Locking as CoreLocking;
use TYPO3\CMS\Core\Locking\LockFactory as CoreLockFactory;

/**
 * Test case for "\Higidi\Lock\LockFactory".
 *
 * @covers \Higidi\Lock\LockFactory
 */
class LockFactoryTest extends UnitTestCase
{
    /**
     * @return array
     */
    public function coreLockingStrategyDataProvider()
    {
        return [
            'simple_strategy' => [CoreLocking\SimpleLockStrategy::class],
            'file_lock_strategy' => [CoreLocking\FileLockStrategy::class],
            'semaphore_strategy' => [CoreLocking\SemaphoreLockStrategy::class],
        ];
    }

    /**
     * @test
     */
    public function itExtendsTheCoreLockFactory()
    {
        $sut = new LockFactory();

        $this->assertInstanceOf(CoreLockFactory::class, $sut);
    }

    /**
     * @test
     */
    public function itCreatesADefaultConfigurationIfNotPassed()
    {
        $sut = new LockFactory();

        $this->assertInstanceOf(Configuration::class, $sut->getConfiguration());
    }

    /**
     * @test
     */
    public function itHoldsAConfiguration()
    {
        $configuration = $this->prophesize(Configuration::class);

        $sut = new LockFactory($configuration->reveal());

        $this->assertSame($configuration->reveal(), $sut->getConfiguration());
    }

    /**
     * @test
     */
    public function itOnlyOperatesIfIsActive()
    {
        $configuration = $this->prophesize(Configuration::class);
        $configuration
            ->isActive()
            ->willReturn(false);
        $sut = new LockFactory($configuration->reveal());

        $locker = $sut->createLocker('blafoo');

        $this->assertNotInstanceOf(MutexAdapterStrategy::class, $locker);
    }

    /**
     * @test
     * @dataProvider coreLockingStrategyDataProvider
     *
     * @param string $strategy
     */
    public function itCreatesSpecificLockerIfConfigurationIsSet($strategy)
    {
        $configuration = $this->prophesize(Configuration::class);
        $configuration
            ->isActive()
            ->willReturn(true);
        $configuration
            ->getStrategy()
            ->willReturn($strategy);
        $configuration
            ->isMutexStrategy()
            ->willReturn(false);

        $sut = new LockFactory($configuration->reveal());

        $locker = $sut->createLocker('blafoo');

        $this->assertInstanceOf($strategy, $locker);
    }
}

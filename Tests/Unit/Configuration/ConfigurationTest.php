<?php

namespace Higidi\Lock\Tests\Unit\Configuration;

use Higidi\Lock\Configuration\Configuration;
use Higidi\Lock\Strategy\MutexAdapterStrategy;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use NinjaMutex\Mutex;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Locking\SimpleLockStrategy;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Test case for "\Higidi\Lock\Configuration\Configuration".
 *
 * @covers \Higidi\Lock\Configuration\Configuration
 */
class ConfigurationTest extends UnitTestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = true;

    /**
     * @test
     */
    public function itIsASingleton()
    {
        $sut = new Configuration();

        $this->assertInstanceOf(SingletonInterface::class, $sut);
    }

    /**
     * @test
     */
    public function itCanBeEnabledByInitializingGlobalsConfigurationArray()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [];

        $sut = new Configuration();

        $this->assertTrue($sut->isActive());
    }

    /**
     * @return array
     */
    public function activeStatusDataProvider()
    {
        return [
            'disabled' => [false],
            'enabled' => [true],
        ];
    }

    /**
     * @test
     * @dataProvider activeStatusDataProvider
     *
     * @param bool $active
     */
    public function itIsPossibleToActivateOrDeactivateViaGlobalsConfigurationArray($active)
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'active' => $active,
        ];

        $sut = new Configuration();

        $this->assertSame($active, $sut->isActive());
    }

    /**
     * @test
     * @dataProvider activeStatusDataProvider
     *
     * @param bool $active
     */
    public function itIsPossibleToActivateOrDeactivateViaConfigurationArray($active)
    {
        $configuration = [
            'active' => $active,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($active, $sut->isActive());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetStrategyViaGlobalsConfigurationArray()
    {
        $strategy = $this->prophesize(LockingStrategyInterface::class)->reveal();
        $className = get_class($strategy);

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'strategy' => $className,
        ];

        $sut = new Configuration();

        $this->assertSame($className, $sut->getStrategy());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetStrategyViaConfigurationArray()
    {
        $strategy = $this->prophesize(LockingStrategyInterface::class)->reveal();
        $className = get_class($strategy);

        $configuration = [
            'active' => true,
            'strategy' => $className,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($className, $sut->getStrategy());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetMutexViaGlobalsConfigurationArray()
    {
        $mutex = $this->prophesize(Mutex::class)->reveal();
        $className = get_class($mutex);

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'mutex' => $className,
        ];

        $sut = new Configuration();

        $this->assertSame($className, $sut->getMutex());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetMutexViaConfigurationArray()
    {
        $mutex = $this->prophesize(Mutex::class)->reveal();
        $className = get_class($mutex);

        $configuration = [
            'active' => true,
            'mutex' => $className,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($className, $sut->getMutex());
    }

    /**
     * @test
     */
    public function itIsDisabledByDefault()
    {
        $sut = new Configuration();
        $active = $sut->isActive();

        $this->assertFalse($active);
    }

    /**
     * @test
     */
    public function itCanBeEnabled()
    {
        $sut = new Configuration();
        $this->assertFalse($sut->isActive());

        $sut->setActive(true);
        $this->assertTrue($sut->isActive());
    }

    /**
     * @test
     */
    public function itHasAsDefaultStrategyTheSimpleLockingStrategy()
    {
        $sut = new Configuration();
        $className = $sut->getStrategy();

        $this->assertSame(SimpleLockStrategy::class, $className);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\InvalidStrategyException
     * @expectedExceptionCode 1510177679
     */
    public function itThrowsAnInvalidStrategyExceptionIfStrategyDoNotImplementTheLockingStrategyInterface()
    {
        $configuration = [
            'strategy' => \stdClass::class,
        ];

        new Configuration($configuration);
    }

    /**
     * @test
     */
    public function itDetectsTheMutexAdapterStrategy()
    {
        $strategy = $this->prophesize(MutexAdapterStrategy::class)->reveal();
        $className = get_class($strategy);
        $configuration = [
            'active' => true,
            'strategy' => $className,
        ];

        $sut = new Configuration($configuration);

        $this->assertTrue($sut->isMutexStrategy());
    }

    /**
     * @test
     */
    public function itHasADefaultMutex()
    {
        $sut = new Configuration();
        $className = $sut->getMutex();

        $this->assertSame(Mutex::class, $className);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\InvalidMutexException
     * @expectedExceptionCode 1510177680
     */
    public function itThrowsAnInvalidMutexExceptionIfMutexDoNotExtendTheBaseMutex()
    {
        $configuration = [
            'mutex' => \stdClass::class,
        ];

        new Configuration($configuration);
    }
}

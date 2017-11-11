<?php

namespace Higidi\Lock\Tests\Unit\Configuration;

use Higidi\Lock\Configuration\Configuration;
use Higidi\Lock\Strategy\MutexAdapterStrategy;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use NinjaMutex\Lock\LockInterface;
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
    public function itIsPossibleToSetLockImplementationViaGlobalsConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'lockImplementation' => $className,
        ];

        $sut = new Configuration();

        $this->assertSame($className, $sut->getLockImplementation());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetLockImplementationViaConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);

        $configuration = [
            'active' => true,
            'lockImplementation' => $className,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($className, $sut->getLockImplementation());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetLockImplementationBuilderViaGlobalsConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $lockImplementationBuilder = [
            $className => function (array $configuration) {
                return $configuration;
            },
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'lockImplementationBuilder' => $lockImplementationBuilder,
        ];

        $sut = new Configuration();

        $this->assertSame($lockImplementationBuilder, $sut->getLockImplementationBuilder());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetLockImplementationBuilderViaConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $lockImplementationBuilder = [
            $className => function (array $configuration) {
                return $configuration;
            },
        ];
        $configuration = [
            'lockImplementationBuilder' => $lockImplementationBuilder,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($lockImplementationBuilder, $sut->getLockImplementationBuilder());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetLockImplementationConfigurationViaGlobalsConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $lockImplementationConfiguration = [
            $className => [],
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'] = [
            'lockImplementationConfiguration' => $lockImplementationConfiguration,
        ];

        $sut = new Configuration();

        $this->assertSame($lockImplementationConfiguration, $sut->getLockImplementationConfiguration());
    }

    /**
     * @test
     */
    public function itIsPossibleToSetLockImplementationConfigurationViaConfigurationArray()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $lockImplementationConfiguration = [
            $className => [],
        ];
        $configuration = [
            'lockImplementationConfiguration' => $lockImplementationConfiguration,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($lockImplementationConfiguration, $sut->getLockImplementationConfiguration());
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

    /**
     * @test
     */
    public function itHasNullAsDefaultLockImplementation()
    {
        $sut = new Configuration();

        $this->assertNull($sut->getLockImplementation());
    }

    /**
     * @test
     */
    public function itHasAnArrayAsDefaultLockImplemenationBuilder()
    {
        $sut = new Configuration();

        $this->assertSame([], $sut->getLockImplementationBuilder());
    }

    /**
     * @test
     */
    public function itReturnsALockImplemenationBuilderByLockImplemenation()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $callable = function (array $configuration) {
            return $configuration;
        };
        $lockImplementationBuilder = [
            $className => $callable,
        ];
        $configuration = [
            'lockImplementationBuilder' => $lockImplementationBuilder,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($callable, $sut->getLockImplementationBuilder($className));
    }

    /**
     * @test
     */
    public function itReturnsPerDefaultNullAsLockImplemenationBuilderByLockImplemenationIfNotExists()
    {
        $sut = new Configuration();

        $this->assertNull($sut->getLockImplementationBuilder(\stdClass::class));
    }

    /**
     * @test
     */
    public function itHasAnArrayAsDefaultLockImplemenationConfiguration()
    {
        $sut = new Configuration();

        $this->assertSame([], $sut->getLockImplementationConfiguration());
    }

    /**
     * @test
     */
    public function itReturnsALockImplemenationConfigurationByLockImplemenation()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $config = ['bla' => 'foo'];
        $lockImplementationConfiguration = [
            $className => $config,
        ];
        $configuration = [
            'lockImplementationConfiguration' => $lockImplementationConfiguration,
        ];

        $sut = new Configuration($configuration);

        $this->assertSame($config, $sut->getLockImplementationConfiguration($className));
    }

    /**
     * @test
     */
    public function itReturnsPerDefaultAnArrayAsLockImplemenationConfigurationByLockImplemenationIfNotExists()
    {
        $sut = new Configuration();

        $this->assertSame([], $sut->getLockImplementationConfiguration(\stdClass::class));
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\InvalidLockImplementationException
     * @expectedExceptionCode 1510268834
     */
    public function itThrowsAnInvalidLockImplementationExceptionIfLockImplementionDoNotImplementTheLockingInterface()
    {
        $configuration = [
            'lockImplementation' => \stdClass::class,
        ];

        new Configuration($configuration);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\InvalidLockImplementationException
     * @expectedExceptionCode 1510436775
     */
    public function itThrowsAnInvalidLockImplemenationExceptionIfLockImplemenationForBuilderIsNotValid()
    {
        $configuration = [
            'lockImplementationBuilder' => [
                \stdClass::class => function (array $configuration) {
                    return $configuration;
                },
            ],
        ];

        new Configuration($configuration);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\NotCallableLockImplemenationBuilderException
     * @expectedExceptionCode 1510438594
     */
    public function itThrowsANotCallableLockImplementationBuilderExceptionIfBuilderIsNotCallable()
    {
        $lockImplementation = $this->prophesize(LockInterface::class)->reveal();
        $className = get_class($lockImplementation);
        $configuration = [
            'lockImplementationBuilder' => [
                $className => 'not_callable',
            ],
        ];

        new Configuration($configuration);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Configuration\Exception\InvalidLockImplementationException
     * @expectedExceptionCode 1510436776
     */
    public function itThrowsAnInvalidLockImplemenationExceptionIfLockImplemenationForConfigurationIsNotValid()
    {
        $configuration = [
            'lockImplementationConfiguration' => [
                \stdClass::class => [],
            ],
        ];

        new Configuration($configuration);
    }
}

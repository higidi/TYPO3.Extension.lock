<?php

namespace Higidi\Lock\Tests\Unit;

use Higidi\Lock\Builder\LockBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use NinjaMutex\Lock;

/**
 * Test case for "\Higidi\Lock\LockBuilder".
 *
 * @covers \Higidi\Lock\LockBuilder
 */
class LockBuilderTest extends UnitTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->clearLockPath();
    }

    /**
     * Unset all additional properties of test classes to help PHP
     * garbage collection. This reduces memory footprint with lots
     * of tests.
     *
     * If owerwriting tearDown() in test classes, please call
     * parent::tearDown() at the end. Unsetting of own properties
     * is not needed this way.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->clearLockPath();
    }

    /**
     * @return void
     */
    protected function clearLockPath()
    {
        $path = $this->getLockPath();
        if (is_dir($path)) {
            GeneralUtility::rmdir($path, true);
        }
    }

    /**
     * @return string
     */
    protected function getLockPath()
    {
        return PATH_site . '/typo3temp/locks/';
    }

    /**
     * @test
     */
    public function itIsASingleton()
    {
        $sut = new LockBuilder();

        $this->assertInstanceOf(SingletonInterface::class, $sut);
    }

    /**
     * @test
     */
    public function itBuildsADirectoryLock()
    {
        $configurtion = [
            'path' => $this->getLockPath(),
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildDirectoryLock($configurtion);

        $this->assertInstanceOf(Lock\DirectoryLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510318044
     */
    public function itThrowsAnInvalidConfigurationExceptionOnBuildADirectoryLockWithMissingPathConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildDirectoryLock([]);
    }
}

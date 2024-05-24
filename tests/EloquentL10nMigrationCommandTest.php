<?php

namespace Safadi\Tests;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use PHPUnit\Framework\TestCase;
use Safadi\Eloquent\L10n\Console\L10nTableCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class EloquentL10nMigrationCommandTest extends TestCase
{

    protected $expectedMigrationFilename;

    protected $app;

    public function setUp(): void
    {
        $this->app = TestApplication::getInstance();
        $this->app->boot();
    }

    public function tearDown(): void
    {
        if (is_file($this->expectedMigrationFilename)) {
            unlink($this->expectedMigrationFilename);
        }
        Mockery::close();
    }

    public function testL10nTableCommandWithModel()
    {
        $command = new L10nTableCommand(new Filesystem);
        $command->rootNamespace(__NAMESPACE__)->setLaravel($this->app);

        $this->expectedMigrationFilename = __DIR__ . '/migrations/' . date('Y_m_d_His') . '_create_posts_l10n_table.php';
        $this->runCommand($command, [
            'model' => 'TestPost'
        ]);

        $this->assertFileExists($this->expectedMigrationFilename);
    }

    public function testL10nTableCommandWithoutModel()
    {
        $command = new L10nTableCommand(new Filesystem);
        $command->rootNamespace(__NAMESPACE__)->setLaravel($this->app);

        $this->expectedMigrationFilename = __DIR__ . '/migrations/' . date('Y_m_d_His') . '_create_tests_l10n_table.php';
        $this->runCommand($command, [
            'model' => 'tests'
        ]);

        $this->assertFileExists($this->expectedMigrationFilename);
    }

    public function testL10nTableCommandMigrationExists()
    {
        $command = new L10nTableCommand(new Filesystem);
        $command->rootNamespace(__NAMESPACE__)->setLaravel($this->app);

        $this->expectedMigrationFilename = __DIR__ . '/migrations/' . date('Y_m_d_His') . '_create_tests_l10n_table.php';

        $result = $this->runCommand($command, [
            'model' => 'tests'
        ]);
        $this->assertFileExists($this->expectedMigrationFilename);
        $this->assertEquals(0, $result);

        $result = $this->runCommand($command, [
            'model' => 'tests'
        ]);
        $this->assertEquals(1, $result);
        
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput);
    }
}

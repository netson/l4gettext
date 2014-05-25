<?php

use Netson\L4gettext\Commands\InstallCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class InstallCommandTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders ()
    {
        return array(
            'Netson\L4gettext\L4gettextServiceProvider',
        );

    }

    protected function getPackageAliases ()
    {
        return array(
            'L4gettext' => 'Netson\L4gettext\Facades\L4gettext',
        );

    }

    /*
    public function testInstallCommandSuccessfull ()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(6)->andReturn(true);
        File::shouldReceive('isWritable')->times(3)->andReturn(true);

        File::shouldReceive('delete')->once()->andReturn(true);
        File::shouldReceive('get')->once()->andReturn(true);
        File::shouldReceive('put')->once()->andReturn(true);

        $commandTester = new CommandTester(new InstallCommand);
        $commandTester->execute(array());

        $this->assertStringEndsWith("- be sure to verify your default settings in the (published) config.php file\n", $commandTester->getDisplay());

    }
     * DISABLED for now; the laravel test framework still fails when calling a different command in a test using the ->call() method
     */

    public function testInstallCommandThrowsExceptionWhenConfigFilesNotWritable ()
    {
        $this->setExpectedException('Netson\L4gettext\ConfigFilesNotWritableException');

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(6)->andReturn(true);
        File::shouldReceive('isWritable')->once()->andReturn(false);

        $commandTester = new CommandTester(new InstallCommand);
        $commandTester->execute(array());

    }

    public function testInstallCommandThrowsExceptionOnWhenOnWindows ()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $this->setExpectedException('Netson\L4gettext\FetchCommandNotSupportedOnWindowsException');
            $commandTester = new CommandTester(new InstallCommand);
            $commandTester->execute(array());
        }
        else
            $this->assertTrue(true);

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
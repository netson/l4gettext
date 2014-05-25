<?php

use Netson\L4gettext\Commands\FetchCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class FetchCommandTest extends Orchestra\Testbench\TestCase {

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

    public function testFetchCommandSuccessfull ()
    {
        $expected = "en_us.utf8";

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(2)->andReturn(true);
        File::shouldReceive('isWritable')->twice()->andReturn(true);

        File::shouldReceive('delete')->twice()->andReturn(true);
        File::shouldReceive('copy')->twice()->andReturn(true);
        File::shouldReceive('append')->twice()->andReturn(true);

        $proc = m::mock("Symfony\Component\Process\Process");
        $procBuilder = m::mock("Symfony\Component\Process\ProcessBuilder");

        $proc->shouldReceive('run')->once();
        $proc->shouldReceive('isSuccessful')->once()->andReturn(true);
        $proc->shouldReceive('getOutput')->once()->andReturn('en_US.utf8');
        $proc->shouldReceive('stop')->once();

        $procBuilder->shouldReceive('setPrefix')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('setArguments')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('getProcess')->once()->andReturn($proc);

        $commandTester = new CommandTester(new FetchCommand($procBuilder));
        $proc->__destruct(); // invoke the stop() call
        $commandTester->execute(array());

        $this->assertStringEndsWith("] encodings\n", $commandTester->getDisplay());

    }

    public function testFetchCommandThrowsExceptionWhenConfigFilesNotWritable ()
    {
        $this->setExpectedException('Netson\L4gettext\ConfigFilesNotWritableException');

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(2)->andReturn(true);
        File::shouldReceive('isWritable')->once()->andReturn(false);

        $commandTester = new CommandTester(new FetchCommand);
        $commandTester->execute(array());

    }

    public function testFetchCommandThrowsExceptionWhenInvalidLocaleDetected ()
    {
        $this->setExpectedException('Netson\L4gettext\InvalidItemCountException');

        $expected = "en_us.utf8.error";

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(2)->andReturn(true);
        File::shouldReceive('isWritable')->twice()->andReturn(true);

        $proc = m::mock("Symfony\Component\Process\Process");
        $procBuilder = m::mock("Symfony\Component\Process\ProcessBuilder");

        $proc->shouldReceive('run')->once();
        $proc->shouldReceive('isSuccessful')->once()->andReturn(true);
        $proc->shouldReceive('getOutput')->once()->andReturn($expected);
        $proc->shouldReceive('stop')->once();

        $procBuilder->shouldReceive('setPrefix')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('setArguments')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('getProcess')->once()->andReturn($proc);

        $commandTester = new CommandTester(new FetchCommand($procBuilder));
        $proc->__destruct(); // invoke the stop() call
        $commandTester->execute(array());

    }

    public function testFetchCommandThrowsExceptionWhenProcessFailed ()
    {
        $this->setExpectedException('Netson\L4gettext\CannotFetchInstalledLocalesException');

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(2)->andReturn(true);
        File::shouldReceive('isWritable')->twice()->andReturn(true);

        $proc = m::mock("Symfony\Component\Process\Process");
        $procBuilder = m::mock("Symfony\Component\Process\ProcessBuilder");

        $proc->shouldReceive('run')->once();
        $proc->shouldReceive('isSuccessful')->once()->andReturn(false);
        $proc->shouldReceive('stop')->once();

        $procBuilder->shouldReceive('setPrefix')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('setArguments')->once()->andReturn(m::self());
        $procBuilder->shouldReceive('getProcess')->once()->andReturn($proc);

        $commandTester = new CommandTester(new FetchCommand($procBuilder));
        $proc->__destruct(); // invoke the stop() call
        $commandTester->execute(array());

    }

    public function testFetchCommandThrowsExceptionOnWhenOnWindows ()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $this->setExpectedException('Netson\L4gettext\FetchCommandNotSupportedOnWindowsException');
            $commandTester = new CommandTester(new FetchCommand);
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
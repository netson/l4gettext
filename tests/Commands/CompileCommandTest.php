<?php

use Netson\L4gettext\Commands\CompileCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class CompileCommandTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders ()
    {
        return array(
            'Netson\L4gettext\L4gettextServiceProvider',
        );

    }

    protected function getPackageAliases ()
    {
        return array(
            'L4gettext'     => 'Netson\L4gettext\Facades\L4gettext',
            'BladeCompiler' => 'Netson\L4gettext\Facades\BladeCompiler',
        );

    }

    public function testCompileCommandSuccessful ()
    {
        BladeCompiler::shouldReceive('setCachePath')->once()
                ->shouldReceive('compile')->once(); // same amount of times as files provided by glob()

        File::shouldReceive("isDirectory")->once()->andReturn(true)
                ->shouldReceive('glob')->once()->andReturn(array("test.php"));

        $commandTester = new CommandTester(new CompileCommand);
        $commandTester->execute(array());

        $expected = "blade templates found and successfully compiled\n";
        $this->assertStringEndsWith($expected, $commandTester->getDisplay());

    }

    public function testCompileCommandThrowsExceptionWhenNoFilesToCompile ()
    {
        $this->setExpectedException('Netson\L4gettext\NoTemplatesToCompileException');

        BladeCompiler::shouldReceive('setCachePath')->once();

        File::shouldReceive("isDirectory")->once()->andReturn(true)
                ->shouldReceive('glob')->once()->andReturn(array());

        $commandTester = new CommandTester(new CompileCommand);
        $commandTester->execute(array());

    }

    public function testCompileCommandSuccessWhenOutputDirDoesNotExist ()
    {
        BladeCompiler::shouldReceive('setCachePath')->once()
                ->shouldReceive('compile')->once(); // same amount of times as files provided by glob()

        File::shouldReceive("isDirectory")->once()->andReturn(false)
                ->shouldReceive('makeDirectory')->once()->andReturn(true)
                ->shouldReceive('glob')->once()->andReturn(array("test.php"));

        $commandTester = new CommandTester(new CompileCommand);
        $commandTester->execute(array());

        $expected = "blade templates found and successfully compiled\n";
        $this->assertStringEndsWith($expected, $commandTester->getDisplay());

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
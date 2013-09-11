<?php

use Netson\L4gettext\Commands\ExtractCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ExtractCommandTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders ()
    {
        return array(
            'Netson\L4shell\L4shellServiceProvider',
            'Netson\L4gettext\L4gettextServiceProvider',
        );

    }

    protected function getPackageAliases ()
    {
        return array(
            'L4shell'   => 'Netson\L4shell\Facades\Command',
            'L4gettext' => 'Netson\L4gettext\Facades\L4gettext',
        );

    }

    public function testExtractCommandSuccessfull ()
    {
        File::shouldReceive('glob')->once()->andReturn(array("test.php"));

        L4shell::shouldReceive('setCommand')->once()->andReturn(m::self());
        L4shell::shouldReceive('setExecutablePath')->once()->andReturn(m::self());
        L4shell::shouldReceive('setArguments')->once()->andReturn(m::self());
        L4shell::shouldReceive('sendToDevNull')->once()->andReturn(m::self());
        L4shell::shouldReceive('setAllowedCharacters')->once()->with(array("*"))->andReturn(m::self());
        L4shell::shouldReceive('execute')->once()->andReturn("my.hostname.ext");

        $commandTester = new CommandTester(new ExtractCommand);
        $commandTester->execute(array());
        $this->assertStringEndsWith("xgettext successfully executed\n", $commandTester->getDisplay());

    }

    public function testExtractCommandThrowsExceptionWhenNoFilesFound ()
    {
        $this->setExpectedException('Netson\L4gettext\NoFilesToExtractFromException');
        File::shouldReceive('glob')->once()->andReturn(array());

        $commandTester = new CommandTester(new ExtractCommand);
        $commandTester->execute(array());

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
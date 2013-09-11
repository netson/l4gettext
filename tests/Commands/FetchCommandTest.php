<?php

use Netson\L4gettext\Commands\FetchCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class FetchCommandTest extends Orchestra\Testbench\TestCase {

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

    public function testFetchCommandSuccessfull ()
    {
        $expected = "en_us.utf8";

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(4)->andReturn(true);
        File::shouldReceive('isWritable')->twice()->andReturn(true);

        L4shell::shouldReceive('setCommand')->once()->andReturn(m::self());
        L4shell::shouldReceive('setArguments')->once()->andReturn(m::self());
        L4shell::shouldReceive('execute')->once()->andReturn($expected);

        File::shouldReceive('delete')->twice()->andReturn(true);
        File::shouldReceive('copy')->twice()->andReturn(true);
        File::shouldReceive('append')->twice()->andReturn(true);

        $commandTester = new CommandTester(new FetchCommand);
        $commandTester->execute(array());

        $this->assertStringEndsWith("be sure to verify your default settings in the config.php file\n", $commandTester->getDisplay());

    }

    public function testFetchCommandThrowsExceptionWhenConfigFilesNotWritable ()
    {
        $this->setExpectedException('Netson\L4gettext\ConfigFilesNotWritable');

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(4)->andReturn(true);
        File::shouldReceive('isWritable')->once()->andReturn(false);

        $commandTester = new CommandTester(new FetchCommand);
        $commandTester->execute(array());

    }

    public function testFetchCommandThrowsExceptionWhenInvalidLocaleDetected ()
    {
        $this->setExpectedException('Netson\L4gettext\InvalidItemCountException');

        $expected = "en_us.utf8.error";

        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isFile')->times(4)->andReturn(true);
        File::shouldReceive('isWritable')->twice()->andReturn(true);

        L4shell::shouldReceive('setCommand')->once()->andReturn(m::self());
        L4shell::shouldReceive('setArguments')->once()->andReturn(m::self());
        L4shell::shouldReceive('execute')->once()->andReturn($expected);

        $commandTester = new CommandTester(new FetchCommand);
        $commandTester->execute(array());

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
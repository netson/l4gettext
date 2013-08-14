<?php

use Netson\L4gettext\Commands\ListCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ListCommandTest extends Orchestra\Testbench\TestCase {

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

    public function testListCommandSuccessfull ()
    {
        $expected = "en_US.utf8";
        L4gettext::shouldReceive('getLocaleAndEncoding')->once()->andReturn($expected);

        $commandTester = new CommandTester(new ListCommand);
        $commandTester->execute(array());
        $this->assertStringEndsWith("current default setting: [$expected]\n", $commandTester->getDisplay());

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
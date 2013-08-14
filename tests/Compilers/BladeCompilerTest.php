<?php

use Mockery as m;

class BladeCompilerTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders ()
    {
        return array(
            'Netson\L4gettext\L4gettextServiceProvider',
        );

    }

    protected function getPackageAliases ()
    {
        return array(
            'BladeCompiler' => 'Netson\L4gettext\Facades\BladeCompiler',
        );

    }

    public function testCheckCompiledPathOutput ()
    {
        $testPath = "test";
        $control = BladeCompiler::getCachePath() . DIRECTORY_SEPARATOR . md5($testPath) . ".php";
        $this->assertSame($control, BladeCompiler::getCompiledPath($testPath));

    }

    public function testGetCachePathReturnsProperString ()
    {
        $testPath = "/test";
        BladeCompiler::setCachePath($testPath);
        $this->assertEquals($testPath, BladeCompiler::getCachePath());

    }

    public function testCompileGetsFileContents ()
    {
        $testPath = "/test";
        BladeCompiler::setCachePath($testPath);

        $contents = 'file contents';
        $mock = File::shouldReceive('get')->once()->andReturn($contents)
                ->shouldReceive('put')->once()
                ->getMock();

        $expected = BladeCompiler::getCompiledPath($testPath);
        BladeCompiler::setFiles($mock);

        $this->assertEquals($expected, BladeCompiler::compile($testPath));

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
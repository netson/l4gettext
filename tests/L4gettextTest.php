<?php

use Mockery as m;

class L4gettextTest extends Orchestra\Testbench\TestCase {

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

    public function testHasLocale ()
    {
        $this->assertTrue(L4gettext::hasLocale());

    }

    public function testHasEncoding ()
    {
        $this->assertTrue(L4gettext::hasEncoding());

    }

    public function testSetLocaleReturnsInstanceOfL4gettext ()
    {
        $default = Config::get('l4gettext::config.default_locale');
        $this->assertInstanceOf('Netson\L4gettext\L4gettext', L4gettext::setLocale($default));

    }

    public function testSessionHasLocale ()
    {
        $this->assertTrue(Session::has('l4gettext_locale'));

    }

    public function testSessionHasDefaultLocale ()
    {
        $default = Config::get('l4gettext::config.default_locale');
        $this->assertSame(Session::get('l4gettext_locale'), $default);

    }

    public function testSetEncodingReturnsInstanceOfL4gettext ()
    {
        $default = Config::get('l4gettext::config.default_encoding');
        $this->assertInstanceOf('Netson\L4gettext\L4gettext', L4gettext::setEncoding($default));

    }

    public function testSessionHasEncoding ()
    {
        $this->assertTrue(Session::has('l4gettext_encoding'));

    }

    public function testSessionHasDefaultEncoding ()
    {
        $default = Config::get('l4gettext::config.default_encoding');
        $this->assertSame(Session::get('l4gettext_encoding'), $default);

    }

    public function testGetLocaleAndEncodingReturnsString ()
    {
        $this->assertSame('string', gettype(L4gettext::getLocaleAndEncoding()));

    }

    public function testInvalidSetLocaleReturnsException ()
    {
        $this->setExpectedException('Netson\L4gettext\InvalidLocaleException');
        L4gettext::setLocale("invalid");

    }

    public function testInvalidSetEncodingReturnsException ()
    {
        $this->setExpectedException('Netson\L4gettext\InvalidEncodingException');
        L4gettext::setEncoding("invalid");

    }

    public function testGetLocaleReturnsDefaultLocale ()
    {
        $default = Config::get('l4gettext::config.default_locale');
        $this->assertSame(L4gettext::getLocale(), $default);

    }

    public function testGetEncodingReturnsDefaultEncoding ()
    {
        $default = Config::get('l4gettext::config.default_encoding');
        $this->assertSame(L4gettext::getEncoding(), $default);

    }

    public function testHasLocaleReturnBoolean ()
    {
        $this->assertInternalType('boolean', L4gettext::hasLocale());

    }

    public function testHasEncodingReturnBoolean ()
    {
        $this->assertInternalType('boolean', L4gettext::hasEncoding());

    }

    public function testCreateFolderFolderAlreadyExists ()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(true)
                ->shouldReceive('makeDirectory')->never();

        new \Netson\L4gettext\L4gettext();

    }

    public function testCreateFolderFolderCreatedSuccessfully ()
    {
        File::shouldReceive('isDirectory')->twice()->andReturn(false) // init by constructor
                ->shouldReceive('makeDirectory')->once()->andReturn(true);

        new \Netson\L4gettext\L4gettext();

    }

    public function testCreateFolderFolderNotCreatedSuccessfully ()
    {
        $this->setExpectedException('Netson\L4gettext\LocaleFolderCreationException');
        File::shouldReceive('isDirectory')->twice()->andReturn(false) // init by constructor
                ->shouldReceive('makeDirectory')->once()->andReturn(false);

        new \Netson\L4gettext\L4gettext();

    }

    public function testToStringEqualsGetLocaleAndEncoding ()
    {
        $this->assertSame(L4gettext::__toString(), L4gettext::getLocaleAndEncoding());

    }

    public function testSetTextDomainShouldCallIsDirectory ()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(true);

        new \Netson\L4gettext\L4gettext();

    }

    public function testSetTextDomainShouldReturnObject ()
    {
        File::shouldReceive('isDirectory')->twice()->andReturn(true) // init by constructor
                ->shouldReceive('makeDirectory')->never();

        $this->assertInstanceOf("Netson\L4gettext\L4gettext", L4gettext::createFolder('/test'));

    }

    public function testSetTextDomainReturnsL4gettextInstance ()
    {
        $textdomain = Config::get('l4gettext::config.textdomain');
        $path_to_mo = Config::get('l4gettext::config.path_to_mo');
        $this->assertInstanceOf('Netson\L4gettext\L4gettext', L4gettext::setTextDomain($textdomain, $path_to_mo));

    }

    public function tearDown ()
    {
        m::close();

    }

}

?>
<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 24-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace PGMB\Vendor\Html2Text;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $html = 'Foo';
        $options = array('do_links' => 'none');
        $html2text = new Html2Text($html, $options);
        $this->assertEquals($html, $html2text->getText());

        $html2text = new Html2Text($html);
        $this->assertEquals($html, $html2text->getText());
    }

    public function testLegacyConstructor()
    {
        $html = 'Foo';
        $options = array('do_links' => 'none');

        $html2text = new Html2Text($html, false, $options);
        $this->assertEquals($html, $html2text->getText());
    }

    public function testLegacyConstructorThrowsExceptionWhenFromFileIsTrue()
    {
        $html = 'Foo';
        $options = array('do_links' => 'none');

        $this->setExpectedException('InvalidArgumentException');
        $html2text = new Html2Text($html, true, $options);
    }
}

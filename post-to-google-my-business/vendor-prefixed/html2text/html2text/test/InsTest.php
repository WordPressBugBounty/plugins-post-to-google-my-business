<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 24-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace PGMB\Vendor\Html2Text;

class InsTest extends \PHPUnit_Framework_TestCase
{
    public function testIns()
    {
        $html = 'This is <ins>inserted</ins>';
        $expected = 'This is _inserted_';

        $html2text = new Html2Text($html);
        $this->assertEquals($expected, $html2text->getText());
    }
}

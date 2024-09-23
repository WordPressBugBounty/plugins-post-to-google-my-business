<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 24-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace PGMB\Vendor\Html2Text;

class DelTest extends \PHPUnit_Framework_TestCase
{
    public function testDel()
    {
        $html = 'My <del>Résumé</del> Curriculum Vitæ';
        $expected = 'My R̶é̶s̶u̶m̶é̶ Curriculum Vitæ';

        $html2text = new Html2Text($html);
        $this->assertEquals($expected, $html2text->getText());
    }
}

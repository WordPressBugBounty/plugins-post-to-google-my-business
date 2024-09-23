<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 24-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace PGMB\Vendor\Html2Text;

class StrToUpperTest extends \PHPUnit_Framework_TestCase
{
    public function testToUpper()
    {
    	$html =<<<EOT
<h1>Will be UTF-8 (äöüèéилčλ) uppercased</h1>
<p>Will remain lowercased</p>
EOT;
        $expected =<<<EOT
WILL BE UTF-8 (ÄÖÜÈÉИЛČΛ) UPPERCASED

Will remain lowercased

EOT;

        $html2text = new Html2Text($html);
        $output = $html2text->getText();

        $this->assertEquals($expected, $output);
    }
}

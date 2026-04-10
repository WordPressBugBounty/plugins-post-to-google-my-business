<?php

namespace PGMB\Vendor\Html2Text;

use PHPUnit\Framework\TestCase;

class StrToUpperTest extends TestCase
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

<?php

namespace PGMB\Vendor\Cron\Tests;

use PGMB\Vendor\Cron\FieldFactory;
use PHPUnit_Framework_TestCase;
/**
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class FieldFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \PGMB\Vendor\Cron\FieldFactory::getField
     */
    public function testRetrievesFieldInstances()
    {
        $mappings = array(0 => 'PGMB\Vendor\Cron\MinutesField', 1 => 'PGMB\Vendor\Cron\HoursField', 2 => 'PGMB\Vendor\Cron\DayOfMonthField', 3 => 'PGMB\Vendor\Cron\MonthField', 4 => 'PGMB\Vendor\Cron\DayOfWeekField', 5 => 'PGMB\Vendor\Cron\YearField');
        $f = new FieldFactory();
        foreach ($mappings as $position => $class) {
            $this->assertEquals($class, get_class($f->getField($position)));
        }
    }
    /**
     * @covers \PGMB\Vendor\Cron\FieldFactory::getField
     * @expectedException InvalidArgumentException
     */
    public function testValidatesFieldPosition()
    {
        $f = new FieldFactory();
        $f->getField(-1);
    }
}
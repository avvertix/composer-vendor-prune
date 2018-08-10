<?php

namespace Avvertix\VendorPrune\Tests;

use Avvertix\VendorPrune\Support\Formatter;
use PHPUnit\Framework\TestCase as BaseTestCase;

class FormatterTest extends TestCase
{

    public function test_size_formatter_print_bytes()
    {
        $formatted = Formatter::size(10);

        $this->assertEquals('10.00B', $formatted);
    }

    public function test_size_formatter_print_kilobytes()
    {
        $formatted = Formatter::size(1024);

        $this->assertEquals('1.00KB', $formatted);
    }

    public function test_size_formatter_print_megabytes()
    {
        $formatted = Formatter::size(1048576);

        $this->assertEquals('1.00MB', $formatted);
    }

    public function test_size_formatter_print_gigabytes()
    {
        $formatted = Formatter::size(1073741824);

        $this->assertEquals('1.00GB', $formatted);
    }

    public function test_size_formatter_respect_decimals()
    {
        $formatted = Formatter::size(1073741824, 1);

        $this->assertEquals('1.0GB', $formatted);
    }

    public function test_gain_formatter()
    {
        $formatted = Formatter::gain(100, 50);

        $this->assertEquals('100.00B => 50.00B 50.00%', $formatted);
    }
    
    public function test_gain_formatter_respect_decimals()
    {
        $formatted = Formatter::gain(100, 50, 0);

        $this->assertEquals('100B => 50B 50%', $formatted);
    }

}

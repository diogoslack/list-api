<?php
namespace App\Tests\Helper;

use PHPUnit\Framework\TestCase;
use App\Helper\StorageHelper;

final class StorageHelperTest extends TestCase
{
    /**
     * @dataProvider getTeraToGigaProvider
     */
    public function testTeraToGiga(string $value, int $expected): void
    {
        $this->assertSame($expected, StorageHelper::teraToGiga($value));
    }

    /**
     * @dataProvider getValuesFromStringProvider
     */
    public function testGetValuesFromString(string $value, array $expected): void
    {
        $this->assertSame($expected, StorageHelper::getValuesFromString($value));
    }    

    public function getTeraToGigaProvider(): array
    {
        return [
            ['128GB', 128],
            ['64gb', 64],
            ['1TB', 1024],
            ['2tb', 2048],
            ['16', 16],
            ['invalid', 0],
            ['', 0],
        ];
    }

    public function getValuesFromStringProvider(): array
    {
        $expected = [
            ['name' => "4x480GBSSD", 'type' => "SSD", 'size' => 1920],
            ['name' => "2x500GBSATA2", 'type' => "SATA", 'size' => 1000],
            ['name' => "2x1TBSATA2", 'type' => "SATA", 'size' => 2048],
            ['name' => "24x1TBSATA2", 'type' => "SATA", 'size' => 24576],
            ['name' => "8x2TBSATA2", 'type' => "SATA", 'size' => 16384],
            ['name' => "8x120GBSSD", 'type' => "SSD", 'size' => 960],
            ['name' => "4x1TBSATA2", 'type' => "SATA", 'size' => 4096],
            ['name' => "8x2TBSATA2", 'type' => "SATA", 'size' => 16384],
            ['name' => "8x300GBSAS", 'type' => "SAS", 'size' => 2400],
            [],
            [],
            [],
        ];
        return [
            ["4x480GBSSD", $expected[0]],
            ["2x500GBSATA2", $expected[1]],
            ["2x1TBSATA2", $expected[2]],
            ["24x1TBSATA2", $expected[3]],
            ["8x2TBSATA2", $expected[4]],
            ["8x120GBSSD", $expected[5]],
            ["4x1TBSATA2", $expected[6]],
            ["8x2TBSATA2", $expected[7]],
            ["8x300GBSAS", $expected[8]],
            ["invalid", $expected[9]],
            ["", $expected[10]],
            ["300GB", $expected[11]],
        ];
    }
}
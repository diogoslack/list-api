<?php
namespace App\Tests\Helper;

use PHPUnit\Framework\TestCase;
use App\Helper\PaginationHelper;

final class PaginationHelperTest extends TestCase
{
    /**
     * @dataProvider getPaginationProvider
     */
    public function testGetPagination(int $current, int $limitPerPage, int $totalRows, array $expected): void
    {
        $this->assertSame($expected, PaginationHelper::getPagination($current, $limitPerPage, $totalRows));
    }

    /**
     * @dataProvider getOffsetProvider
     */
    public function testGetOffset(int $current, int $limitPerPage, int $expected): void
    {
        $this->assertSame($expected, PaginationHelper::getOffset($current, $limitPerPage));
    }

    public function getPaginationProvider(): array
    {
        $expectedWithAllData = [
            "currentPage" => 2,
            "nextPage" => 3,
            "previousPage" => 1,
            "totalPages" => 10
        ];
        $expectedAPageOnly = [
            "currentPage" => 1,
            "nextPage" => null,
            "previousPage" => null,
            "totalPages" => 1
        ];
        $expectedLastPage = [
            "currentPage" => 10,
            "nextPage" => null,
            "previousPage" => 9,
            "totalPages" => 10
        ];
        return [
            [2, 10, 100, $expectedWithAllData],
            [1, 5, 4, $expectedAPageOnly],
            [10, 10, 100, $expectedLastPage],
        ];
    }

    public function getOffsetProvider(): array
    {
        $expectedForFirstPage = 0;
        $expectedForSecondPage = 10;
        
        return [
            [1, 10, $expectedForFirstPage],
            [2, 10, $expectedForSecondPage]
        ];
    }
}
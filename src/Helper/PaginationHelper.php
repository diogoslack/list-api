<?php
namespace App\Helper;

class PaginationHelper {

    public static function getPagination(int $current, int $limitPerPage, int $totalRows): array
    {
        $totalPages = (int) ceil($totalRows / $limitPerPage);
        $next = $current + 1;
        $previous = $current - 1;
        return [
          'currentPage' => $current,
          'nextPage' => $next > $totalPages ? null : $next,
          'previousPage' => $previous < 1 ? null : $previous,
          'totalPages' => $totalPages
        ];
    }

    public static function getOffset(int $current, int $limitPerPage): int
    {
        return ($current - 1) * $limitPerPage;
    }
}
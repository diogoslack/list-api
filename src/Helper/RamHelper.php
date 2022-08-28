<?php
namespace App\Helper;

class RamHelper {

  const TB_IN_GB = 1024;

  public static function teraToGiga(string $value): int 
  {
    return substr($value, -2) == 'TB' ?  (int) $value * 1024 : (int) $value;
  }

  public static function getValuesFromString(string $value): array
    {
        preg_match('/((\d.*)(GB|TB)(.*))/', $value, $matches);
        if ($matches && $matches[2] && $matches[3] && $matches[4]) {
            $size = (int) $matches[2];
            $size = $matches[3] == 'TB' ? $size * self::TB_IN_GB : $size;
            return ['name' => $value, 'type' => $matches[4], 'size' => $size];
        }
        return [];
    }
}
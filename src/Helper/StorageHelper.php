<?php
namespace App\Helper;

class StorageHelper {

  const TB_IN_GB = 1024;

  public static function teraToGiga(string $value): int 
  {
    return substr($value, -2) == 'TB' ?  (int) $value * 1024 : (int) $value;
  }

  public static function getValuesFromString(string $value): array
    {
        preg_match('/((\d.*)x(.*))(GB|TB)((SATA|SAS|SSD).*)/', $value, $matches);        
        if ($matches && $matches[2] && $matches[3] && $matches[4] && $matches[6]) {
            $quantity = (int) $matches[2];
            $size = (int) $matches[3];
            $size = $matches[4] == 'TB' ? $size * self::TB_IN_GB : $size;
            $total = $size * $quantity;
            $type = $matches[6];            
            return ['name' => $value, 'type' => $type, 'size' => $total];
        }
        return [];
    }

    public static function getStorageQuantity(string $value): int
    {
        preg_match('/((\d.*)x(.*))(GB|TB)/', $value, $matches);
        if ($matches && $matches[2]) {
            return (int) $matches[2];
        }
        return 0;
    }
}
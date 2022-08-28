<?php
namespace App\Builder\Product;

use App\Entity\Product;
use App\Builder\Product\ProductBuilder;

class Director
{
    public function build(
        ProductBuilder $builder,
        \DateTime $version,
        string $priceData,
        string $modelData,
        string $locationData,
        string $ramData,
        ?string $storageData
    ): Product {
        $builder->createProduct($version, $priceData);
        $builder->addModel($modelData);
        $builder->addLocation($locationData);
        $builder->addRam($ramData);
        $builder->addStorage($storageData);

        return $builder->getProduct();
    }
}
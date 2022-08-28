<?php
namespace App\Builder\Product;

use App\Builder\Product\ProductBuilder;
use App\Entity\Product;
use App\Entity\Model;
use App\Entity\Location;
use App\Entity\Ram;
use App\Entity\Storage;
use Doctrine\Persistence\ManagerRegistry;

class ProductNoStorageBuilder extends ProductBuilder {

    public function addStorage(?string $storageData): void
    {        
        $this->product->setStorageQuantity(0);
        $this->product->setStorage(null);
    }
}
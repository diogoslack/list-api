<?php
namespace App\Builder\Product;

use App\Entity\Product;
use App\Entity\Model;
use App\Entity\Location;
use App\Entity\Ram;
use App\Entity\Storage;
use App\Helper\ProductHelper;
use App\Helper\RamHelper;
use App\Helper\StorageHelper;
use Doctrine\Persistence\ManagerRegistry;

class ProductBuilder implements BuilderInterface
{
    protected Product $product;
    protected Model $model;
    protected Location $location;
    protected Ram $ram;
    protected Storage $storage;
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->product = new Product();
        $this->model = new Model();
        $this->location = new Location();
        $this->ram = new Ram();
        $this->storage = new Storage();
    }

    public function createProduct(\DateTime $version, string $priceData): void 
    {        
        $this->product->setVersion($version);
        
        $productData = ProductHelper::getValuesFromString($priceData);
        if (!$productData || !$productData['price']) {
            return;
        }

        $this->product->setPrice($productData['price']);
        $this->product->setCurrency($productData['currency']);
    }

    public function addModel(string $modelData): void
    {            
        $this->model->setName($modelData);
        $dbModel = $this->doctrine->getRepository(Model::class)->findOneBy(['name' => $this->model->getName()]);

        if ($dbModel) {
            $this->product->setModel($dbModel);
        } else {
            $this->product->setModel($this->model);
        }
    }

    public function addLocation(string $locationData): void
    {        
        $this->location->setName($locationData);
        $dbLocation = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $this->location->getName()]);

        if ($dbLocation) {
            $this->product->setLocation($dbLocation);
        } else {
            $this->product->setLocation($this->location);
        }
    }

    public function addRam(string $ramData): void
    {
        $ramData = RamHelper::getValuesFromString($ramData);
        if (!$ramData || !$ramData['name']) {
            return;
        }

        $this->ram->setName($ramData['name']);
        $this->ram->setType($ramData['type']);
        $this->ram->setSize($ramData['size']);
        $dbRam = $this->doctrine->getRepository(Ram::class)->findOneBy(['name' => $this->ram->getName()]);

        if ($dbRam) {
            $this->product->setRam($dbRam);
        } else {
            $this->product->setRam($this->ram);
        }
    }

    public function addStorage(?string $storageData): void
    {
        if (!$storageData) {
            return;
        }

        $storageQuantity = StorageHelper::getStorageQuantity($storageData);
        $storageData = StorageHelper::getValuesFromString($storageData);        
        if (!$storageData || !$storageData['name']) {
            return;
        }

        $this->storage->setName($storageData['name']);
        $this->storage->setType($storageData['type']);
        $this->storage->setSize($storageData['size']);
        $this->product->setStorageQuantity($storageQuantity);
        
        $dbStorage = $this->doctrine->getRepository(Storage::class)->findOneBy(['name' => $this->storage->getName()]);

        if ($dbStorage) {
            $this->product->setStorage($dbStorage);
        } else {
            $this->product->setStorage($this->storage);
        }
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

}
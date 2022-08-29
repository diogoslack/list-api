<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Model;
use App\Entity\Location;
use App\Entity\Ram;
use App\Entity\Storage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $version = new \DateTime();
        $productList = [
            [
                'model' => 'Dell R210Intel Xeon X3440',
                'ram' => ['name' => '16GBDDR3', 'type' => 'DDR3', 'size' => 16],
                'hdd' => ['name' => '2x2TBSATA2', 'type' => 'SATA', 'size' => 4096, 'qty' => 2],
                'location' => 'AmsterdamAMS-01',
                'price' => '49.99',
                'currency' => '€'
            ],
            [
                'model' => 'HP DL180G62x Intel Xeon E5620',
                'ram' => ['name' => '128GBDDR4', 'type' => 'DDR4', 'size' => 128],
                'hdd' => [ 'name' => '8x2TBSATA2', 'type' => 'SATA', 'size' => 16384, 'qty' => 8],
                'location' => 'AmsterdamAMS-01',
                'price' => '119.00',
                'currency' => '€'
            ],
            [
                'model' => 'HP DL380eG82x Intel Xeon E5-2420',
                'ram' => ['name' => '64GBDDR4', 'type' => 'DDR4', 'size' => 64],
                'hdd' => [ 'name' => '4x480GBSSD', 'type' => 'SSD', 'size' => 1920, 'qty' => 4],
                'location' => 'AmsterdamAMS-01',
                'price' => '131.99',
                'currency' => '€'
            ],
            [
                'model' => 'RH2288v32x Intel Xeon E5-2650V4',
                'ram' => ['name' => '64GBDDR3', 'type' => 'DDR3', 'size' => 64],
                'hdd' => [ 'name' => '4x2TBSATA2', 'type' => 'SATA', 'size' => 8192, 'qty' => 4],
                'location' => 'AmsterdamAMS-01',
                'price' => '227.99',
                'currency' => '€'
            ],            
        ];

        foreach($productList as $productValues) {            
            $product = new Product();
            $model = new Model();
            $location = new Location();
            $ram = new Ram();
            $storage = new Storage();

            $product->setVersion($version);
            $product->setPrice($productValues['price']);
            $product->setCurrency($productValues['currency']);

            $dbModel = $manager->getRepository(Model::class)->findOneBy(['name' => $productValues['model']]);
            if ($dbModel) {
                $product->setModel($dbModel);
            } else {
                $model->setName($productValues['model']);
                $product->setModel($model);
            }

            $dbLocation = $manager->getRepository(Location::class)->findOneBy(['name' => $productValues['location']]);
            if ($dbLocation) {
                $product->setLocation($dbLocation);
            } else {
                $location->setName($productValues['location']);
                $product->setLocation($location);
            }

            $dbRam = $manager->getRepository(Ram::class)->findOneBy(['name' => $productValues['ram']['name']]);
            if ($dbRam) {
                $product->setRam($dbRam);
            } else {
                $ram->setName($productValues['ram']['name']);
                $ram->setType($productValues['ram']['type']);
                $ram->setSize($productValues['ram']['size']);
                $product->setRam($ram);
            }

            $dbStorage = $manager->getRepository(Storage::class)->findOneBy(['name' => $productValues['hdd']['name']]);
            if ($dbStorage) {
                $product->setStorage($dbStorage);
            } else {
                $storage->setName($productValues['hdd']['name']);
                $storage->setType($productValues['hdd']['type']);
                $storage->setSize($productValues['hdd']['size']);
                $product->setStorage($storage);
            }
            
            $product->setStorageQuantity($productValues['hdd']['qty']);
            
            $manager->persist($product);
            $manager->flush();
            unset($product, $model, $location, $ram, $storage);
        }

        $manager->flush();
    }
}

<?php
namespace App\Builder\Product;

use App\Entity\Product;

interface BuilderInterface
{
    public function createProduct(\DateTime $version, string $priceData): void;

    public function addModel(string $modelData): void;

    public function addLocation(string $locationData): void;

    public function addRam(string $ramData): void;

    public function addStorage(?string $storageData): void;

    public function getProduct(): Product;
}
<?php
namespace App\Tests\Repository;

use App\Entity\Storage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StorageRepositoryTest extends KernelTestCase
{    
    private \Doctrine\ORM\EntityManager $entityManager;

    protected function setUp(): void
    {        
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByName()
    {
        $storageToSearch = '2x2TBSATA2';
        $storage = $this->entityManager
            ->getRepository(Storage::class)
            ->findOneBy(['name' => $storageToSearch])
        ;

        $this->assertSame($storageToSearch, $storage->getName());
        $this->assertSame(4096, $storage->getSize());
        $this->assertSame('SATA', $storage->getType());
    }

    protected function tearDown(): void
    {
        parent::tearDown();


        $this->entityManager->close();
    }
}
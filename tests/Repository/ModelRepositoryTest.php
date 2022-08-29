<?php
namespace App\Tests\Repository;

use App\Entity\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModelRepositoryTest extends KernelTestCase
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
        $modelToSearch = 'Dell R210Intel Xeon X3440';
        $model = $this->entityManager
            ->getRepository(Model::class)
            ->findOneBy(['name' => $modelToSearch])
        ;

        $this->assertSame($modelToSearch, $model->getName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();


        $this->entityManager->close();
    }
}
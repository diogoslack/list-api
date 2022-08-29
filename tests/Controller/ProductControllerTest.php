<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductControllerTest extends WebTestCase
{
    private \Doctrine\ORM\EntityManager $entityManager;

    public function testLocation()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET', '/product/location',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $expected = '{"result":[{"location":"AmsterdamAMS-01"}]}';
        $this->assertSame($response->getContent(), $expected);
    }

    public function testGetProduct()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET', '/product',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $objRes = json_decode($response->getContent(), true);
        $expected = [
            'totalPages' => 1,
            'previousPage' => null,
            'price' => 49.99,
            'currency' => '€',
            'model'  => "Dell R210Intel Xeon X3440",
            'ram' => "16GBDDR3"
        ];
        $this->assertSame($objRes['pagination']['totalPages'], $expected['totalPages']);
        $this->assertSame($objRes['pagination']['previousPage'], $expected['previousPage']);

        $this->assertSame($objRes['result'][0]['price'], $expected['price']);
        $this->assertSame($objRes['result'][0]['currency'], $expected['currency']);
        $this->assertSame($objRes['result'][0]['model'], $expected['model']);
        $this->assertSame($objRes['result'][0]['ram'], $expected['ram']);
    }
}
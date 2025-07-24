<?php

namespace App\Tests\Functional;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $productRepository;
    private $orderRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->productRepository = static::getContainer()->get(ProductRepository::class);
        $this->orderRepository = static::getContainer()->get(OrderRepository::class);

        $this->createTestProducts();
    }
    public static function tearDownAfterClass(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $productRepository = static::getContainer()->get(ProductRepository::class);

        $products = $productRepository->findBy(['test' => true]);
        foreach ($products as $product) {
            $entityManager->remove($product);
        }
        $entityManager->flush();
        $entityManager->close();
        $entityManager = null;
        $productRepository = null;
        parent::tearDownAfterClass();
    }

    private function createTestProducts(): void
    {
        if ($this->productRepository->count([]) > 0) {
            return;
        }

        $product1 = new Product();
        $product1->setTest(true);
        $product1->setPrice(10.00);
        $product1->setVat(2.00);


        $product2 = new Product();
        $product2->setTest(true);
        $product2->setPrice(25.50);
        $product2->setVat(5.10);

        $this->entityManager->persist($product1);
        $this->entityManager->persist($product2);
        $this->entityManager->flush();
    }

    public function testCreateOrderSuccess(): void
    {
        $product = $this->productRepository->findOneBy([]);

        $orderData = [
            'order' => [
                'items' => [
                    [
                        'product_id' => $product->getId(),
                        'quantity' => 2
                    ]
                ]
            ]
        ];


        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('order_id', $responseData);
        $this->assertArrayHasKey('order_total', $responseData);
        $this->assertArrayHasKey('order_vat', $responseData);
        $this->assertArrayHasKey('items', $responseData);
        $this->assertCount(1, $responseData['items']);

        $this->assertEquals($product->getId(), $responseData['items'][0]['product_id']);
        $this->assertEquals(2, $responseData['items'][0]['quantity']);
        $this->assertEquals($product->getPrice(), $responseData['items'][0]['price']);
        $this->assertEquals($product->getVat(), $responseData['items'][0]['vat']);

        $order = $this->orderRepository->find($responseData['order_id']);
        if ($order) {
            $this->entityManager->remove($order);
            $this->entityManager->flush();
        }

    }

    public function testCreateOrderWithInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json content'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid JSON format', $responseData['error']);
    }

    public function testCreateOrderWithMissingOrderField(): void
    {
        $orderData = [
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2
                ]
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Validation failed', $responseData['error']);
    }

    public function testCreateOrderWithEmptyItems(): void
    {
        $orderData = [
            'order' => [
                'items' => []
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Validation failed', $responseData['error']);
    }

    public function testCreateOrderWithInvalidProductId(): void
    {
        $connection = $this->entityManager->getConnection();
        $sql = "SELECT last_value FROM product_id_seq";
        $currentProductId = $connection->executeQuery($sql)->fetchOne();
        $nextProductId = $currentProductId + 1;

        $orderData = [
            'order' => [
                'items' => [
                    [
                        'product_id' => $nextProductId,
                        'quantity' => 2
                    ]
                ]
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Product not found', $responseData['error']);
    }

    public function testCreateOrderWithInvalidQuantity(): void
    {
        $product = $this->productRepository->findOneBy([]);

        $orderData = [
            'order' => [
                'items' => [
                    [
                        'product_id' => $product->getId(),
                        'quantity' => 0
                    ]
                ]
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Validation failed', $responseData['error']);
    }

    public function testCreateOrderWithMissingProductId(): void
    {
        $orderData = [
            'order' => [
                'items' => [
                    [
                        'quantity' => 2
                    ]
                ]
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Missing constructor arguments', $responseData['error']);
    }

    public function testCreateOrderWithMissingQuantity(): void
    {
        $product = $this->productRepository->findOneBy([]);

        $orderData = [
            'order' => [
                'items' => [
                    [
                        'product_id' => $product->getId()
                    ]
                ]
            ]
        ];

        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($orderData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Missing constructor arguments', $responseData['error']);
    }

    public function testGetOrdersEndpoint(): void
    {
        $this->client->request('GET', '/api/orders');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');
    }

    public function testGetProductsEndpoint(): void
    {
        $productCount = $this->productRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $this->client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount($productCount, $responseData['member']); // 2 test products
    }

    public function testGetSingleProductEndpoint(): void
    {
        $product = $this->productRepository->findOneBy([]);

        $this->client->request('GET', '/api/products/' . $product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($product->getId(), $responseData['id']);
        $this->assertEquals($product->getPrice(), $responseData['price']);
        $this->assertEquals($product->getVat(), $responseData['vat']);
    }

    public function testGetNonExistentOrder(): void
    {
        $connection = $this->entityManager->getConnection();
        $sql = "SELECT last_value FROM order_id_seq";
        $currentOrderId = $connection->executeQuery($sql)->fetchOne();
        $nextOrderId = $currentOrderId + 1;

        $this->client->request('GET', '/api/orders/' . $nextOrderId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testGetNonExistentProduct(): void
    {
        $connection = $this->entityManager->getConnection();
        $sql = "SELECT last_value FROM product_id_seq";
        $currentProductId = $connection->executeQuery($sql)->fetchOne();
        $nextProductId = $currentProductId + 1;

        $this->client->request('GET', '/api/products/'.$nextProductId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

}

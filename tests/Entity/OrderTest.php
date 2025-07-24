<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\Item;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
    }

    public function testIdGetter(): void
    {
        $this->assertNull($this->order->getId());
    }

    public function testItemsCollection(): void
    {
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->order->getItems());
        $this->assertTrue($this->order->getItems()->isEmpty());
    }

    public function testAddItem(): void
    {
        $product = $this->createMock(Product::class);
        $item = new Item($product, 2, $this->order);
        
        $this->order->addItem($item);
        
        $this->assertCount(1, $this->order->getItems());
        $this->assertTrue($this->order->getItems()->contains($item));
    }

    public function testAddMultipleItems(): void
    {
        $product1 = $this->createMock(Product::class);
        $product2 = $this->createMock(Product::class);
        
        $item1 = new Item($product1, 1, $this->order);
        $item2 = new Item($product2, 3, $this->order);
        
        $this->order->addItem($item1);
        $this->order->addItem($item2);
        
        $this->assertCount(2, $this->order->getItems());
    }

    public function testGetTotalPriceWithNoItems(): void
    {
        $this->assertEquals(0.0, $this->order->getTotalPrice());
    }

    public function testGetTotalPriceWithItems(): void
    {
        $product1 = $this->createMock(Product::class);
        $product1->method('getPrice')->willReturn(10.0);
        $product1->method('getVat')->willReturn(0.22);
        
        $product2 = $this->createMock(Product::class);
        $product2->method('getPrice')->willReturn(20.0);
        $product2->method('getVat')->willReturn(0.22);
        
        $item1 = new Item($product1, 2, $this->order);
        $item2 = new Item($product2, 1, $this->order);
        
        $this->order->addItem($item1);
        $this->order->addItem($item2);
        
        // Expected: (10 * 2) + (20 * 1) = 40
        $this->assertEquals(40.0, $this->order->getTotalPrice());
    }

    public function testGetTotalVatWithNoItems(): void
    {
        $this->assertEquals(0.0, $this->order->getTotalVat());
    }

    public function testGetTotalVatWithItems(): void
    {
        $product1 = $this->createMock(Product::class);
        $product1->method('getPrice')->willReturn(10.0);
        $product1->method('getVat')->willReturn(0.22);
        
        $product2 = $this->createMock(Product::class);
        $product2->method('getPrice')->willReturn(20.0);
        $product2->method('getVat')->willReturn(0.22);
        
        $item1 = new Item($product1, 2, $this->order);
        $item2 = new Item($product2, 1, $this->order);
        
        $this->order->addItem($item1);
        $this->order->addItem($item2);
        
        // Expected: (10 * 2 * 0.22) + (20 * 1 * 0.22) = 4.4 + 4.4 = 8.8
        $this->assertEquals(8.8, $this->order->getTotalVat());
    }

    public function testGetTotalPriceWithDecimalQuantities(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(15.50);
        $product->method('getVat')->willReturn(0.10);
        
        $item = new Item($product, 3, $this->order);
        $this->order->addItem($item);
        
        // Expected: 15.50 * 3 = 46.50
        $this->assertEquals(46.50, $this->order->getTotalPrice());
    }

    public function testGetTotalVatWithDecimalQuantities(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(15.50);
        $product->method('getVat')->willReturn(0.10);
        
        $item = new Item($product, 3, $this->order);
        $this->order->addItem($item);
        
        // Expected: 15.50 * 3 * 0.10 = 4.65
        $this->assertEquals(4.65, $this->order->getTotalVat());
    }

    public function testAddItemReturnsSelf(): void
    {
        $product = $this->createMock(Product::class);
        $item = new Item($product, 1, $this->order);
        
        $result = $this->order->addItem($item);
        
        $this->assertSame($this->order, $result);
    }
} 
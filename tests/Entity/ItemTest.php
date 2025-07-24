<?php

namespace App\Tests\Entity;

use App\Entity\Item;
use App\Entity\Product;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    private Product $product;
    private Order $order;
    private Item $item;

    protected function setUp(): void
    {
        $this->product = $this->createMock(Product::class);
        $this->product->method('getPrice')->willReturn(99.99);
        $this->product->method('getVat')->willReturn(0.22);
        
        $this->order = $this->createMock(Order::class);
        $this->item = new Item($this->product, 2, $this->order);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Item::class, $this->item);
    }

    public function testGetProduct(): void
    {
        $this->assertSame($this->product, $this->item->getProduct());
    }

    public function testGetQuantity(): void
    {
        $this->assertEquals(2, $this->item->getQuantity());
    }

    public function testGetOrder(): void
    {
        $this->assertSame($this->order, $this->item->getOrder());
    }

    public function testGetIdInitiallyNull(): void
    {
        $this->assertNull($this->item->getId());
    }

    public function testGetPriceFromProduct(): void
    {
        $expectedPrice = 99.99;
        $this->product->method('getPrice')->willReturn($expectedPrice);
        
        $this->assertEquals($expectedPrice, $this->item->getPrice());
    }

    public function testGetVatFromProduct(): void
    {
        $expectedVat = 0.22;
        $this->product->method('getVat')->willReturn($expectedVat);
        
        $this->assertEquals($expectedVat, $this->item->getVat());
    }

    public function testConstructorWithDifferentValues(): void
    {
        $product = $this->createMock(Product::class);
        $order = $this->createMock(Order::class);
        $quantity = 5;
        
        $item = new Item($product, $quantity, $order);
        
        $this->assertSame($product, $item->getProduct());
        $this->assertEquals($quantity, $item->getQuantity());
        $this->assertSame($order, $item->getOrder());
    }

    public function testConstructorWithZeroQuantity(): void
    {
        $product = $this->createMock(Product::class);
        $order = $this->createMock(Order::class);
        
        $item = new Item($product, 0, $order);
        
        $this->assertEquals(0, $item->getQuantity());
    }

    public function testConstructorWithNegativeQuantity(): void
    {
        $product = $this->createMock(Product::class);
        $order = $this->createMock(Order::class);
        
        $item = new Item($product, -1, $order);
        
        $this->assertEquals(-1, $item->getQuantity());
    }

    public function testPriceCalculationWithMockProduct(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(25.50);
        $product->method('getVat')->willReturn(0.10);
        
        $order = $this->createMock(Order::class);
        $item = new Item($product, 3, $order);
        
        $this->assertEquals(25.50, $item->getPrice());
        $this->assertEquals(0.10, $item->getVat());
    }

    public function testPriceCalculationWithDecimalValues(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(123.456);
        $product->method('getVat')->willReturn(0.123);
        
        $order = $this->createMock(Order::class);
        $item = new Item($product, 1, $order);
        
        $this->assertEquals(123.456, $item->getPrice());
        $this->assertEquals(0.123, $item->getVat());
    }

    public function testPriceCalculationWithZeroValues(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(0.0);
        $product->method('getVat')->willReturn(0.0);
        
        $order = $this->createMock(Order::class);
        $item = new Item($product, 1, $order);
        
        $this->assertEquals(0.0, $item->getPrice());
        $this->assertEquals(0.0, $item->getVat());
    }

    public function testPriceCalculationWithNegativeValues(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(-10.0);
        $product->method('getVat')->willReturn(-0.05);
        
        $order = $this->createMock(Order::class);
        $item = new Item($product, 1, $order);
        
        $this->assertEquals(-10.0, $item->getPrice());
        $this->assertEquals(-0.05, $item->getVat());
    }
} 
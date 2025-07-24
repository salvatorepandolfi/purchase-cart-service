<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    public function testIdGetterAndSetter(): void
    {
        $id = 1;
        $this->product->setId($id);
        
        $this->assertEquals($id, $this->product->getId());
    }

    public function testPriceGetterAndSetter(): void
    {
        $price = 99.99;
        $this->product->setPrice($price);
        
        $this->assertEquals($price, $this->product->getPrice());
    }

    public function testVatGetterAndSetter(): void
    {
        $vat = 0.22;
        $this->product->setVat($vat);
        
        $this->assertEquals($vat, $this->product->getVat());
    }

    public function testDefaultValues(): void
    {
        $this->assertNull($this->product->getId());
        $this->assertNull($this->product->getPrice());
        $this->assertNull($this->product->getVat());
    }

    public function testDecimalPrecision(): void
    {
        $price = 123.456789;
        $vat = 0.123456;
        
        $this->product->setPrice($price);
        $this->product->setVat($vat);
        
        $this->assertEquals($price, $this->product->getPrice());
        $this->assertEquals($vat, $this->product->getVat());
    }

    public function testZeroValues(): void
    {
        $this->product->setPrice(0);
        $this->product->setVat(0);
        
        $this->assertEquals(0, $this->product->getPrice());
        $this->assertEquals(0, $this->product->getVat());
    }

    public function testNegativeValues(): void
    {
        $this->product->setPrice(-10.50);
        $this->product->setVat(-0.05);
        
        $this->assertEquals(-10.50, $this->product->getPrice());
        $this->assertEquals(-0.05, $this->product->getVat());
    }
} 
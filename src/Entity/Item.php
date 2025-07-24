<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['item:read'])]
    private Order $order;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $vat;

    public function __construct(Product $product, int $quantity, Order $order)
    {
        $this->product = $product;
        $this->price = $product->getPrice();
        $this->vat = $product->getVat();
        $this->quantity = $quantity;
        $this->order = $order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $vat;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $test = false;

    public function __construct(float $price = 0.0, float $vat = 0.0, bool $test = false)
    {
        $this->price = $price;
        $this->vat = $vat;
        $this->test = $test;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function setVat(float $vat): void
    {
        $this->vat = $vat;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(bool $test): void
    {
        $this->test = $test;
    }
}

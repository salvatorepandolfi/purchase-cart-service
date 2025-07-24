<?php

namespace App\DTO;

class ItemResponse
{
    public int $productId;

    public int $quantity;

    public float $price;

    public float $vat;

    public function __construct(int $productId, int $quantity, float $price, float $vat)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = round($price, 2);
        $this->vat = round($vat, 2);
    }
}

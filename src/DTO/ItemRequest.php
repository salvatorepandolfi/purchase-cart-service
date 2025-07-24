<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ItemRequest
{
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    public int $productId;

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    public int $quantity;

    public function __construct(int $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}

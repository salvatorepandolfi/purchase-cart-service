<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderRequestContainer
{
    /**
     * @var ItemRequest[]
     */
    #[Assert\NotBlank]
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public array $items = [];
}

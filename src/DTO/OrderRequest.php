<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderRequest
{
    #[Assert\NotNull]
    #[Assert\Valid]
    public OrderRequestContainer $order;

    public function __construct()
    {
        $this->order = new OrderRequestContainer();
    }
}

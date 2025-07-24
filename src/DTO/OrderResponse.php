<?php

namespace App\DTO;

class OrderResponse
{
    public int $orderId;

    public float $orderTotal;

    public float $orderVat;

    /**
     * @var ItemResponse[]
     */
    public array $items;

    /**
     * OrderResponse constructor.
     *
     * @param ItemResponse[] $items
     */
    public function __construct(int $orderId, float $orderTotal, float $orderVat, array $items)
    {
        $this->orderId = $orderId;
        $this->orderTotal = round($orderTotal, 2);
        $this->orderVat = round($orderVat, 2);
        $this->items = $items;
    }
}

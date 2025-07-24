<?php

namespace App\Service;

use App\DTO\OrderRequest;
use App\Entity\Item;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function createOrder(OrderRequest $orderRequest): Order
    {
        $order = new Order();
        $missingProducts = [];
        $items = [];
        foreach ($orderRequest->order->items as $item) {
            $product = $this->productRepository->find($item->productId);
            if (!$product) {
                $missingProducts[] = $item->productId;
            } else {
                $items[] = new Item($product, $item->quantity, $order);
            }
        }
        if (count($missingProducts) > 0) {
            throw new \InvalidArgumentException(sprintf('Products with IDs %s do not exist.', implode(', ', $missingProducts)));
        }

        foreach ($items as $currentItem) {
            $this->entityManager->persist($currentItem);
            $order->addItem($currentItem);
        }
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}

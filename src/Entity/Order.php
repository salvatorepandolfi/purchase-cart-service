<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\OrderController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/orders',
            controller: OrderController::class,
            openapi: new Operation(
                operationId: 'createOrder',
                tags: ['Order'],
                responses: [
                    '201' => [
                        'description' => 'Order created successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'orderId' => ['type' => 'integer'],
                                        'orderTotal' => ['type' => 'number'],
                                        'orderVat' => ['type' => 'number'],
                                        'items' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'productId' => ['type' => 'integer'],
                                                    'quantity' => ['type' => 'integer'],
                                                    'price' => ['type' => 'number'],
                                                    'vat' => ['type' => 'number'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Invalid input data',
                    ],
                ],
                requestBody: new RequestBody(
                    description: 'Order creation request body',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'order' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'items' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'productId' => ['type' => 'integer', 'minimum' => 1],
                                                        'quantity' => ['type' => 'integer', 'minimum' => 1],
                                                    ],
                                                    'required' => ['productId', 'quantity'],
                                                ],
                                            ],
                                        ],
                                        'required' => ['items'],
                                    ],
                                ],
                                'required' => ['order'],
                            ],
                        ],
                    ])
                ),
            ),
            denormalizationContext: ['groups' => ['order:write']],
            validationContext: ['groups' => ['order:write']],
            input: 'App\DTO\OrderRequest',
            output: 'App\DTO\OrderResponse'
        ),
        new Get(
            normalizationContext: ['groups' => ['order:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['order:read']]
        ),
    ]
)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * @var Collection<Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'order', orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return Collection<Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        $this->items->add($item);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): float
    {
        $total = 0;
        /** @var Item $item */
        foreach ($this->items as $item) {
            $total += $item->getPrice() * $item->getQuantity();
        }

        return $total;
    }

    public function getTotalVat(): float
    {
        $totalVat = 0;
        /** @var Item $item */
        foreach ($this->items as $item) {
            $totalVat += $item->getPrice() * $item->getQuantity() * $item->getVat();
        }

        return $totalVat;
    }
}

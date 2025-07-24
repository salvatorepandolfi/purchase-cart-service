<?php

namespace App\Controller;

use App\DTO\ItemResponse;
use App\DTO\OrderRequest;
use App\DTO\OrderResponse;
use App\Entity\Item;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $orderRequest = $this->serializer->deserialize(
                $request->getContent(),
                OrderRequest::class,
                'json'
            );
            $violationList = $this->validator->validate($orderRequest);
            if (count($violationList) > 0) {
                $errors = [];
                foreach ($violationList as $violation) {
                    $errors[] = [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                }

                return new JsonResponse([
                    'error' => 'Validation failed',
                    'details' => $errors,
                ], Response::HTTP_BAD_REQUEST);
            }

            $createdOrder = $this->orderService->createOrder($orderRequest);

            $itemResponses = [];
            /** @var Item $item */
            foreach ($createdOrder->getItems() as $item) {
                $itemResponses[] = new ItemResponse(
                    $item->getProduct()->getId(),
                    $item->getQuantity(),
                    $item->getPrice(),
                    $item->getVat()
                );
            }

            $responseData = new OrderResponse(
                $createdOrder->getId(),
                $createdOrder->getTotalPrice(),
                $createdOrder->getTotalVat(),
                $itemResponses,
            );
            $responseData = $this->serializer->serialize($responseData, 'json');

            return new JsonResponse(
                json_decode($responseData, true),
                Response::HTTP_CREATED
            );
        } catch (NotEncodableValueException $e) {
            return new JsonResponse([
                'error' => 'Invalid JSON format',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (NotFoundHttpException|\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => 'Product not found',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (MissingConstructorArgumentsException $e) {
            return new JsonResponse([
                'error' => 'Missing constructor arguments',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            dump($e);

            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

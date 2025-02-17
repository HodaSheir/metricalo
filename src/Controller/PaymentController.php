<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PaymentService;

final class PaymentController extends AbstractController
{
    public function __construct(private PaymentService $paymentService) {}

    #[Route('/app/example/{provider}', methods: ['POST'])]
    public function processPayment(string $provider, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!in_array($provider, ['aci', 'shift4'])) {
            return new JsonResponse(['error' => 'Invalid provider'], 400);
        }

        try {
            $response = $this->paymentService->processPayment($provider, $data);
            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}

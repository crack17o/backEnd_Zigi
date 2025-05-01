<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Command;
use App\Service\CinetPayService;

class PaymentController extends AbstractController
{
    private $cinetPayService;
    private $entityManager;

    public function __construct(CinetPayService $cinetPayService, EntityManagerInterface $entityManager)
    {
        $this->cinetPayService = $cinetPayService;
        $this->entityManager = $entityManager;
    }

    #[Route('/payment/init', name: 'payment_init', methods: ['POST'])]
    public function initPayment(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $amount = $data['netTotalPrice'];
            $transactionId = $data['commandId'];

            // Envoyer la requête à CinetPay
            $response = $this->cinetPayService->initPayment($transactionId, $amount);

            if (isset($response['data']['payment_url'])) {
                return $this->json(['url' => $response['data']['payment_url']]);
            }
      
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['error' => 'Échec de l’initialisation du paiement'], 400);
    }

    #[Route('/payment/notify', name: 'payment_notify', methods: ['POST'])]
    public function paymentNotify(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $transactionId = $data['transaction_id'] ?? null;

            if (!$transactionId) {
                return $this->json(['error' => 'Transaction ID manquant'], 400);
            }

            $transaction = $this->entityManager->getRepository(Command::class)->findOneBy(['transactionId' => $transactionId]);

            if (!$transaction) {
                return $this->json(['error' => 'Transaction non trouvée'], 404);
            } elseif ($transaction->getStatus() === 'PAID') {
                return $this->json(['message' => 'Transaction déjà payée'], 200);
            }

            $response = $this->cinetPayService->checkPaymentStatus($transactionId);

            if ($response['data']['status'] === 'ACCEPTED') {
                $transaction->setStatus('PAID');
                $this->entityManager->flush();
                return $this->json(['message' => 'Paiement validé']);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['message' => 'Paiement non confirmé'], 400);
    }

    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function paymentSuccess(): JsonResponse
    {
        return $this->json(['message' => 'Paiement réussi !']);
    }
}

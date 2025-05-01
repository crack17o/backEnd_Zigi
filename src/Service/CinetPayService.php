<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;

class CinetPayService
{
    private $httpClient;
    private $apiKey;
    private $siteId;
    private $returnUrl;
    private $notifyUrl;
    private $checkUrl;
    private $paymentUrl;
    private $entityManager;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $siteId, string $returnUrl, string $notifyUrl, string $checkUrl, string $paymentUrl, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->siteId = $siteId;
        $this->returnUrl = $returnUrl;
        $this->notifyUrl = $notifyUrl;
        $this->checkUrl = $checkUrl;
        $this->paymentUrl = $paymentUrl;
        $this->entityManager = $entityManager;
    }

    public function initPayment($transactionId, $amount, $currency = "CDF", $description = "Achat sur zigi")
    {
        // Vérification de l'existence de transactionId dans la table command
        if (!$this->transactionExists($transactionId)) {
            throw new \Exception("Transaction ID non valide : $transactionId");
        }
        
        $response = $this->httpClient->request('POST', $this->paymentUrl, [
            'json' => [
                'apikey'           => $this->apiKey,   // Clé API CinetPay
                'site_id'          => $this->siteId,   // ID du site fourni par CinetPay
                'transaction_id'   => (string)$transactionId,  // Identifiant unique
                'amount'           => (int)$amount,         // Montant (ex: 5000) ✅ DOIT ÊTRE UN MULTIPLE DE 5
                'currency'         => $currency,           // Devise (XOF, XAF, CDF, GNF, USD)
                'description'      => $description, // Pas de caractères spéciaux
                'notify_url'       => $this->notifyUrl,  // URL de notification
                'return_url'       => $this->returnUrl, // URL de redirection après paiement
                'channels'         => 'ALL',           // Méthode de paiement (ALL, MOBILE_MONEY, CREDIT_CARD, WALLET)
                'lang'             => 'fr',            // Langue (fr, en) - optionnel
            ]
        ]);

        return $response->toArray();
    }

    public function checkPaymentStatus($transactionId)
    {
        $response = $this->httpClient->request('POST', $this->checkUrl, [
            'json' => [
                'apikey'         => $this->apiKey,
                'site_id'        => $this->siteId,
                'transaction_id' => (string) $transactionId
            ]
        ]);

        return $response->toArray();
    }

    // Ajout de la méthode pour vérifier l'existence de la transaction
    private function transactionExists($transactionId)
    {
        // Utilisez l'Entity Manager injecté
        $transaction = $this->entityManager->getRepository(Command::class)->find( $transactionId);
        return $transaction !== null; // Vérifie si la transaction existe
    }
}

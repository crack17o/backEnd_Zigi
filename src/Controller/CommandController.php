<?php 

namespace App\Controller;

use App\Service\CommandService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommandController extends AbstractController
{
    

    #[Route('/api/orders/create', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request,CommandService $commandService): JsonResponse
    {
        try {
            // Décoder les données JSON envoyées dans la requête
            $data = json_decode($request->getContent(), true);

            // Vérifier si les données sont présentes
            if (!$data) {
                return $this->json(['error' => 'Données invalides ou manquantes'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Validation des données utilisateurs
            $userData = $data['user'] ?? null;
            $orderData = $data['order'] ?? null;
            $userId = $data['userId'] ?? null;

            if (!$userData || !$orderData) {
                return $this->json(['error' => 'Les données utilisateur ou commande sont manquantes'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Appeler le service pour traiter la commande
            $response = $commandService->processOrder($userData, $orderData, $userId);

            // Retourner une réponse JSON avec les informations de la commande
            return $this->json([
                'message' => $response['message'],
                'userId' => $response['userId'],
                'commandId' => $response['commandId'],
                'netTotalPrice' => $response['netTotalPrice'],
                'currency' => $response['currency'],
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    
     
    #[Route('/api/orders/list', name:'get_all_orders', methods:['GET'])]
    
    public function getAllOrders(CommandService $commandService): JsonResponse
    {
        try {
            // Appel au service pour récupérer toutes les commandes
            $orders = $commandService->getAllOrders();

            // Transformation des commandes en un tableau de données pour la réponse
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'idCommand' => $order->getId(),
                    'user' => [
                        'id' => $order->getIdUser()->getId(),
                        'email' => $order->getIdUser()->getEmail(),
                        'firstName' => $order->getIdUser()->getFirstName(),
                        'lastName' => $order->getIdUser()->getLastName(),
                        'address' => $order->getIdUser()->getAddress(),
                        'status' => $order->getIdUser()->getisActive(),
                        'phoneNumber' => $order->getIdUser()->getNumeroTel(),
                        'userType' => $order->getIdUser()->getTypeUser(),
                    ],
                    'dateCommand' => $order->getDateCommand()->format('Y-m-d H:i:s'),
                    'statut' => $order->getStatutCom(),
                    // Ajoutez d'autres champs nécessaires ici
                ];
            }

            return $this->json([
                'success' => true,
                'data' => $data,
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            // Gérer les erreurs
            return $this->json(['message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }


    
    #[Route("/api/orders/approved/{adminId}/{commandId}", name:"approve_order", methods:["POST"])]   
    public function approveOrder(int $commandId,int $adminId,CommandService $commandService): JsonResponse
    {
        try {
            // Récupérer l'adminId depuis le corps de la requête JSON
            
         
            // Appeler le service pour approuver la commande
            $result = $commandService->approveOrder($commandId, $adminId);

            return $this->json([
                'message' => $result['message'],
                'commandId' => $result['commandId'],
            ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs et envoyer une réponse appropriée
            return $this->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    #[Route("/api/orders/rejected/{adminId}/{commandId}", name:"rejected_order", methods:["POST"])]   
    public function rejectOrder(int $commandId,int $adminId,CommandService $commandService): JsonResponse
    {
        try {
            // Récupérer l'adminId depuis le corps de la requête JSON
            
         
            // Appeler le service pour approuver la commande
            $result = $commandService->rejectOrder($commandId, $adminId);

            return $this->json([
                'message' => $result['message'],
                'commandId' => $result['commandId'],
            ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs et envoyer une réponse appropriée
            return $this->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }




} 
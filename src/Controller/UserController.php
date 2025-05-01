<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Contrôleur gérant les opérations liées aux utilisateurs
 */
class UserController extends AbstractController
{
    /**
     * Login
     * 
     * @param Request $request La requête HTTP contenant les données du client
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Réponse JSON avec les détails du client créé
     * 
     * Route: POST /api/login
     */
    #[Route('api/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserService $userService
    ): JsonResponse {


        try {

            //Recuperation des données du formulaire 
            $data = json_decode($request->getContent(), true);

            [$token, $myUser] = $userService->executeLogin($data);


            return $this->json([
                'message' => 'Connexion reussie',
                'token' => $token,
                'user' => [
                    'id' => $myUser->getId(),
                    'email' => $myUser->getEmail(),
                    'firstName' => $myUser->getFirstName(),
                    'lastName' => $myUser->getLastName(),
                    'address' => $myUser->getAddress(),
                    'status' => $myUser->getisActive(),
                    'phoneNumber' => $myUser->getNumeroTel(),
                    'userType' => $myUser->getTypeUser()
                ]
            ], JsonResponse::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['errors' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }



    /**
     * Crée un nouveau compte client
     * 
     * @param Request $request La requête HTTP contenant les données du client
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Réponse JSON avec les détails du client créé
     * 
     * Route: POST /api/users/newCustomer
     */
    #[Route('api/users/newCustomer', name: 'app_newCustomer', methods: ['POST'])]
    public function createCustomer(Request $request, UserService $userService): JsonResponse
    {
        try {

            //Recuperation des données du formulaire 
            $data = json_decode($request->getContent(), true);

            // Définition des règles de validation pour l'email
            $constraints = new Assert\Collection([
                'email' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire.'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse email n\'est pas valide.'
                    ]),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ]);

            $myUser = $userService->executeCreateCustomer($data);


            return $this->json([
                'message' => 'Inscription réussie avec succès,un mail d\'activation vous a été envoyé',
                'user' => [
                    'id' => $myUser->getId(),
                    'email' => $myUser->getEmail(),
                    'firstName' => $myUser->getFirstName(),
                    'lastName' => $myUser->getLastName(),
                    'address' => $myUser->getAddress(),
                    'status' => $myUser->getisActive(),
                    'phoneNumber' => $myUser->getNumeroTel(),
                    'userType' => $myUser->getTypeUser()
                ]
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['errors' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }



    /**
     * Met à jour les informations d'un utilisateur
     * 
     * @param int $id Identifiant de l'utilisateur
     * @param Request $request La requête HTTP contenant les nouvelles données
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Réponse JSON avec les détails mis à jour
     * 
     * Route: PUT /api/users/userUpdate/{id}
     */
    #[Route('api/users/userUpdate/{id}', name: 'app_update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, UserService $userService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $updatedUser = $userService->executeUpdateUser($id, $data);

            return $this->json([
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => [
                    'id' => $updatedUser->getId(),
                    'email' => $updatedUser->getEmail(),
                    'firstName' => $updatedUser->getFirstName(),
                    'lastName' => $updatedUser->getLastName(),
                    'address' => $updatedUser->getAddress(),
                    'status' => $updatedUser->getisActive(),
                    'phoneNumber' => $updatedUser->getNumeroTel(),
                    'userType' => $updatedUser->getTypeUser()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Supprime un utilisateur
     * 
     * @param int $id Identifiant de l'utilisateur à supprimer
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Message de confirmation
     * 
     * Route: DELETE /api/users/userDelete/{id}
     */
    #[Route('api/users/userDelete/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, UserService $userService): JsonResponse
    {
        try {
            $userService->executeDeleteUser($id);
            return $this->json(['message' => 'Utilisateur supprimé avec succès']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Récupère la liste de tous les utilisateurs
     * 
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Liste des utilisateurs au format JSON
     * 
     * Route: GET /api/users/userList
     */
    #[Route('api/users/userList', name: 'app_list_users', methods: ['GET'])]
    public function listUsers(UserService $userService): JsonResponse
    {
        try {
            $users = $userService->executeListUsers();
            return $this->json([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Active le compte d'un utilisateur avec un code de validation
     * 
     * @param int $id Identifiant de l'utilisateur
     * @param Request $request La requête HTTP contenant le code d'activation
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Message de confirmation ou erreur
     * 
     * Route: POST /api/users/activeAccount/{id}
     */
    #[Route('api/users/activeAccount/{id}', name: 'app_myactiveUser', methods: ['POST'])]
    public function activeAccount(int $id, Request $request, UserService $userService): JsonResponse
    {
        try {
            // Décoder le contenu JSON de la requête
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['codeActivation'])) {
                throw new \InvalidArgumentException('Code d\'activation manquant');
            }


            // Activer le compte
            $userService->executeActivationAccount($id, $data['codeActivation']);

            return $this->json([
                'message' => 'Votre compte a été activé avec succès'
            ], JsonResponse::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Génère et envoie un nouveau code de validation à l'utilisateur
     * 
     * @param int $id Identifiant de l'utilisateur
     * @param UserService $userService Service de gestion des utilisateurs
     * @return JsonResponse Message de confirmation ou erreur
     * 
     * Route: GET /api/users/requestNewCode/{id}
     */
    #[Route('api/users/requestNewCode/{id}', name: 'app_newCustommeee', methods: ['GET'])]
    public function requestNewCode(int $id, UserService $userService): JsonResponse
    {

        try {
            $userService->executerequestNewCode($id);
            return new JsonResponse(['message' => 'Nouveau code envoyé sur votre email'], JsonResponse::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /*#[Route('api/orders', name: 'app_process_order', methods: ['POST'])]
    public function processOrder(Request $request, UserService $userService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Vérifier que les données de l'utilisateur et de la commande sont présentes
            if (!isset($data['user']) || !isset($data['order'])) {
                throw new \InvalidArgumentException('Données utilisateur ou commande manquantes');
            }

            $result = $userService->processOrder($data['user'], $data['order']);

            return $this->json($result, JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }*/
}

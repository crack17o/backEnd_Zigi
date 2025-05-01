<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\DetailsCommand;
use App\Entity\User;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommandService
{


    private EntityManagerInterface $em;
    private ProductRepository $productRepository;
  

    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        CommandRepository $commandRepository
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;

    }
    /**
     * Traite une commande en fonction des données utilisateur et de commande fournies.
     *
     * @param array $userData Les données de l'utilisateur.
     * @param array $orderData Les données de la commande.
     * @param int|null $userId L'ID de l'utilisateur (facultatif).
     * @return array Les informations sur la commande traitée.
     */
    public function processOrder(array $userData, array $orderData, ?int $userId = null): array
    {
        $this->em->getConnection()->beginTransaction(); // Démarre une transaction

        try {
            // Trouve ou crée un utilisateur
            $user = $this->findOrCreateUser($userData, $userId);

            // Crée la commande
            $command = new Command();
            $command->setIdUser($user)
                ->setDateCommand(new \DateTime())
                ->setStatutCom('PENDING'); // Statut de la commande

            $this->em->persist($command);

            // Traite les détails de la commande
            $netTotalPrice = $this->processOrderDetails($orderData, $command);
            $this->em->flush(); // Enregistre toutes les modifications

            $command->setAmount($netTotalPrice);
            $this->em->persist($command);
            $this->em->flush();
            $this->em->getConnection()->commit(); // Valide la transaction

            return [
                'message' => 'Commande traitée avec succès',
                'userId' => $user->getId(),
                'commandId' => $command->getId(),
                'netTotalPrice' =>  $command->getAmount(),
                'currency' => $command->getCurrency(),
            ];
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack(); // Annule la transaction en cas d'erreur
            throw $e; // Relance l'exception
        }
    }

    /**
     * Trouve ou crée un utilisateur en fonction des données fournies.
     *
     * @param array $userData Les données de l'utilisateur.
     * @param int|null $userId L'ID de l'utilisateur (facultatif).
     * @return User L'utilisateur trouvé ou créé.
     */
    private function findOrCreateUser(array $userData, ?int $userId): User
    {
        if ($userId) {
            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                throw new \Exception("Utilisateur non trouvé.");
            }
            return $user;
        }

        // Cherche un utilisateur par email
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userData['email']]);

        if (!$user) {
            // Crée un nouvel utilisateur
            $user = new User();
            $user->setEmail($userData['email'])
                ->setFirstName($userData['firstName'])
                ->setLastName($userData['lastName'])
                ->setAddress($userData['address'])
                ->setNumeroTel($userData['phoneNumber'])
                ->setTypeUser('NO_CUSTOMER'); // Type d'utilisateur

            $this->em->persist($user);
            $this->em->flush(); // Enregistre immédiatement l'utilisateur
        }

        return $user;
    }

    /**
     * Traite les détails de la commande et calcule le prix total net.
     *
     * @param array $orderData Les données de la commande.
     * @param Command $command La commande associée.
     * @return float Le prix total net de la commande.
     */
    private function processOrderDetails(array $orderData, Command $command): float
    {
        $netTotalPrice = 0;

        foreach ($orderData as $item) {
            $product = $this->productRepository->find($item['productId']);
            if (!$product) {
                throw new \Exception("Produit non trouvé : " . $item['productId']);
            }

            if ($product->getStock() < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour le produit : " . $product->getName());
            }

            // Crée un détail de commande
            $detailsCommand = new DetailsCommand();
            $totalPrice = $item['quantity'] * $product->getPrice();

            $detailsCommand->setIdProduit($product)
                ->setIdCommand($command)
                ->setQuantity($item['quantity'])
                ->setTotalPrice($totalPrice);

            $this->em->persist($detailsCommand);

            // Met à jour le stock du produit
            $product->setStock($product->getStock() - $item['quantity']);
            $this->em->persist($product);

            $netTotalPrice += $totalPrice;
        }

        return round($netTotalPrice, 2);
    }

    public function approveOrder(int $commandId, int $adminId): array
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $command = $this->em->getRepository(Command::class)->find($commandId);
        if (!$command) {
            throw new \Exception("Commande non trouvée.");
        }

        $command->setStatutCom('ACCEPTED'); // Approve the command
        $this->em->flush();

        return [
            'message' => 'Commande approuvée avec succès',
            'commandId' => $command->getId(),
        ];
    }
    public function rejectOrder(int $commandId, int $adminId): array
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $command = $this->em->getRepository(Command::class)->find($commandId);
        if (!$command) {
            throw new \Exception("Commande non trouvée.");
        }

        $command->setStatutCom('REJECTED'); // Approve the command
        $this->em->flush();

        return [
            'message' => 'Commande rejetée avec succès',
            'commandId' => $command->getId(),
        ];
    }

    public function getAllOrders(): array
    {
        return $this->em->getRepository(Command::class)->findAll(); // Récpérer toutes les commandes
    }

    
    public function calculateTotalRevenue(): float
    {
    return $this->em->createQueryBuilder()
        ->select('SUM(dc.totalPrice) as totalRevenue')
        ->from(DetailsCommand::class, 'dc')
        ->getQuery()
        ->getSingleScalarResult();
    }
}

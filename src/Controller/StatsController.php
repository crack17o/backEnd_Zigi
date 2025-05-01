<?php

namespace App\Controller;

use App\Service\CommandService;
use App\Service\CommentService;
use App\Service\ProductService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatsController extends AbstractController
{
    #[Route('/api/stats', name: 'get_stats', methods: ['GET'])]
    public function getStats(
        UserService $userService,
        CommentService $commentService,
        CommandService $commandService,
        ProductService $productService
    ): JsonResponse {
        $totalUsers = $userService->countTotalUsers();
        $usersByType = $userService->countUsersByType();
        $validatedAccounts = $userService->countValidatedAccounts();
        $totalComments = $commentService->countTotalComments();
        $totalRevenue = $commandService->calculateTotalRevenue();
        $topSellingProducts = $productService->getTopSellingProducts(5); // Top 5 produits
        $topSellingCategories = $productService->getTopSellingCategories(5); // Top 5 catégories*/

        return $this->json([
            'totalUsers' => $totalUsers,
            'usersByType' => $usersByType,
            'validatedAccounts' => $validatedAccounts,
            'totalComments' => $totalComments,
            'totalRevenue' => $totalRevenue,
            'topSellingProducts' => $topSellingProducts,
            'topSellingCategories' => $topSellingCategories,
        ]);
    }
}

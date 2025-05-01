<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    /**
     * Ajoute une nouvelle catégorie.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant l'ajout.
     * @param Request $request La requête contenant les données de la catégorie.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/addCategory/{idAdmin}', name: 'app_newCategory', methods: ['POST'])]
    public function executeAddCategory(int $idAdmin,
    Request $request,ProductService $productService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $category = $productService->addCategory($data,$idAdmin);

            return new JsonResponse(['message' => 'Categorie ajoutée avec succès',
                                     'Categorie'=>[
                                        'id'=>$category->getId(),
                                        'name'=>$category->getName()
                                     ]],JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()],JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Ajoute un nouveau produit.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant l'ajout.
     * @param Request $request La requête contenant les données du produit.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/addProduct/{idAdmin}', name: 'app_newProduct', methods: ['POST'])]
    public function executeAddProduct(int $idAdmin, Request $request, ProductService $productService): JsonResponse
    {
        try {
            // Récupération des données envoyées dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Vérifie si les données sont un tableau de produits
            if (!isset($data['products']) || !is_array($data['products'])) {
                throw new \Exception("La clé 'products' doit être un tableau.");
            }
            
            $addedProducts = [];
            foreach ($data['products'] as $productData) {
                // Ajout de chaque produit
                $product = $productService->addProduct($productData, $idAdmin);
                $addedProducts[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'Categorie' => $product->getIdCat()->getName()
                ];
            }
    
            // Réponse avec la liste des produits ajoutés
            return new JsonResponse([
                'message' => 'Produits ajoutés avec succès',
                'Produits' => $addedProducts
            ], JsonResponse::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
                  
    /**
     * Modifie un produit existant.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant la modification.
     * @param int $productId L'ID du produit à modifier.
     * @param Request $request La requête contenant les nouvelles données du produit.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/updateProduct/{idAdmin}/{productId}', name: 'app_updateProduct', methods: ['PUT'])]
    public function executeUpdateProduct(int $idAdmin, int $productId, Request $request, ProductService $productService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $product = $productService->updateProduct($productId, $data, $idAdmin);

            return new JsonResponse(['message' => 'Produit modifié avec succès',
                                     'Produit'=>[
                                        'id'=>$product->getId(),
                                        'name'=>$product->getName(),
                                        'price' =>$product->getPrice(),
                                        'Categorie' =>$product->getIdCat()->getName()
                                     ]], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Supprime un produit existant.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant la suppression.
     * @param int $productId L'ID du produit à supprimer.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/deleteProduct/{idAdmin}/{productId}', name: 'app_deleteProduct', methods: ['DELETE'])]
    public function executeDeleteProduct(int $idAdmin, int $productId, ProductService $productService): JsonResponse
    {
        try {
            $productService->deleteProduct($productId, $idAdmin);
            return new JsonResponse(['message' => 'Produit supprimé avec succès'], JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Liste tous les produits.
     *
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant la liste des produits.
     */
    #[Route('/api/orders/listProducts', name: 'app_listProducts', methods: ['GET'])]
    public function executeListProducts(ProductService $productService): JsonResponse
    {
        $products = $productService->listProducts();
        return new JsonResponse($products
        , JsonResponse::HTTP_OK);
    }

    /**
     * Modifie une catégorie existante.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant la modification.
     * @param int $categoryId L'ID de la catégorie à modifier.
     * @param Request $request La requête contenant les nouvelles données de la catégorie.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/updateCategory/{idAdmin}/{categoryId}', name: 'app_updateCategory', methods: ['PUT'])]
    public function executeUpdateCategory(int $idAdmin, int $categoryId, Request $request, ProductService $productService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $category = $productService->updateCategory($categoryId, $data, $idAdmin);

            return new JsonResponse(['message' => 'Catégorie modifiée avec succès',
                                     'Categorie'=>[
                                        'id'=>$category->getId(),
                                        'name'=>$category->getName()
                                     ]], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Supprime une catégorie existante.
     *
     * @param int $idAdmin L'ID de l'administrateur effectuant la suppression.
     * @param int $categoryId L'ID de la catégorie à supprimer.
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant le message de succès ou d'erreur.
     */
    #[Route('/api/orders/deleteCategory/{idAdmin}/{$categoryId}', name: 'app_deleteCategory', methods: ['DELETE'])]
    public function executeDeleteCategory(int $idAdmin, int $categoryId, ProductService $productService): JsonResponse
    {
        try {
            $productService->deleteCategory($categoryId, $idAdmin);
            return new JsonResponse(['message' => 'Catégorie supprimée avec succès'], JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Liste toutes les catégories.
     *
     * @param ProductService $productService Le service de produit.
     * @return JsonResponse La réponse JSON contenant la liste des catégories.
     */
    #[Route('/api/orders/listCategories', name: 'app_listCategories', methods: ['GET'])]
    public function executeListCategories(ProductService $productService): JsonResponse
    {
        $categories = $productService->listCategories();
        return new JsonResponse($categories, JsonResponse::HTTP_OK);
    }

     
    #[Route('/api/products/{productId}/update-stock', name : "update_product_stock", methods : ['POST'])]
     
    public function updateProductStock(int $productId, Request $request, ProductService $productService ): JsonResponse
    {
        try {
            // Récupérer les données du corps de la requête JSON
            $data = json_decode($request->getContent(), true);
            $quantity = $data['quantity'] ?? null;
            $adminId = $data['adminId'] ?? null;

            if ($quantity === null || $adminId === null) {
                throw new \Exception("Les paramètres 'quantity' et 'adminId' sont requis.");
            }

            // Appeler le service pour mettre à jour le stock du produit
            $result = $productService->updateProductStock($productId, $quantity, $adminId);

            return $this->json([
                'success' => true,
                'message' => $result['message'],
                'productId' => $result['productId'],
            ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs et envoyer une réponse appropriée
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

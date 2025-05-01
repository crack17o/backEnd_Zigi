<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Categorie;
use App\Entity\DetailsCommand;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{


    private EntityManagerInterface $em;
    private ProductRepository $productRepository;
    private CommandRepository $commandRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        CommandRepository $commandRepository
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->commandRepository = $commandRepository;
    }

    /**
     * Ajoute un nouveau produit.
     *
     * @param array $productData Les données du produit à ajouter.
     * @param int $adminId L'ID de l'administrateur effectuant l'ajout.
     * @return Product Le produit ajouté.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si le produit existe déjà.
     */
    public function addProduct(array $productData, int $adminId): Product
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $product = new Product();
        $product->setName($productData['name'])
            ->setDescription($productData['description'] ?? '')
            ->setPrice($productData['price'])
            ->setStock($productData['stock']);

        $category = $this->em->getRepository(Categorie::class)->find($productData['categoryId']);
        if (!$category) {
            throw new \Exception("La catégorie spécifiée n'existe pas.");
        }
        $product->setIdCat($category); // Assignez l'objet catégorie

        $existingProduct = $this->em->getRepository(Product::class)->findOneBy(['name' => $productData['name']]);
        if ($existingProduct) {
            throw new \Exception("Le Produit ".$existingProduct->getName()." spécifié existe déjà.");
        }

        $product = $this->productRepository->addProduct($product);

        return $product;
    }

    /**
     * Modifie un produit existant.
     *
     * @param int $productId L'ID du produit à modifier.
     * @param array $productData Les nouvelles données du produit.
     * @param int $adminId L'ID de l'administrateur effectuant la modification.
     * @return Product Le produit modifié.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si le produit n'existe pas.
     */
    public function updateProduct(int $productId, array $productData, int $adminId): Product
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \Exception("Le produit spécifié n'existe pas.");
        }

        $product->setName($productData['name'])
            ->setDescription($productData['description'] ?? '')
            ->setPrice($productData['price'])
            ->setStock($productData['stock']);

        $this->em->flush(); // Enregistrer les modifications

        return $product;
    }

    /**
     * Supprime un produit existant.
     *
     * @param int $productId L'ID du produit à supprimer.
     * @param int $adminId L'ID de l'administrateur effectuant la suppression.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si le produit n'existe pas.
     */
    public function deleteProduct(int $productId, int $adminId): void
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \Exception("Le produit spécifié n'existe pas.");
        }

        $this->em->remove($product);
        $this->em->flush(); // Enregistrer la suppression
    }

    /**
     * Liste tous les produits.
     *
     * @return array Un tableau de tous les produits.
     */
    public function listProducts(): array
    {
        $products = $this->em->getRepository(Product::class)->findAll(); 
        
        $myProArray = []; // Initialisation du tableau
        foreach ($products as $pro) {
            $photos = []; // Tableau pour stocker les photos liées au produit
            foreach ($pro->getSendPhotos() as $photo) {
                $photos[] = $photo->getUrl(); // On suppose que `getUrl()` donne l'URL de la photo
            }
    
            $myProArray[] = [
                'id' => $pro->getId(),
                'categorie' => $pro->getIdCat()->getName(),
                'name' => $pro->getName(),
                'description' => $pro->getDescription(),
                'prix' => $pro->getPrice(),
                'photos' => $photos // Ajouter les photos à la réponse
            ];
        }
    
        return $myProArray;
    }
    

    /**
     * Ajoute une nouvelle catégorie.
     *
     * @param array $categoryData Les données de la catégorie à ajouter.
     * @param int $adminId L'ID de l'administrateur effectuant l'ajout.
     * @return Categorie La catégorie ajoutée.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si la catégorie existe déjà.
     */
    public function addCategory(array $categoryData, int $adminId): Categorie
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $existingcategory = $this->em->getRepository(Categorie::class)->findOneBy(['name' => $categoryData['name']]);
        if ($existingcategory) {
            throw new \Exception("La catégorie spécifiée existe déjà.");
        }

        $category = new Categorie();
        $category->setName($categoryData['name'])
            ->setDescription($categoryData['description'] ?? '');

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    /**
     * Modifie une catégorie existante.
     *
     * @param int $categoryId L'ID de la catégorie à modifier.
     * @param array $categoryData Les nouvelles données de la catégorie.
     * @param int $adminId L'ID de l'administrateur effectuant la modification.
     * @return Categorie La catégorie modifiée.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si la catégorie n'existe pas.
     */
    public function updateCategory(int $categoryId, array $categoryData, int $adminId): Categorie
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $category = $this->em->getRepository(Categorie::class)->find($categoryId);
        if (!$category) {
            throw new \Exception("La catégorie spécifiée n'existe pas.");
        }

        $category->setName($categoryData['name'])
            ->setDescription($categoryData['description'] ?? '');

        $this->em->flush(); // Enregistrer les modifications

        return $category;
    }

    /**
     * Supprime une catégorie existante.
     *
     * @param int $categoryId L'ID de la catégorie à supprimer.
     * @param int $adminId L'ID de l'administrateur effectuant la suppression.
     * @throws \Exception Si l'utilisateur n'est pas un administrateur ou si la catégorie n'existe pas.
     */
    public function deleteCategory(int $categoryId, int $adminId): void
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $category = $this->em->getRepository(Categorie::class)->find($categoryId);
        if (!$category) {
            throw new \Exception("La catégorie spécifiée n'existe pas.");
        }

        $this->em->remove($category);
        $this->em->flush(); // Enregistrer la suppression
    }

    /**
     * Liste toutes les catégories.
     *
     * @return array Un tableau de toutes les catégories.
     */
    public function listCategories(): array
    {
        $category = $this->em->getRepository(Categorie::class)->findAll();

        $myCategorie = [];
        foreach ($category as $cat){
            $myCategorie[] =[
                'id' => $cat->getId(),
                'name' => $cat->getName(),
                'description' => $cat->getDescription()
            ];
        }
        return $myCategorie; // Retourner toutes les catégories

    }

    public function removeProduct(int $productId, int $adminId): array
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \Exception("Produit non trouvé.");
        }

        $this->productRepository->removeProduct($product);

        return [
            'message' => 'Produit retiré avec succès',
            'productId' => $productId,
        ];
    }

    public function updateProductStock(int $productId, int $quantity, int $adminId): array
    {
        $admin = $this->em->getRepository(User::class)->find($adminId);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $this->productRepository->updateStock($productId, $quantity);

        return [
            'message' => 'Stock mis à jour avec succès',
            'productId' => $productId,
        ];
    }



    public function getTopSellingProducts(int $limit): array
    {
        $results = $this->em->createQueryBuilder()
            ->select('p.id, p.name, SUM(dc.quantity) as totalSold')
            ->from(Product::class, 'p')
            ->join(DetailsCommand::class, 'dc', 'WITH', 'dc.id_Produit = p.id')
            ->groupBy('p.id, p.name')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Transformer les résultats en un tableau associatif
        return array_map(function ($result) {
            return [
                'id' => $result['id'],
                'name' => $result['name'],
                'totalSold' => (int)$result['totalSold'],
            ];
        }, $results);
    }

    public function getTopSellingCategories(int $limit): array
    {
        $results = $this->em->createQueryBuilder()
            ->select('c.id, c.name, SUM(dc.quantity) as totalSold')
            ->from(Categorie::class, 'c')
            ->join(Product::class, 'p', 'WITH', 'p.idCat = c.id')
            ->join(DetailsCommand::class, 'dc', 'WITH', 'dc.id_Produit = p.id')
            ->groupBy('c.id, c.name')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Transformer les résultats en un tableau associatif
        return array_map(function ($result) {
            return [
                'id' => $result['id'],
                'name' => $result['name'],
                'totalSold' => (int)$result['totalSold'],
            ];
        }, $results);
    }
}

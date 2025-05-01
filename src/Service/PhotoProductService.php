<?php

namespace App\Service;

use App\Entity\PhotoProduct;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PhotoProductService
{

    private ImgBBService $imgBBService;
    private EntityManagerInterface $em;

    public function __construct(ImgBBService $cloud, EntityManagerInterface $em)
    {
        $this->imgBBService = $cloud;
        $this->em = $em;
    }

    public function sendPhotoProduct($idAdmin,$idProduct, $imageFile): array
    {
        $admin = $this->em->getRepository(User::class)->find($idAdmin);
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Accès refusé. L'utilisateur n'est pas un administrateur.");
        }

        $pro = $this->em->getRepository(Product::class)->find($idProduct);

        if (!$pro) {
            throw new \Exception('Ce produit n\'existe pas');
        }

        if (!$imageFile) {
            throw  new \Exception('Aucune image envoyée');
        }

        // Vérification du type de fichier et de la taille
        if ($imageFile->getSize() > 32 * 1024 * 1024) { // 32 Mo
            throw new \Exception('L\'image doit être inférieure à 32 Mo.');
        }

       /* if (strpos($imageFile->getMimeType(), 'video/') === 0) {
            throw new \Exception('Le fichier envoyé est une vidéo, veuillez envoyer une image.');
        }*/
        
        
        // Upload l'image 
        $photoUrl = $this->imgBBService->uploadImage($imageFile->getPathname());

        $photoProduct = new PhotoProduct();
        $photoProduct->setIdUser($pro); //C'est idProduit juste que j'avais ecrit idUser
        $photoProduct->setUrl($photoUrl);

        $this->em->persist($photoProduct);
        $this->em->flush();

            return [
            'id' => $pro->getId(),
            '$photoUrl' =>$photoUrl,
            'categorie' => $pro->getIdCat()->getName(),
            'name' => $pro->getName(),
            'description' => $pro->getDescription(),
            'prix' => $pro->getPrice()
        ];
    }
}

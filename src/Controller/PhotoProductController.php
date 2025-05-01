<?php

namespace App\Controller;

use App\Service\PhotoProductService;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Attribute\Route;

class PhotoProductController extends AbstractController
{
    #[Route('/api/orders/addPhotoProduct/{idAdmin}/{idProduct}', name: 'app_newpho', methods: ['POST'])]
    public function executeAddPhoto(int $idAdmin,int $idProduct,
    HttpFoundationRequest $request,PhotoProductService $photoProductService): JsonResponse
    {
        try {

            $file = $request->files->get('photo');
            
            $data = $photoProductService->sendPhotoProduct($idAdmin,$idProduct,$file);

            return new JsonResponse(['message' => 'Image inserée avec succés',
                                     'data'=> $data
                                     ],JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()],JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}

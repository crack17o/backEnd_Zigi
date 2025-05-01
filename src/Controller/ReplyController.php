<?php

namespace App\Controller;

use App\Service\ReplyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ReplyController extends AbstractController
{
    #[Route('/api/comment/response/{idAdmin}/{idComment}', name: 'app_reply',methods:['POST'])]
    public function addReply(int $idAdmin,int $idComment,Request $request,ReplyService $replyService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            
            $response = $replyService->addReply($idAdmin, $idComment, $data);

            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'id' => $response->getId(),
                    'response' => $response->getResponse(),
                    'replyAt' => $response->getReplyAt()->format('Y-m-d H:i:s'),
                ],
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}

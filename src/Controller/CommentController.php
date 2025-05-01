<?php

namespace App\Controller;

use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/api/comment/create/{idUser}', name: 'new_comment',methods:['POST'])]
    public function newComment(int $idUser,Request $request,CommentService $commentService): JsonResponse
    {

       try {
            
             $data = json_decode($request->getContent(),true);
             $myNewComm = $commentService->postComment($idUser,$data);

             return new JsonResponse(['success'=>true,
                                      'data' =>[
                                        'idComment' => $myNewComm->getId(),
                                        'User' =>[
                                            'idUser'=> $myNewComm->getIdUser()->getId(), 
                                            'Names' => $myNewComm->getIdUser()->getFirstName() . ' ' . $myNewComm->getIdUser()->getLastName(),
                                        ],
                                        'description' => $myNewComm->getDescription(),
                                        'postAt' => $myNewComm->getPostAt(),

                                      ]],JsonResponse::HTTP_CREATED);
       } catch (\Exception $e) {
            return $this->json(['error'=>$e->getMessage()],JsonResponse::HTTP_BAD_REQUEST);
       }
    }

    #[Route('/api/comment/list', name: 'list_comment',methods:['GET'])]
    public function listComment(CommentService $commentService): JsonResponse
    {
        try {
            $myComments = $commentService->listComments();
    
            $data = [];
    
            foreach ($myComments as $comment) {
                // Préparer les réponses liées au commentaire
                $replies = [];
                foreach ($comment->getReplies() as $reply) {
                    $replies[] = [
                        'id' => $reply->getId(),
                        'response' => $reply->getResponse(),
                        'replyAt' => $reply->getReplyAt()->format('Y-m-d H:i:s'),
                        'idAdmin' => $reply->getIdAdmin()?->getId(),
                        'adminName' => $reply->getIdAdmin()?->getFirstName() . ' ' . $reply->getIdAdmin()?->getLastName(),
                    ];
                }
    
                // Préparer les données du commentaire
                $data[] = [
                    'id_Com' => $comment->getId(),
                    'User' => [
                        'id' => $comment->getIdUser()->getId(),
                        'email' => $comment->getIdUser()->getEmail(),
                        'firstName' => $comment->getIdUser()->getFirstName(),
                        'lastName' => $comment->getIdUser()->getLastName(),
                    ],
                    'postAt' => $comment->getPostAt()->format('Y-m-d H:i:s'),
                    'description' => $comment->getDescription(),
                    'replies' => $replies, // Inclure toutes les réponses ici
                ];
            }
    
            return new JsonResponse(['data' => $data], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    
}

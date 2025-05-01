<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;

class CommentService{

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function postComment(int $idUser,$dtcomment) : Comment{
        
        //Verifier si le user existe
        $existingUser = $this->em->getRepository(User::class)->find($idUser);

        if(!$existingUser || $existingUser->getTypeUser()=== 'NO_CUSTOMER'){
             throw new \Exception('L\'utilisateur n\'existe pas');
        }

        if(!isset($dtcomment['description'])){
            throw new \Exception('Ecrivez un commentaire svp');
        }

        $comment = new Comment();
        $comment->setIdUser($existingUser);

        $now = new \DateTime(); // Récupère la date actuelle du serveur
        $comment->setPostAt($now);
        $comment->setDescription($dtcomment['description']);

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }


    public function listComments(): array{
        return $this->em->getRepository(Comment::class)->findAll();
    }

    public function countTotalComments(): int
    {
        return $this->em->getRepository(Comment::class)->count([]);
    }
}
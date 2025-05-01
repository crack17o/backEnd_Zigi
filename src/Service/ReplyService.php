<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Reply;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ReplyService{

    private EntityManagerInterface $em;

    public  function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    public function addReply(int $idAdmin, int $idComment, array $data): Reply
    {
        // Validate admin user
        $admin = $this->em->getRepository(User::class)->find($idAdmin);
        if (!$admin) {
            throw new \Exception("Cet Administrateur n'existe pas.");
        }

        if (!in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \Exception("Cet utilisateur n'est pas Administrateur.");
        }

        // Validate comment
        $comment = $this->em->getRepository(Comment::class)->find($idComment);
        if (!$comment) {
            throw new \Exception("Le commentaire n'existe pas.");
        }

        // Validate response text
        if (!isset($data['response']) || empty($data['response'])) {
            throw new \Exception("Placez une reponse svp.");
        }

        // Create reply
        $reply = new Reply();
        $reply->setIdAdmin($admin);
        $reply->setIdComment($comment);
        $reply->setResponse($data['response']);
        $reply->setReplyAt(new \DateTime());

        // Persist and flush
        $this->em->persist($reply);
        $this->em->flush();

        return $reply;
    }
}
<?php

namespace App\Entity;

use App\Repository\ReplyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReplyRepository::class)]
class Reply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $response = null;

    #[ORM\ManyToOne(inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idAdmin = null;

    #[ORM\ManyToOne(inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comment $idComment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $replyAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): static
    {
        $this->response = $response;

        return $this;
    }

    public function getIdAdmin(): ?User
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(?User $idAdmin): static
    {
        $this->idAdmin = $idAdmin;

        return $this;
    }

    public function getIdComment(): ?Comment
    {
        return $this->idComment;
    }

    public function setIdComment(?Comment $idComment): static
    {
        $this->idComment = $idComment;

        return $this;
    }

    public function getReplyAt(): ?\DateTimeInterface
    {
        return $this->replyAt;
    }

    public function setReplyAt(\DateTimeInterface $replyAt): static
    {
        $this->replyAt = $replyAt;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\PhotoProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoProductRepository::class)]
class PhotoProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'sendPhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $idProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getIdUser(): ?Product
    {
        return $this->idProduct;
    }

    public function setIdUser(?Product $idUser): static
    {
        $this->idProduct = $idUser;

        return $this;
    }
}

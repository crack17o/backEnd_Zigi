<?php

namespace App\Entity;

use App\Repository\DetailsCommandRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsCommandRepository::class)]
class DetailsCommand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detailsCommands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $id_Produit = null;

    #[ORM\ManyToOne(inversedBy: 'detailsCommands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Command $id_Command = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice;

    #[ORM\Column]
    private int $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProduit(): ?Product
    {
        return $this->id_Produit;
    }

    public function setIdProduit(?Product $id_Produit): static
    {
        $this->id_Produit = $id_Produit;

        return $this;
    }

    public function getIdCommand(): ?Command
    {
        return $this->id_Command;
    }

    public function setIdCommand(?Command $id_Command): static
    {
        $this->id_Command = $id_Command;

        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $idCat = null;

    /**
     * @var Collection<int, DetailsCommand>
     */
    #[ORM\OneToMany(targetEntity: DetailsCommand::class, mappedBy: 'id_Produit')]
    private Collection $detailsCommands;

    /**
     * @var Collection<int, SendPhoto>
     */
    #[ORM\OneToMany(targetEntity: PhotoProduct::class, mappedBy: 'idProduct')]
    private Collection $sendPhotos;

    public function __construct()
    {
        $this->detailsCommands = new ArrayCollection();
        $this->sendPhotos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIdCat(): ?Categorie
    {
        return $this->idCat;
    }

    public function setIdCat(?Categorie $idCat): static
    {
        $this->idCat = $idCat;

        return $this;
    }

    /**
     * @return Collection<int, DetailsCommand>
     */
    public function getDetailsCommands(): Collection
    {
        return $this->detailsCommands;
    }

    public function addDetailsCommand(DetailsCommand $detailsCommand): static
    {
        if (!$this->detailsCommands->contains($detailsCommand)) {
            $this->detailsCommands->add($detailsCommand);
            $detailsCommand->setIdProduit($this);
        }

        return $this;
    }

    public function removeDetailsCommand(DetailsCommand $detailsCommand): static
    {
        if ($this->detailsCommands->removeElement($detailsCommand)) {
            // set the owning side to null (unless already changed)
            if ($detailsCommand->getIdProduit() === $this) {
                $detailsCommand->setIdProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhotoProduct>
     */
    public function getSendPhotos(): Collection
    {
        return $this->sendPhotos;
    }

    public function addSendPhoto(PhotoProduct $sendPhoto): static
    {
        if (!$this->sendPhotos->contains($sendPhoto)) {
            $this->sendPhotos->add($sendPhoto);
            $sendPhoto->setIdUser($this);
        }

        return $this;
    }

    public function removeSendPhoto(PhotoProduct $sendPhoto): static
    {
        if ($this->sendPhotos->removeElement($sendPhoto)) {
            // set the owning side to null (unless already changed)
            if ($sendPhoto->getIdUser() === $this) {
                $sendPhoto->setIdUser(null);
            }
        }

        return $this;
    }
}

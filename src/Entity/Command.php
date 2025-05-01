<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommand = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2,nullable: true)]
    private float $amount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = "CDF";
   
    #[ORM\Column(length: 255)]
    private ?string $statut_com = null;

    #[ORM\ManyToOne(inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    /**
     * @var Collection<int, DetailsCommand>
     */
    #[ORM\OneToMany(targetEntity: DetailsCommand::class, mappedBy: 'id_Command')]
    private Collection $detailsCommands;

    public function __construct()
    {
        $this->detailsCommands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommand(): ?\DateTimeInterface
    {
        return $this->dateCommand;
    }

    public function setDateCommand(\DateTimeInterface $dateCommand): static
    {
        $this->dateCommand = $dateCommand;

        return $this;
    }

    public function getStatutCom(): ?string
    {
        return $this->statut_com;
    }

    public function setStatutCom(?string $statut_com): static
    {
        $this->statut_com = $statut_com;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

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
            $detailsCommand->setIdCommand($this);
        }

        return $this;
    }

    public function removeDetailsCommand(DetailsCommand $detailsCommand): static
    {
        if ($this->detailsCommands->removeElement($detailsCommand)) {
            // set the owning side to null (unless already changed)
            if ($detailsCommand->getIdCommand() === $this) {
                $detailsCommand->setIdCommand(null);
            }
        }

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}

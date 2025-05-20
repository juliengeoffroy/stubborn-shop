<?php

namespace App\Entity;

use App\Repository\SweatshirtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SweatshirtRepository::class)]
class Sweatshirt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isFeatured = false;

    #[ORM\Column(type: 'integer')]
    private int $stockXS = 2;

    #[ORM\Column(type: 'integer')]
    private int $stockS = 2;

    #[ORM\Column(type: 'integer')]
    private int $stockM = 2;

    #[ORM\Column(type: 'integer')]
    private int $stockL = 2;

    #[ORM\Column(type: 'integer')]
    private int $stockXL = 2;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'sweatshirt')]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
    public function isFeatured(): bool { return $this->isFeatured; }
    public function setIsFeatured(bool $isFeatured): self { $this->isFeatured = $isFeatured; return $this; }

    public function getStockXS(): int { return $this->stockXS; }
    public function setStockXS(int $stock): self { $this->stockXS = $stock; return $this; }

    public function getStockS(): int { return $this->stockS; }
    public function setStockS(int $stock): self { $this->stockS = $stock; return $this; }

    public function getStockM(): int { return $this->stockM; }
    public function setStockM(int $stock): self { $this->stockM = $stock; return $this; }

    public function getStockL(): int { return $this->stockL; }
    public function setStockL(int $stock): self { $this->stockL = $stock; return $this; }

    public function getStockXL(): int { return $this->stockXL; }
    public function setStockXL(int $stock): self { $this->stockXL = $stock; return $this; }

    public function getOrderItems(): Collection { return $this->orderItems; }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setSweatshirt($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getSweatshirt() === $this) {
                $orderItem->setSweatshirt(null);
            }
        }
        return $this;
    }
}

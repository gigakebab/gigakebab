<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $delivery_at;

    #[ORM\Column(type: 'datetime')]
    private $oder_at;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\ManyToOne(targetEntity: Address::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $address;

    #[ORM\OneToMany(mappedBy: 'order_product', targetEntity: ProductLine::class)]
    private $product_line;

    public function __construct()
    {
        $this->product_line = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeliveryAt(): ?\DateTimeInterface
    {
        return $this->delivery_at;
    }

    public function setDeliveryAt(\DateTimeInterface $delivery_at): self
    {
        $this->delivery_at = $delivery_at;

        return $this;
    }

    public function getOderAt(): ?\DateTimeInterface
    {
        return $this->oder_at;
    }

    public function setOderAt(\DateTimeInterface $oder_at): self
    {
        $this->oder_at = $oder_at;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, ProductLine>
     */
    public function getProductLine(): Collection
    {
        return $this->product_line;
    }

    public function addProductLine(ProductLine $productLine): self
    {
        if (!$this->product_line->contains($productLine)) {
            $this->product_line[] = $productLine;
            $productLine->setOrderProduct($this);
        }

        return $this;
    }

    public function removeProductLine(ProductLine $productLine): self
    {
        if ($this->product_line->removeElement($productLine)) {
            // set the owning side to null (unless already changed)
            if ($productLine->getOrderProduct() === $this) {
                $productLine->setOrderProduct(null);
            }
        }

        return $this;
    }
}

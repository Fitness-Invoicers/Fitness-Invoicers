<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $discountAmountOnItem = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $discountAmountOnTotal = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $taxes = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Quote $quote = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?invoice $invoices = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDiscountAmountOnItem(): ?float
    {
        return $this->discountAmountOnItem;
    }

    public function setDiscountAmountOnItem(?float $discountAmountOnItem): static
    {
        $this->discountAmountOnItem = $discountAmountOnItem;

        return $this;
    }

    public function getDiscountAmountOnTotal(): ?float
    {
        return $this->discountAmountOnTotal;
    }

    public function setDiscountAmountOnTotal(?float $discountAmountOnTotal): static
    {
        $this->discountAmountOnTotal = $discountAmountOnTotal;

        return $this;
    }

    public function getTaxes(): ?float
    {
        return $this->taxes;
    }

    public function setTaxes(?float $taxes): static
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getInvoices(): ?invoice
    {
        return $this->invoices;
    }

    public function setInvoices(?invoice $invoices): static
    {
        $this->invoices = $invoices;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->getProduct()->getPrice() * $this->getQuantity() * (1 + $this->getTaxes() / 100);
    }

    public function getTotalAmountWithoutTaxes(): float
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    public function getDiscountAmount(): float
    {
        return $this->getDiscountAmountOnItem() * $this->getQuantity();
    }

    public function isValid(): bool
    {
        return $this->getProduct() !== null
            && $this->getQuantity() > 0
            && $this->getTaxes() >= 0
            && $this->getDiscountAmountOnItem() >= 0
            && $this->getDiscountAmountOnTotal() >= 0
            && $this->getTotalAmount() >= 0
            && $this->getTotalAmountWithoutTaxes() >= 0
            && $this->getDiscountAmount() >= 0
            && $this->getInvoices() !== null || $this->getQuote() !== null;
    }

    public function getIsNotValidErrors(): array
    {
        $errors = [];
        if ($this->getProduct() === null) {
            $errors[] = 'product.is.required';
        }

        if ($this->getQuantity() <= 0) {
            $errors[] = 'item.quantity.must.be.greater.than.0';
        }

        if ($this->getTaxes() < 0) {
            $errors[] = 'item.taxes.must.be.greater.or.equal.to.0';
        }

        if ($this->getDiscountAmountOnItem() < 0) {
            $errors[] = 'item.discount.amount.on.item.must.be.greater.or.equal.to.0';
        }

        if ($this->getDiscountAmountOnTotal() < 0) {
            $errors[] = 'item.discount.amount.on.total.must.be.greater.or.equal.to.0';
        }

        if ($this->getTotalAmount() < 0) {
            $errors[] = 'item.total.amount.must.be.greater.or.equal.to.0';
        }

        if ($this->getTotalAmountWithoutTaxes() < 0) {
            $errors[] = 'item.total.amount.without.taxes.must.be.greater.or.equal.to.0';
        }

        if ($this->getDiscountAmount() < 0) {
            $errors[] = 'item.discount.amount.must.be.greater.or.equal.to.0';
        }

        if ($this->getInvoices() === null || $this->getQuote() === null) {
            $errors[] = 'item.invoice.or.quote.is.required';
        }

        return $errors;
    }

}

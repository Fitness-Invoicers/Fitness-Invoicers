<?php

namespace App\Builder;

use App\Entity\Company;
use App\Entity\Deposit;
use App\Entity\Item;
use App\Entity\Quote;
use App\Enum\InvoiceStatusEnum;
use App\Factory\InvoiceFactory;

class InvoiceBuilder implements BuilderInterface
{
    private ?float $discountAmount = null;
    private ?float $discountPercent = null;
    private ?InvoiceStatusEnum $status = null;
    private ?Quote $quote = null;
    private ?Company $company = null;

    /**
     * @var array<Item>|null
     */
    private ?array $items = null;

    /**
     * @var array<Deposit>|null
     */
    private ?array $deposits = null;

    public function build(bool $persist = true): object
    {
        $invoice = InvoiceFactory::createOne(array_filter([
            'discountAmount' => $this->discountAmount,
            'discountPercent' => $this->discountPercent,
            'status' => $this->status,
            'quote' => $this->quote,
            'items' => $this->items,
            'deposits' => $this->deposits,
        ]));

        if ($persist) {
            $invoice->save();
        }

        return $invoice->object();
    }

    public function withDiscountAmount(float $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function withDiscountPercent(float $discountPercent): self
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    public function withStatus(InvoiceStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function onQuote(Quote $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function onCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @param array<Item> $items
     */
    public function withItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param array<Deposit> $deposits
     */
    public function withDeposits(array $deposits): self
    {
        $this->deposits = $deposits;

        return $this;
    }

    public function addItem(Item $item): self
    {
        $this->items = [...($this->items ?? []), $item];

        return $this;
    }

    public function addDeposit(Deposit $deposit): self
    {
        $this->deposits = [...($this->deposits ?? []), $deposit];

        return $this;
    }
}

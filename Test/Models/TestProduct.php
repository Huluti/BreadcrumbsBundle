<?php

namespace Huluti\BreadcrumbsBundle\Test\Models;

class TestProduct
{
    private string $name;
    private float $price;
    private ?TestCategory $category;
    private ?TestSupplier $supplier;

    /** @phpstan-ignore-next-line   */
    private $nullValue;

    public function __construct(string $name, float $price, ?TestCategory $category = null, ?TestSupplier $supplier = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
        $this->supplier = $supplier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCategory(): ?TestCategory
    {
        return $this->category;
    }

    public function getSupplier(): ?TestSupplier
    {
        return $this->supplier;
    }

    public function getNullValue()
    {
        return $this->nullValue;
    }
}

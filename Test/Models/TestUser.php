<?php

namespace Huluti\BreadcrumbsBundle\Test\Models;

class TestUser
{
    private string $firstName;
    private string $lastName;
    private int $age;
    private ?TestProduct $favoriteProduct;

    public function __construct(string $firstName, string $lastName, int $age, ?TestProduct $favoriteProduct = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->favoriteProduct = $favoriteProduct;
    }

    public function __toString(): string
    {
        return sprintf('TestUser: %s %s', $this->firstName, $this->lastName);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getFavoriteProduct(): ?TestProduct
    {
        return $this->favoriteProduct;
    }
}

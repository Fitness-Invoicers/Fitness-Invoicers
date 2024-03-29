<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La rue est obligatoire")]
    private ?string $street = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function isValid() : bool
    {
        return $this->postal_code !== null
            && $this->city !== null
            && $this->country !== null
            && $this->street !== null;
    }

    public function getIsNotValidErrors() : array
    {
        $error = [];
        if ($this->postal_code === null) {
            $error[] = "Le code postal est obligatoire";
        }
        if ($this->city === null) {
            $error[] = "La ville est obligatoire";
        }
        if ($this->country === null) {
            $error[] = "Le pays est obligatoire";
        }
        if ($this->street === null) {
            $error[] = "La rue est obligatoire";
        }
        return $error;
    }

}

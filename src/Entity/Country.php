<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 2
     * )
     */
    private $abbr2;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 3
     * )
     */
    private $abbr3;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 3
     * )
     */
    private $code;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Vendor::class, mappedBy="country")
     */
    private $vendors;

    public function __construct()
    {
        $this->vendors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAbbr2(): ?string
    {
        return $this->abbr2;
    }

    public function setAbbr2(string $abbr2): self
    {
        $this->abbr2 = $abbr2;

        return $this;
    }

    public function getAbbr3(): ?string
    {
        return $this->abbr3;
    }

    public function setAbbr3(string $abbr3): self
    {
        $this->abbr3 = $abbr3;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at = null): self
    {
        $this->created_at = $created_at ? $created_at : new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at = null): self
    {
        $this->updated_at = $updated_at ? $updated_at : new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, Vendor>
     */
    public function getVendors(): Collection
    {
        return $this->vendors;
    }

    public function addVendor(Vendor $vendor): self
    {
        if (!$this->vendors->contains($vendor)) {
            $this->vendors[] = $vendor;
            $vendor->setCountry($this);
        }

        return $this;
    }

    public function removeVendor(Vendor $vendor): self
    {
        if ($this->vendors->removeElement($vendor)) {
            // set the owning side to null (unless already changed)
            if ($vendor->getCountry() === $this) {
                $vendor->setCountry(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=511)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *   max = 511
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Vendor::class, inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vendor;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Parameter::class, mappedBy="device", orphanRemoval=true)
     * @ORM\OrderBy({"prio" = "ASC"})
     */
    private $parameters;

    private $image;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
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

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(?Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

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
     * @return Collection<int, Parameter>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->setDevice($this);
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getDevice() === $this) {
                $parameter->setDevice(null);
            }
        }

        return $this;
    }

    public static function toArray($devices): array
    {
        if (!count($devices)) return [];
        $devicesArray = [];
        foreach($devices as $device) {
            $devicesArray[$device->getId()] = [
                'id' => $device->getId(),
                'name' => $device->getName(),
                'type' => $device->getType()->getName(),
                'vendor' => $device->getVendor()->getName(),
                'created_at' => $device->getCreatedAt(),
                'updated_at' => $device->getUpdatedAt(),
            ];
        }
        return $devicesArray;
    }
}

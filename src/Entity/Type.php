<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
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
     *   max = 255
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Device::class, mappedBy="type")
     */
    private $devices;

    /**
     * @ORM\OneToMany(targetEntity=AvParameter::class, mappedBy="type", orphanRemoval=true)
     */
    private $avParameters;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->avParameters = new ArrayCollection();
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
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setType($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getType() === $this) {
                $device->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AvParameter>
     */
    public function getAvParameters(): Collection
    {
        return $this->avParameters;
    }

    public function addAvParameter(AvParameter $avParameter): self
    {
        if (!$this->avParameters->contains($avParameter)) {
            $this->avParameters[] = $avParameter;
            $avParameter->setType($this);
        }

        return $this;
    }

    public function removeAvParameter(AvParameter $avParameter): self
    {
        if ($this->avParameters->removeElement($avParameter)) {
            // set the owning side to null (unless already changed)
            if ($avParameter->getType() === $this) {
                $avParameter->setType(null);
            }
        }

        return $this;
    }
}

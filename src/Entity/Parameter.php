<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParameterRepository::class)
 */
class Parameter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Device::class, inversedBy="parameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $device;

    /**
     * @ORM\ManyToOne(targetEntity=ParamOption::class)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     */
    private $custom_value;

    /**
     * @ORM\ManyToOne(targetEntity=AvParameter::class, inversedBy="parameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $av_parameter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getValue(): ?ParamOption
    {
        return $this->value;
    }

    public function setValue(?ParamOption $value): self
    {
        $this->value = $value;

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

    public function getCustomValue(): ?string
    {
        return $this->custom_value;
    }

    public function setCustomValue(?string $custom_value): self
    {
        $this->custom_value = $custom_value;

        return $this;
    }

    public function getAvParameter(): ?AvParameter
    {
        return $this->av_parameter;
    }

    public function setAvParameter(?AvParameter $av_parameter): self
    {
        $this->av_parameter = $av_parameter;

        return $this;
    }
}

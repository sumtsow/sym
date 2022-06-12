<?php

namespace App\Entity;

use App\Repository\AvParameterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvParameterRepository::class)
 */
class AvParameter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="avParameters")
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
     * @ORM\OneToMany(targetEntity=ParamOption::class, mappedBy="avParameter", orphanRemoval=true)
     */
    private $paramOptions;

    public function __construct()
    {
        $this->paramOptions = new ArrayCollection();
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

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, ParamOption>
     */
    public function getParamOptions(): Collection
    {
        return $this->paramOptions;
    }

    public function addParamOption(ParamOption $paramOption): self
    {
        if (!$this->paramOptions->contains($paramOption)) {
            $this->paramOptions[] = $paramOption;
            $paramOption->setAvParameter($this);
        }

        return $this;
    }

    public function removeParamOption(ParamOption $paramOption): self
    {
        if ($this->paramOptions->removeElement($paramOption)) {
            // set the owning side to null (unless already changed)
            if ($paramOption->getAvParameter() === $this) {
                $paramOption->setAvParameter(null);
            }
        }

        return $this;
    }
}

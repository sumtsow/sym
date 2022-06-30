<?php

namespace App\Entity;

use App\Repository\AvParameterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *   max = 255
     * )
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

    /**
     * @ORM\OneToMany(targetEntity=Parameter::class, mappedBy="av_parameter")
     */
    private $parameters;

    public function __construct()
    {
        $this->paramOptions = new ArrayCollection();
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
            $parameter->setAvParameter($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getAvParameter() === $this) {
                $parameter->setAvParameter(null);
            }
        }

        return $this;
    }

    public static function toArray($avParameters): array
    {
        if (!count($avParameters)) return [];
        $parametersArray = [];
        foreach($avParameters as $parameter) {
            $parametersArray[$parameter->getId()] = [
                'id' => $parameter->getId(),
                'name' => $parameter->getName(),
                'type' => $parameter->getType()->getName(),
                'created_at' => $parameter->getCreatedAt(),
                'updated_at' => $parameter->getUpdatedAt(),
            ];
        }
        return $parametersArray;
    }
}

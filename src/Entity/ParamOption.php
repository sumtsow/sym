<?php

namespace App\Entity;

use App\Repository\ParamOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParamOptionRepository::class)
 */
class ParamOption
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1023)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *   max = 1023
     * )
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=AvParameter::class, inversedBy="paramOptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $avParameter;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getAvParameter(): ?AvParameter
    {
        return $this->avParameter;
    }

    public function setAvParameter(?AvParameter $avParameter): self
    {
        $this->avParameter = $avParameter;

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

    public static function toArray($options): array
    {
        if (!count($options)) return [];
        $optionsArray = [];
        foreach($options as $option) {
            $optionsArray[$option->getId()] = [
                'id' => $option->getId(),
                'av_parameter' => $option->getAvParameter()->getName(),
                'value' => $option->getValue(),
                'created_at' => $option->getCreatedAt(),
                'updated_at' => $option->getUpdatedAt(),
            ];
        }
        return $optionsArray;
    }
}

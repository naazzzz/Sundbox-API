<?php

namespace App\Entity;

use App\Entity\Enum\SGroupsEnum;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampableEntityTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        SGroupsEnum::GET_BASE->value,
        SGroupsEnum::GET_BASE_OBJ->value
    ])]
    protected DateTimeInterface $dateCreate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        SGroupsEnum::GET_BASE->value,
        SGroupsEnum::GET_BASE_OBJ->value
    ])]
    protected DateTimeInterface $dateUpdate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups([
        SGroupsEnum::GET_BASE->value,
        SGroupsEnum::GET_BASE_OBJ->value
    ])]
    public DateTimeInterface $deleted;

    /**
     * this method should be called from constructor.
     */
    private function initDates(): void
    {
        try {
            $this->dateCreate = new DateTimeImmutable();
            $this->dateUpdate = new DateTimeImmutable();
        } catch (Exception $e) {
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    #[ORM\PrePersist]
    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        try {
            $now = new DateTimeImmutable();
            $this->dateUpdate = $now;
        } catch (Exception $e) {
        }
    }
}

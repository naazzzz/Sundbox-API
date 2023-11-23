<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Enum\SGroupsEnum;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

abstract class BaseEntity
{
    use TimestampableEntityTrait;

    public function __construct()
    {
        $this->initDates();
    }

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    #[Groups([
        SGroupsEnum::GET_BASE->value,
        SGroupsEnum::GET_BASE_OBJ->value
    ])]
    #[ApiProperty(identifier: true)]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
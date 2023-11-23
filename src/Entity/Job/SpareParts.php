<?php

namespace App\Entity\Job;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\BaseEntity;
use App\Entity\Enum\SGroupsEnum;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


//Нужно для SparePartsDTO, самое интересное там
#[ApiResource(
    uriTemplate: '/users/{usersId}/spare_parts/{sparePartsId}',
    operations: [
        new Get(),
        new GetCollection(
            uriTemplate: '/users/{usersId}/spare_parts',
            uriVariables: [
                'usersId' => new Link(
                    fromProperty: 'parts',
                    fromClass: User::class
                ),
            ],
        ),
        new Put(),
        new Patch(),
        new Post(
            uriTemplate: '/users/{usersId}/spare_parts',
            uriVariables: [
                'usersId' => new Link(
                    fromProperty: 'parts',
                    fromClass: User::class
                ),
            ],),
        new Delete()
    ],
    uriVariables: [
        'usersId' => new Link(
            fromProperty: 'parts',
            fromClass: User::class
        ),
        'sparePartsId' => new Link(
            fromClass: SpareParts::class
        ),
    ],
)]
#[ORM\Entity]
class SpareParts extends BaseEntity
{
    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_SPARE_PARTS->value,
        SGroupsEnum::SET_SPARE_PARTS->value
    ])]
    public string $description;

    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_SPARE_PARTS->value,
        SGroupsEnum::SET_SPARE_PARTS->value
    ])]
    public float $price;

    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_SPARE_PARTS->value,
        SGroupsEnum::SET_SPARE_PARTS->value
    ])]
    public int $workTime;

    #[ORM\ManyToOne(targetEntity: Job::class, cascade: ['persist'], inversedBy: 'parts')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        SGroupsEnum::SET_SPARE_PARTS->value,
        SGroupsEnum::GET_SPARE_PARTS->value
    ])]
    public ?Job $job = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'parts')]
    #[Groups([
        SGroupsEnum::SET_SPARE_PARTS->value,
        SGroupsEnum::GET_SPARE_PARTS->value
    ])]
    public User $user;
}
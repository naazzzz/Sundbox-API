<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\BaseEntity;
use App\Entity\Enum\SGroupsEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('legal_entity_read', object)"
        ),
        new GetCollection(
        ),
        new Post(),
        new Patch(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => [SGroupsEnum::SET_LEGAL_ENTITY->value]],
    denormalizationContext: ['groups' => [SGroupsEnum::SET_LEGAL_ENTITY->value]],
    security: "is_granted('ROLE_USER')",
    exceptionToStatus: [
        //Можно изменить код ошибки без всяких кастомных контроллеров
        ValidationException::class => 422,
    ]
)]
#[ORM\Entity]
class LegalEntity extends BaseEntity
{
    public const LEGAL_ENTITY_READ = 'legal_entity_read';

    public function __construct(
        //Начиная с php 8 такое объявление вроде как хороший тон
        //Такой синтаксис эквивалентен тому, что мы бы полем создали бы эту коллекцию и
        //в конструкторе присвоили бы ей значение ArrayCollection
        //Почему мне кажется что так хорошо: все коллекции для сущности будут в одном месте
        #[ApiProperty(uriTemplate: '/legal_entities/{id}/users')]
        #[ORM\OneToMany(mappedBy: 'legalEntity', targetEntity: User::class, cascade: ['persist', 'remove'])]
        #[Groups([
            SGroupsEnum::GET_LEGAL_ENTITY->value
        ])]
        /**
         * @var list<User>
         */
        public iterable $users = new ArrayCollection(),
       ) {
        parent::__construct();
    }

    #[ORM\Column(unique: true)]
    #[Assert\NotBlank]
    #[Groups([
        SGroupsEnum::GET_LEGAL_ENTITY->value,
        SGroupsEnum::SET_LEGAL_ENTITY->value
    ])]
    public string $name;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups([
        SGroupsEnum::GET_LEGAL_ENTITY->value,
        SGroupsEnum::SET_LEGAL_ENTITY->value
    ])]
    public bool $customer = false;

}
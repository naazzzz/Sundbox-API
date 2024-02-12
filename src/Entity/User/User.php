<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\BaseEntity;
use App\Entity\Enum\SGroupsEnum;
use App\Entity\Job\SpareParts;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    uriTemplate: '/legal_entities/{legalId}/users/{userId}',
    operations: [
        new Get(),
        new GetCollection(
            //Тут мне просто захотелось поиграться с Subresources, вообще это отдельный вопрос для обсуждений,
            //Потому что очень классные штуки, которые самоописывают код и взаимосвязи в нем,
            //Если выдерживать глубину в 2-3 элемента как заявляют сами разработчики
            //Например избежать объяснений взаимосвязей на Бизоне между документами и техникой можно было при помощи Subresources
            uriTemplate: '/legal_entities/{legalId}/users',
            uriVariables: [
                'legalId' => new Link(
                    fromProperty: 'users',
                    fromClass: LegalEntity::class
                ),
            ],
        ),
        //Вообще метод Put надо бы переставать использовать в том виде, как мы это делаем, потому что с Api Platform 3.1
        //Он будет отвечать за изменение файла целиком по умолчанию, как и есть в спецификации в то время, как мы его используем, как:
        //Put + Patch
        new Put(processor: UserPasswordHasher::class),
        // Также процесс хеширования паролей можно как в документации просто вынести в state processor
        new Patch(processor: UserPasswordHasher::class),
        new Post(
            uriTemplate: '/legal_entities/{legalId}/users',
            uriVariables: [
                'legalId' => new Link(
                    fromProperty: 'users',
                    fromClass: LegalEntity::class
                ),
            ],
            openapi: new Operation(
            //В целом теперь в таком виде описание и добавление параметров в запросе (Напр.: Приложить файл к запросу без всяких постманов)
            //Да и чтобы избежать лишних вопросов, что, для чего, можно в description почаще класть важную информацию например каким ролям открыт этот эндпоинт
            //Да и в принципе, чтобы избежать описание документации в постмане это же все может описать в openApi
                description: 'Security: none'
            ),
            processor: UserPasswordHasher::class,
        ),
        new Delete()
    ],
    uriVariables: [
        'legalId' => new Link(
            fromProperty: 'users',
            fromClass: LegalEntity::class
        ),
        'userId' => new Link(
            fromClass: User::class
        ),
    ],
    //Я сам в шоке, что enum получилось использовать для S_GROUPS, можно убрать безумную гору констант из классов сущностей
    normalizationContext: ['groups' => [SGroupsEnum::GET_USER->value]],
    denormalizationContext: ['groups' => [SGroupsEnum::SET_USER->value]],
    exceptionToStatus: [
        //Можно изменить код ошибки без всяких кастомных контроллеров
        ValidationException::class => 422,
    ]
)]
#[ORM\Entity]
class User extends BaseEntity implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct(
        #[ORM\OneToMany(mappedBy: 'user', targetEntity: SpareParts::class, cascade: ['persist','remove'])]
        public iterable $parts = new ArrayCollection()
    ) {
        parent::__construct();
    }

    #[ORM\Column(unique: true)]
    //Мне кажется что проверку на NotBlank стоит юзать почаще,
        // вместо того, чтобы делать nullable: true, чтобы избежать проблем с базой в будущем
        // да и поддержка всех Assert довольно информативно обновилась
    #[Assert\NotBlank]
    #[Groups([
        //Вообще насчет enums для групп сериализации мне кажется нужно сделать один файлик, типо SGroupsEnum куда их для всех сущностей
        // и кидать мне кажется это упорядочит код и все группы будут лежать в одном файле
        SGroupsEnum::GET_USER->value,
        SGroupsEnum::SET_USER->value
    ])]
    public string $username;

    #[ORM\Column(unique: true)]
    #[Assert\Email]
    #[Groups([
        SGroupsEnum::GET_USER->value,
        SGroupsEnum::SET_USER->value
    ])]
    public string $email;

    #[ORM\ManyToOne(targetEntity: LegalEntity::class, cascade: ['persist'], inversedBy: 'users')]
    #[Groups([
        SGroupsEnum::GET_USER->value,
        SGroupsEnum::SET_USER->value
    ])]
    public LegalEntity $legalEntity;

    #[ORM\Column]
    public string $password;

    #[Assert\NotBlank]
    #[Groups(
        SGroupsEnum::SET_USER->value
    )]
    public string $plainPassword;


    #[ORM\Column(type: "array")]
    #[ApiProperty(
        security: "is_granted('ROLE_ADMIN')",
        securityPostDenormalize: "is_granted('UPDATE', object)")]
    public array $roles;

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): static
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): static
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole($role = null): bool
    {
        if (is_null($role)) {
            return false;
        }
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getGroups(): array
    {
        return [];
    }
    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
       return $this->email;
    }
}
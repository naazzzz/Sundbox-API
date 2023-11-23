<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\SparePartsDTO;
use App\Entity\Job\SpareParts;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AddJobStateProvider implements ProviderInterface
{
    public function __construct(
        //Мне кажется так удобнее чем лезть в сервисы и прописывать ручками, вроде бы это Java-стиль
      #[Autowire(service: CollectionProvider::class)]
      private readonly ProviderInterface $collectionProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /**
         * @var SpareParts[] $entities
         */
        $entities = $this->collectionProvider->provide(...);

        $dtos = [];

        foreach ($entities as $entity) {
            $dtos[] = new SparePartsDTO(new ArrayCollection());
        }

        return $dtos;
    }
}
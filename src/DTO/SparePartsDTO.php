<?php

namespace App\DTO;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\Job\SpareParts;
use App\Entity\User\User;
use App\State\AddJobStateProvider;

#[ApiResource(operations: [
    new Post(
        uriTemplate: '/user/{id}/parts/add_parts_for_user',
        uriVariables: [
            'id' => new Link(
                fromProperty: 'parts',
                fromClass: User::class
            ),
        ],
        //используется для заполнения модели DTO полями, для которой оно нужно
        //API Platform Conference 2023 - Ryan Weaver - Create the DTO system of your dreams
        provider: AddJobStateProvider::class,
        // ну и делаем запись либо через processor, либо через controller
        // controller: AddJobForUserController::class,
        //Врубаем доктриновскую сериализацию, десириализацию, фильтры, пагинацию
        stateOptions: new Options(
            entityClass: SpareParts::class
        )
    )
])]
class SparePartsDTO
{
    public function __construct(

        #[ApiProperty(openapiContext: [
            'type' => 'array',
            'items' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'example' => 1], 'workTime' => ['type' => 'integer', 'example' => 80]]]])]
        public iterable $jobs,

    )
    {
    }
}
<?php

namespace App\Entity\Job;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\BaseEntity;
use App\Entity\Enum\SGroupsEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

//Нужно для SparePartsDTO, самое интересное там
#[ApiResource]
#[ORM\Entity]
class Job extends BaseEntity
{
    public function __construct(
        #[ORM\OneToMany(mappedBy: 'job', targetEntity: SpareParts::class, cascade: ['persist','remove'])]
        public iterable $parts = new ArrayCollection()
    ) {
        parent::__construct();
    }

    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_JOB->value,
        SGroupsEnum::SET_JOB->value
    ])]
    public string $description;

    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_JOB->value,
        SGroupsEnum::SET_JOB->value
    ])]
    public float $price;

    #[ORM\Column]
    #[Groups(groups: [
        SGroupsEnum::GET_JOB->value,
        SGroupsEnum::SET_JOB->value
    ])]
    public int $workTime;

}
<?php

namespace App\State;

//Не знаю насколько адекватно, но для процессоров и провайдеров выглядит хорошей практикой делать final
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordHasher implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * @param User $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data->plainPassword) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->plainPassword
        );
        $data->password = $hashedPassword;

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
<?php

namespace App\User\Service;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $repository;

    /**
     * UserService constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository         $repository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @return User[]
     */
    public function test(): array
    {
        return $this->repository->findAll();
    }

}

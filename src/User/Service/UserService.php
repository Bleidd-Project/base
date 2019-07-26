<?php

namespace App\User\Service;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $repository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Test */
    private $test;

    /**
     * UserService constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository         $repository
     * @param TranslatorInterface    $translator
     * @param Test                   $test
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $repository, TranslatorInterface $translator, Test $test)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->translator = $translator;
        $this->test = $test;
    }

    /**
     * @return mixed
     */
    public function test()
    {
        return $this->translator->trans($this->test->test());

        return $this->repository->findAll();
    }

}

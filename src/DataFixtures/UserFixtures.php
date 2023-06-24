<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'username' => 'leorm',
                'name' => 'Leonardo Rodrigues Marques',
                'email' => 'leonardo@paginaemconstrucao.com.br',
                'roles' => ['ROLE_USER'],
            ],
            [
                'username' => 'jose',
                'name' => 'José João Mateus',
                'email' => 'jose@teste.com',
                'roles' => ['ROLE_USER'],
            ]
        ];

        foreach ($users as $user) {
            $entity = new User();

            $entity->setUsername($user['username'])
                    ->setName($user['name'])
                    ->setEmail($user['email'])
                    ->setRoles($user['roles'])
            ;

            $password = $this->userPasswordHasher->hashPassword($entity, $user['username']);

            $entity->setPassword($password);

            $manager->persist($entity);
        }

        $manager->flush();
    }
}

<?php
namespace COre\Framework\Auth;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class UserAuth{
    private ContainerInterface $container;
    private EntityManager $manager;

    public function __construct(ContainerInterface $container){

        $this->container=$container;
        $this->manager=$container->get(EntityManager::class);
    }
}
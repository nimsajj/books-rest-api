<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\ORM\EntityManagerInterface;

abstract class BaseFixture extends Fixture
{
    protected $em;
    protected $faker;
    const LOCAL = 'fr_FR';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->faker = Factory::create(self::LOCAL);
    }
}

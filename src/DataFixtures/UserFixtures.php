<?php

namespace App\DataFixtures;

use App\Builder\ThereIs;
use App\Enum\CivilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = ThereIs::aUser()
            ->withEmail('admin@yopmail.com')
            ->withPassword('admin')
            ->withCivility(CivilityEnum::MALE)
            ->build();

        $manager->persist($admin);

        for ($i = 0; $i < 10; ++$i) {
            $user = ThereIs::aUser()->build();
            $manager->persist($user);
        }

        $manager->flush();
    }
}

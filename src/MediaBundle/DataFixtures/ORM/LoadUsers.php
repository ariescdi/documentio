<?php

namespace MediaBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MediaBundle\Entity\User;

/**
 * Description of LoadUsers.
 *
 * @author Sylvain Garcia
 */
class LoadUsers extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return User The created user.
     */
    public function createUser(ObjectManager $manager,
            $username, $email, $pwd, $isadmin)
    {
        /* @var $user User */
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($pwd);

        $user->setRoles(array('ROLE_USER'));
        if ($isadmin) {
            $user->addRole('ROLE_ADMIN');
        }
        $user->setEnabled(true);
        $user->setLocked(false);

        $manager->persist($user);

        return $user;
    }

    public function load(ObjectManager $manager)
    {
        // sylvain
        $this->createUser($manager,
                'sylvain',
                'garcia.6l20@gmail.com',
                'sgarcia',
                true);

        // admin
        $this->createUser($manager,
                'admin',
                'fake@fake.com',
                'admin',
                true);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}

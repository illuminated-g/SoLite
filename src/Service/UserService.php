<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;

use Carbon\Carbon;

/**
 * PackageService serves to perform package operations such as creation, lookup, etc.
 */
class UserService {

    private $em;
    private $security;

    /**
     * Initializes this instance when needed by a request.
     *
     * @param EntityManagerInterface $em Provides access to database backed entities.
     * @param Security $security Provides access to currently logged in user, if any, to check permissions.
     */
    public function __construct(ManagerRegistry $doctrine, Security $security) {
        $this->security = $security;
        $this->em = $doctrine->getManager();
    }

    /**
     * Finishes setup for a user, typically on the first time they log in.
     *
     * @param User $user The user to finish setting up
     */
    public function finishSetup(User $user) {
        
    }
}
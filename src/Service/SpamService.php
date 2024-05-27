<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;

use Carbon\Carbon;


class SpamService {
    private $em;
    private $security;
    private $illegalUserChars;
    private $illegalInfoChars;

    /**
     * Initializes this instance when needed by a request.
     *
     * @param EntityManagerInterface $em Provides access to database backed entities.
     * @param Security $security Provides access to currently logged in user, if any, to check permissions.
     */
    public function __construct(ManagerRegistry $doctrine, Security $security,
        String $illegalUserChars, String $illegalInfoChars) {
        $this->security = $security;
        $this->em = $doctrine->getManager();
    }

    public function checkIsSpamUser(User $user) {
        
    }
}
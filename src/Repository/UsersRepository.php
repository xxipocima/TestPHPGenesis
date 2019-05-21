<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends EntityRepository
{
    /**
     * @param Users $user
     * @return Users[]
     */
    public function findAllForUser(Users $user)
    {
        return $this->findBy(array('user' => $user));
    }

    /**
     * @param $nickname
     * @return Users
     */
    public function findOneByNickname($nickname)
    {
        return $this->findOneBy(array('firstName' => $nickname));
    }

    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('users');
        if ($filter) {
            $qb->andWhere('users.firstName LIKE :filter OR users.lastName LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }
        return $qb;
    }

    public function findAllQueryBuilders($filter = '')
    {
        $qb = $this->createQueryBuilder('users');
        if ($filter) {
            $qb->andWhere('users.firstName LIKE :filter OR users.lastName LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }
        return $qb;
    }
}

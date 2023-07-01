<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(User $user, string $name = null, string $description = null)
    {
        $query = $this->createQueryBuilder('t')
                ->where('t.owner = :owner')
                ->setParameter('owner', $user->getId()->toBinary())
        ;

        if (null != $name) {
            $query->andWhere('MATCH (t.name) AGAINST (:name IN BOOLEAN MODE) > 0')
                    ->setParameter('name', $name)
            ;
        }

        if (null != $description) {
            $query->andWhere('MATCH (t.description) AGAINST (:description IN BOOLEAN MODE) > 0')
                    ->setParameter('description', $description)
            ;
        }

        return $query->orderBy('t.name', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 
     * @param string $name
     * @return Task[]
     */
    public function findByName(string $name, User $user)
    {
        return $this->createQueryBuilder('t')
                        ->where('t.name = :name')
                        ->setParameter('name', $name)
                        ->andWhere('t.done = :done')
                        ->setParameter('done', false)
                        ->andWhere('t.owner = :owner')
                        ->setParameter('owner', $user->getId()->toBinary())
                        ->orderBy('t.name', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findByOwner(User $user)
    {

        return $this->createQueryBuilder('t')
                        ->where('t.owner = :owner')
                        ->setParameter('owner', $user->getId()->toBinary())
                        ->andWhere('t.done = :done')
                        ->setParameter('done', false)
                        ->orderBy('t.name', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

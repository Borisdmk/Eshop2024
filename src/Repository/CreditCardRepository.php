<?php
namespace App\Repository;
use App\Entity\CreditCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<CreditCard>
 *
 * @method CreditCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditCard[] findAll()
 * @method CreditCard[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditCardRepository extends ServiceEntityRepository
{
 public function __construct(ManagerRegistry $registry)
 {
 parent::__construct($registry, CreditCard::class);
 }
 public function save(CreditCard $entity, bool $flush = false): void
 {
 $this->_em->persist($entity);
 if ($flush) {
 $this->_em->flush();
 }
 }
 public function remove(CreditCard $entity, bool $flush = false): void
 {
 $this->_em->remove($entity);
 if ($flush) {
 $this->_em->flush();
 }
 }
 // /**
 // * @return CreditCard[] Returns an array of CreditCard objects
 // */
 /*
 public function findByExampleField($value)
 {
 return $this->createQueryBuilder('c')
 ->andWhere('c.exampleField = :val')
 ->setParameter('val', $value)
 ->orderBy('c.id', 'ASC')
 ->setMaxResults(10)
  ->getQuery()
 ->getResult()
 ;
 }
 */
 /*
 public function findOneBySomeField($value): ?CreditCard
 {
 return $this->createQueryBuilder('c')
 ->andWhere('c.exampleField = :val')
 ->setParameter('val', $value)
 ->getQuery()
 ->getOneOrNullResult()
 ;
 }
 */
}
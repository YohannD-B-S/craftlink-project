<?php

namespace App\Repository;

use App\Entity\Artisan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artisan>
 */
class ArtisanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artisan::class);
    }

    /**
     * Recherche des artisans disponibles selon la spécialité et le code postal.
     *
     * @param string $speciality
     * @param string $postalCode
     * @return Artisan[]
     */
    public function findAvailableArtisans(string $speciality, string $postalCode): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->where('a.speciality = :speciality')
            ->andWhere('a.available = :available')
            ->andWhere('u.postalCode = :postalCode')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('speciality', $speciality)
            ->setParameter('available', 'Disponible')
            ->setParameter('postalCode', $postalCode)
            ->setParameter('role', '%ROLE_ARTISAN%');

        $artisans = $qb->getQuery()->getResult(); // ✅ Assure que ce sont bien des objets Artisan

        if (empty($artisans)) {
            $departmentCode = substr($postalCode, 0, 2);

            $qb = $this->createQueryBuilder('a')
                ->join('a.user', 'u')
                ->where('a.speciality = :speciality')
                ->andWhere('a.available = :available')
                ->andWhere('u.postalCode LIKE :departmentCode')
                ->andWhere('u.roles LIKE :role')
                ->setParameter('speciality', $speciality)
                ->setParameter('available', 'Disponible')
                ->setParameter('departmentCode', $departmentCode . '%')
                ->setParameter('role', '%ROLE_ARTISAN%');

            $artisans = $qb->getQuery()->getResult();
        }

        return $artisans; // ✅ Retourne bien des objets Artisan complets
    }
}
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
        return $this->createQueryBuilder('a')
            ->join('a.user', 'u') // Jointure avec l'utilisateur
            ->where('a.speciality = :speciality')
            ->andWhere('a.available = :available')
            ->andWhere('u.postalCode = :postalCode')
            ->andWhere('u.roles LIKE :role') // Filtrer uniquement les artisans
            ->setParameter('speciality', $speciality)
            ->setParameter('available', 'Disponible')
            ->setParameter('postalCode', $postalCode)
            ->setParameter('role', '%ROLE_ARTISAN%') // Vérifier le rôle
            ->getQuery()
            ->getResult();
    }
}
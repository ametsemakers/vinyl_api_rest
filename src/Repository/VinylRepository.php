<?php

namespace App\Repository;

use App\Entity\Vinyl;
use App\Entity\Song;
use App\Entity\Image;
use Doctrine\ORM\EntityRepository;
//use Doctrine\ORM\QueryBuilder;

class VinylRepository extends EntityRepository
{
    // public function getWithChildren()
    // {
    //     return $this->createQueryBuilder('v')
    //         //->addselect('vinyl')
    //         //->leftJoin('v.idVinyl', 's.idVinyl')
    //         ->leftJoin('v.idImage', 'image')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function get()
    {
    $qb = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->innerJoin('o.sections', 'section')
            ->where('translation.locale = :localeCode')
            ->andWhere('section.code = :sectionCode')
            ->andWhere('o.enabled = true')
            ->orderBy('o.updatedAt', 'DESC')
            ->setParameter('sectionCode', $sectionCode)
            ->setParameter('localeCode', $localeCode)
        ;
    }

}
<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\ORM\EntityRepository;
//use Doctrine\ORM\QueryBuilder;

class ImageRepository extends EntityRepository
{
    public function getSongFromVinyl($id_image)
    {
        return $this->createQueryBuilder('i')
            ->addselect('image')
            ->where('song.id = :id')
            ->setParameter('id', $id_image)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
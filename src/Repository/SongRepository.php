<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\ORM\EntityRepository;
//use Doctrine\ORM\QueryBuilder;

class SongRepository extends EntityRepository
{
    public function getSongFromVinyl($id_vinyl)
    {
        return $this->createQueryBuilder('s')
            ->addselect('song')
            ->where('song.id_vinyl = :id')
            ->setParameter('id', $id_vinyl)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
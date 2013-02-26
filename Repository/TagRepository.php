<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    const QUERY_PROPERTY = "q";
    
    public function extractQueryBuilder($params)
    {
        $qb = $this->getTagsQueryBuilder();
        foreach ($params as $key => $value) {
            if($key == self::QUERY_PROPERTY){
                $qb
                    ->andWhere('tag.name LIKE :value')
                    ->setParameter('value', '%'.$value.'%')
                ;
            }
        }

        return $qb;
    }

    public function getTagsQueryBuilder()
    {
        return $this
            ->createQueryBuilder('tag')
            ->orderBy('tag.name', 'ASC')
        ;
    }

    /**
     * extractQuery
     *
     * @param array $params
     * @return Query
     */
    public function extractQuery($params)
    {
        $qb = $this->extractQueryBuilder($params);

        return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * extract
     *
     * @param array $params
     * @return DoctrineCollection
     */
    public function extract($params)
    {
        $q = $this->extractQuery($params);
        
        return is_null($q) ? array() : $q->getResult();
    }

}
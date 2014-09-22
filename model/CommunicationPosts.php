<?php
/**
 * CommunicationPosts
 * @version        $Id$
 * @package        Model
 */

require_once dirname(__FILE__) . '/Base/CommunicationPostsBase.php';
class CommunicationPosts extends \CommunicationPostsBase
{
    public static function getPosts($limit = 10, $offset = 0)
    {
        $data =
            CommunicationPosts::read()
                ->select('*')
                ->where('status != 0')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('id', 'desc')
                ->execute()
                ->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function countAll()
    {
        $data = CommunicationPosts::read()
            ->select('*')
            ->orderBy('id', 'desc')
            ->execute()
            ->rowCount();

        return $data;
    }

    public static function countByCategorty($catId)
    {
        $data = CommunicationPosts::read()
            ->select('*')
            ->where('status != 0')
            ->where('category_id =' . $catId)
            ->orderBy('id', 'desc')
            ->execute()
            ->rowCount();

        return $data;
    }

    public static function getFeaturedPosts($limit = 10, $offset = 0)
    {
        $data =
            CommunicationPosts::read()
                ->select('*')
                ->where('status = 2')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('id', 'desc')
                ->execute()
                ->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }


    public static function getByCategory($catId, $limit = 10, $offset = 0)
    {
        $data =
            CommunicationPosts::read()
                ->select('*')
                ->where('status != 0')
                ->andWhere('category_id = ' . $catId)
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('id', 'desc')
                ->execute()
                ->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

}
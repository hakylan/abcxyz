<?php 
/**
 * UserOriginSite
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserOriginSiteBase.php';
class UserOriginSite extends \UserOriginSiteBase {
    const STATUS_ACTIVE = 1;

    public static function getAllUserOriginSite() {
        static $user_origin_site;
        if (null == $user_origin_site) {
            $user_origin_site = self::select()
                ->andWhere( " `status` = " . \UserOriginSite::STATUS_ACTIVE )
                ->execute();
        }

        return $user_origin_site;
    }
}
<?php 
/**
 * FavoriteItem
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/FavoriteItemBase.php';
class FavoriteItem extends \FavoriteItemBase {

    /**
     * Check item has been like
     * @param $item_id
     * @param $homeland
     * @param $onSite
     * @return bool
     */
    public function hasLiked($item_id=null, $homeland = null, $onSite = false) {
        $exist = null;
        if($onSite) {
            // Find item in system

        } else {
            $exist = self::findByItemIdAndHomeland($item_id, $homeland);
        }
        if(!empty($exist)) {
            $liked = true;
        } else $liked = false;

        return $liked;
    }
}
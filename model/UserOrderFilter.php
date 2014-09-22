<?php 
/**
 * UserOrderFilter
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserOrderFilterBase.php';
class UserOrderFilter extends \UserOrderFilterBase {
    const STATUS_NEW = 0; // Init
    const STATUS_UNFINISHED = 1;
    const STATUS_FINISHED = 2;
    const STATUS_REJECT = 3;
}
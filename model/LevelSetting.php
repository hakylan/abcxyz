<?php 
/**
 * LevelSetting
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/LevelSettingBase.php';
class LevelSetting extends \LevelSettingBase {

    /**
     * @param int $level
     * @return LevelSetting
     */
    public static function getLevelObjByLevel($level = 0){
        return self::retrieveByLevel($level);
    }
}
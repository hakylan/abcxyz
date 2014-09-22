<?php 
/**
 * Locations
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/LocationsBase.php';
class Locations extends \LocationsBase {

    const LOCATION_STATE = "STATE",
        LOCATION_COUNTRY = "COUNTRY",
        LOCATION_DISTRICT = "DISTRICT";

    public function get($config = array()){
        return ModelHelper::getInstance(new self)->get($config);
    }

    public static function getLocationLabel($id){
        $location = self::findOneById($id);
        if(!$location || !($location instanceof \Locations)) return false;
        return $location->getLabel();
    }

    public static function getLocationKeyCode($id){
        $location = self::findOneById($id);
        if(!$location || !($location instanceof \Locations)) return false;
        return $location->getKeyCode();
    }
}
<?php 
/**
 * PackageCheckingHistory
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/PackageCheckingHistoryBase.php';
class PackageCheckingHistory extends \PackageCheckingHistoryBase {

    /**
     * Hàm lấy lịch sử kiểm theo từng kiện
     * @param int $package_id
     * @return PackageCheckingHistory|PDOStatement
     */
    public static function retrieveByPackageId( $package_id ){
        $query = \PackageCheckingHistory::select();
        $query->andWhere(" `package_id` = {$package_id} ");
        $query->orderBy(" `ordering_check` ", "ASC");
        $result = $query->execute();
        $data = array();

        if( sizeof($result) > 0 ) {
            foreach( $result as $key => $item ) {
                if( $item instanceof \PackageCheckingHistory ) {
                    $data[ $key ] = $item->toArray();
                    //DELIVERY STAFF - delivery_staff_id
                    $user = \Users::retrieveById( $item->getCreatedBy() );
                    if ( $user ) {
                        $data[ $key ]['created_by'] = $user->getAttributes('id,username,code,last_name,first_name');
                        $data[ $key ]['created_by']['shorten_fullname'] = $user->getShortenFullName();
                        $data[ $key ]['created_by']['avatar'] = \Users::getAvatar32x( $user );
                        $data[ $key ]['created_by']['detail_link'] = \SeuDo\Main::getBackendRouter()->createUrl( 'user/detail', array('id' => $user->getId()) );
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Hàm insert log khi kiểm
     * @param $created_by
     * @param $package_id
     * @param $total_product
     */
    public static function addCheckingHistory( $user_id, $package_id, $total_product ) {
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();

        try {
            $package_checking = new \PackageCheckingHistory();
            $package_checking->setCreatedBy( $user_id );
            $package_checking->setPackageId( $package_id );
            $package_checking->setTotalProduct( $total_product );
            $package_checking->setCreatedTime( new DateTime() );
            $package_checking->save();
            $id = $package_checking->getId();

            $conn->commit();

            $created_by = array();
            $user = \Users::retrieveById( $user_id );
            if ( $user ) {
                $created_by = $user->getAttributes('id,username,code,last_name,first_name');
                $created_by['shorten_fullname'] = $user->getShortenFullName();
                $created_by['avatar'] = \Users::getAvatar32x( $user );
                $created_by['detail_link'] = \SeuDo\Main::getBackendRouter()->createUrl( 'user/detail', array('id' => $user->getId()) );
            }

            return array(
                'created_time' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            );
        }catch (\Flywheel\Exception $e) {
            $conn->rollBack();
            throw $e;
            return false;
        }

    }

}
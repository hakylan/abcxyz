<?php 
/**
 * MemberScoreHistory
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/MemberScoreHistoryBase.php';
class MemberScoreHistory extends \MemberScoreHistoryBase {

    const TYPE_OBJECT_ORDER = 'ORDER';

    public static function getMemberScoreHistory( $user_id = 0, $page = 1, $per_page = 10, $all = false, $field_sort = '', $type_sort = 'DESC' ) {
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();

        try {

            $result = array();
            $total_page = 0;

            if( $user_id == 0 ) {
                return false;
            }

            $query = \MemberScoreHistory::select();
            $query->andWhere("user_id = {$user_id}");


            if( $field_sort == '' ) {
                $query->orderBy('created_time', $type_sort);
            } else {
                $query->orderBy($field_sort, $type_sort);
            }

            $q = clone $query;
            $total = (int)$q->count()->execute();

            if( $all == false ) {
                $start = ($page - 1) * $per_page;
                $query->setFirstResult($start)->setMaxResults($per_page);
            }

            if($total > 0){
                $total_page = $total % $per_page == 0 ? $total / $per_page : intval($total / $per_page) + 1;
            }

            $count = 0;
            foreach( $query->execute() as $item ) {
                if( $item instanceof \MemberScoreHistory ) {
                    $count++;
                    $row = $item->toArray();
                    $row['link'] = '';
                    $row['stt'] = ( $page - 1 ) * $per_page + $count;

                    if( $row['object_type'] == \MemberScoreHistory::TYPE_OBJECT_ORDER ) {
                        $order_id = $row['object_id'];
                        $order_code = '';
                        $order = \Order::retrieveById( $order_id );
                        if( !$order instanceof \Order ) {
                            $order = new stdClass();
                        }

                        $order_code = $order->getCode();

                        //ORDER
                        if( $order_id > 0 && $order_code ) {
                            $row['link'] = 'chi-tiet-don-hang/' . $order_id;
                            $row['object_code'] = $order_code;
                        }
                    }

                    $row['point'] = round( $row['point'] );
                    $row['total_point'] = round( $row['total_point'] );

                    $result[] = $row;
                }
            }

            $conn->commit();

            return array('items' => $result,
                    'total' => $total,
                    'total_page' => $total_page,
                    'current_page' => $page,
                    'SQL' => $query->getSQL()
            );

        }catch (\Flywheel\Exception $e) {
            $conn->rollBack();
            throw $e;
        }

    }

}
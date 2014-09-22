<?php
namespace Backend\Controller\Delivery;

use Backend\Controller\BackendBase;
use Flywheel\Event\Event;
use SeuDo\Logger;

class BillDetail extends BackendBase
{

    private $user = null;
    public $is_public_profile = false;

    public function beforeExecute()
    {
        $this->setTemplate( "Seudo" );
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $this->is_public_profile = true;
        }

    }

    public function executeDefault()
    {
        $this->setView( "Delivery/bill_detail" );
        $this->setLayout( "default" );

        $id = $this->request()->get( "id" );

        $domestic_shipping = \DomesticShipping::retrieveById( $id );

        $document = $this->document();

        $document->title = "Chi tiết phiếu - " . $domestic_shipping->getDomesticBarcode();

        $this->view()->assign( "domestic_shipping", $domestic_shipping );

        return $this->renderComponent();
    }

    public function executeSaveRealCod()
    {
        if (!$this->isAllowed(PERMISSION_DELIVERY_CHANGE_REAL_COD)) {
            return $this->renderText(\AjaxResponse::responseError("Bạn không có quyền thực hiện thao tác này"));
        }
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $real_cod = $this->request()->post( 'real_cod', 'FLOAT', 0 );
        $bill_detail_id = $this->request()->post( 'id', 'INT', 0 );
        $domestic_shipping = \DomesticShipping::retrieveById( $bill_detail_id );
        if ( $domestic_shipping instanceof \DomesticShipping ) {
            try {
                $domestic_shipping->setRealCod( $real_cod );
                $domestic_shipping->save();
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->format = 'JSON';
                $ajax->message = "Lưu phí thực thu thành công";
                $this->dispatch( ON_AFTER_CHANGE_REAL_COD, new Event( $this, array(
                    'domestic_shipping' => $domestic_shipping,
                    'staff'=>$this->user,
                    'real_cod'=>$real_cod,
                    "is_public" => $this->is_public_profile
                ) ) );
                return $this->renderText( $ajax->toString() );
            } catch ( \Exception $e ) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = 'Có lỗi khi lưu dữ liệu.';
                return $this->renderText( $ajax->toString() );
            }

        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Hóa đơn này không tồn tại.';
            return $this->renderText( $ajax->toString() );
        }

    }
}
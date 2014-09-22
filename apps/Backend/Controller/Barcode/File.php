<?php
namespace Backend\Controller\Barcode;

use Backend\Controller\BackendBase;
use Flywheel\Db\Type\DateTime;
use Flywheel\Filesystem\Uploader;
use Flywheel\Util\Folder;
use Flywheel\Validator\Util;
use SeuDo\BarcodeFile;
use SeuDo\Logger;

class File extends BackendBase {
    public function executeDefault() {
    }

    public function executeUploadPage() {
        if (!$this->isAllowed(PERMISSION_UPLOAD_BARCODE)) {//permission first
            $this->raise403(self::t('Bạn không có quyền upload các file quét mã vạch'));
        }

        $this->setView('Barcode/upload');
        $this->document()->addJsVar('bar_code_upload_file_url', $this->createUrl('barcode/file/upload_file'));
        return $this->renderComponent();
    }

    public function executeUploadFile() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Có lỗi xảy ra');

        if (!$this->isAllowed(PERMISSION_UPLOAD_BARCODE)) {//permission first
            $ajax->message = self::t('Bạn không có quyền upload các file quét mã vạch');
            return $this->renderText($ajax->toString());
        }

        $activity = trim($this->post('warehouse'));
        if ($activity) {
            $activity = explode('/',$activity);
            $warehouse = $activity[1];
            $activity = $activity[0];
        } else {
            $ajax->message = self::t('Vui lòng chọn Hành động');
            return $this->renderText($ajax->toString());
        }

        $staff = \BackendAuth::getInstance()->getUser();

        $error = array();

        $working_date = str_replace(' ', '', trim($this->post('working_date')));
        $working_date = str_replace('-', '/', $working_date);
        if (date('m') == '01') {//careful about working date is last year's december
            $t = explode('/', $working_date);
            if ($t[1] == '12');
            $year = date('Y') - 1;
        } else {
            $year = date('Y');
        }

        $working_date = $working_date .'/' .$year;

        if (Util::validateDate($working_date, 'd/m/Y')) {
            $working_date = DateTime::createFromFormat('d/m/Y', $working_date);
        } else {
            $error['working_date'] = self::t('Ngày không chính xác');
        }

        if (!empty($error)) {
            $ajax->error = $error;
            return $this->renderText($ajax->toString());
        }

        //upload new file
        $upload_path = RUNTIME_PATH .'/barcode_files/';
        Folder::create($upload_path);
        $uploader = new Uploader(RUNTIME_PATH .'/barcode_files/', 'barcode_file');
        $uploader->setIsEncryptFileName(true);
        $uploader->setNameAfterUploaded(time() .'_' .$_FILES['barcode_file']['name']);
        $uploader->setFilterType('.csv,.xls,.xlsx,.xl,.xsl,.xml');
        if (!$uploader->upload()) {
            Logger::factory('system')->error("Could not upload file", $uploader->getError());
            return $this->renderText(\AjaxResponse::responseError('Could not upload file'));
        }
        $data = $uploader->getData();
        Logger::factory('system')->info('Upload file success!', $data);

        try {
            //parsing file
            $content = BarcodeFile::parsingFile($upload_path .DIRECTORY_SEPARATOR .$data['file_name']);
            $content = BarcodeFile::serialize($content);

            //Save to barcode file
            $bf = new \BarcodeFiles();
            $bf->setActivity($activity);
            $bf->setFileLocation($data['file_name']);
            $bf->setFileName($data['file_origin_name']);
            $bf->setContent(implode(',', $content));
            $bf->setWarehouse($warehouse);
            $bf->setTotalBarcode(sizeof($content));
            $bf->setWorkingDate($working_date);
            $bf->setUploadedBy($staff->getId());
            if ($bf->save()) {
                BarcodeFile::pushAnalysisQueue($bf);
                Logger::factory('system')->info('Saved barcode file data to database', $bf->toArray());
                $this->dispatch('afterSavedBarcodeFile', new \BackendEvent($this, array(
                    'barcode_file' => $bf
                )));

                $ajax->message = 'OK';
                $ajax->barcode_file = $bf->toArray();
                $ajax->uploader = $staff->getAttributes('username, full_name, last_name, email');
                $ajax->type = \AjaxResponse::SUCCESS;
            } else {
                $ajax->message = self::t('Lỗi kỹ thuật, liên hệ bộ phận kỹ thuật để giải quyết');
                Logger::factory('system')->error("Could not upload barcode file. \nValidation Failure Messages:\n" . $bf->getValidationFailuresMessage("\n"));
            }
        } catch (\Exception $e) {
            $ajax->message = self::t('Lỗi kỹ thuật, liên hệ bộ phận kỹ thuật để giải quyết');
            Logger::factory('system')->error("Exception: {$e->getMessage()} in {$e->getFile()} at {$e->getLine()}\nTraces:\n" .$e->getTraceAsString());
        }

        return $this->renderText($ajax->toString());
    }

    public function executeGetBarcodeFiles() {
    }
} 
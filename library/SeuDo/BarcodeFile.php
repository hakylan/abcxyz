<?php
namespace SeuDo;

class BarcodeFile {
    /**
     * Parsing Excel file
     *
     * @param $file_path
     * @return array
     * @throws \Exception
     */
    public static function parsingFile($file_path) {
        try {
            /** @var \PHPExcel_Reader_Excel2003XML $excelReader */
            $excelReader = \PHPExcel_IOFactory::createReaderForFile($file_path);
            $excelReader->setReadDataOnly();
            $excelReader->setLoadAllSheets();

            $excelObj = $excelReader->load($file_path);
            $result = $excelObj->getActiveSheet()->toArray(null, false, false, false);
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $bar_codes
     * @return array
     */
    public static function serialize($bar_codes) {
        $t = array();
        for($i = 0, $size = sizeof($bar_codes); $i < $size; ++$i) {
            if (is_array($bar_codes[$i])) {
                for ($j = 0, $sizeOfRows = sizeof($bar_codes[$i]); $j < $sizeOfRows; ++$j) {
                    $t[] = $bar_codes[$i][$j];
                }
            } else {
                $t[] = $bar_codes[$i];
            }
        }

        return array_unique($t);
    }

    /**
     * Push barcode file content to analysis queue
     * @param $bf
     */
    public static function pushAnalysisQueue(\BarcodeFiles $bf) {
        $barcodeList = $bf->getContentInArray();
        $queue = Queue::factory('barcode_analysis');
        for ($i = 0, $size = sizeof($barcodeList); $i < $size; ++$i) {
            $queue->push(json_encode(array(
                'barcode_file_id' => $bf->getId(),
                'barcode' => $barcodeList[$i]
            )));
        }
    }
} 
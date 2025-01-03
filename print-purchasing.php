<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once './assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'PHPExcel.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/IOFactory.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/Cell/AdvancedValueBinder.php';

// <editor-fold defaultstate="collapsed" desc="Define Style for excel">
$styleArray = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startcolor' => array(
            'argb' => 'FFA0A0A0'
        ),
        'endcolor' => array(
            'argb' => 'FFFFFFFF'
        )
    )
);

$styleArray1 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);

$styleArray2 = array(
    'font' => array(
        'bold' => true,
        'size' => 14
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);
// </editor-fold>
$purchasingId = $myDatabase->real_escape_string($_POST['purchasingId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$vendorName = $_POST['vendorName'];

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$whereAvailableProperty = '';

$fileName = "Purchasing Data " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "P";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Purchasing");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
// $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Number");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract Type");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Total Amount");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Freight");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Entry Date");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Payment Type");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Request payment date");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "admin Date");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Approve");

$sql = "CALL SearchPurchasing_backup_4Juli2023 ('{$purchasingId}','{$vendorName}','{$periodFrom}','{$periodTo}')";

// echo $sql ;
// die();
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    if ($rowContent->admin_input != '' && $rowContent->status == 0 ) {
        $adminInput1 = 'OK';
    } else if ($rowContent->status == 0 && $rowContent->admin_input == '' ) {
        $adminInput1 = 'On Check';
    }else if($rowContent->status == 1){
        $adminInput1 = 'Reject';
    }

    if($rowContent->payment_type == 1){
        $paymentType = "Urgent";
    }else{
        $paymentType = "Normal";
    }

    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->purchasing_id);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContent->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->contract_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContent->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowContent->contract_no);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowContent->price);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowContent->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowContent->totalAmount);

    //$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}",  number_format($rowContent->price, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}",  number_format($rowContent->quantity, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}",  number_format($rowContent->totalAmount, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowContent->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowContent->freight);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $rowContent->entry_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $paymentType);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $rowContent->reqPaymentDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowContent->admin_input);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $adminInput1);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $rowContent->approve);
}
$bodyRowEnd = $rowActive;

for ($temp = ord("A"); $temp <= ord("P"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Stock Transit Report">

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>

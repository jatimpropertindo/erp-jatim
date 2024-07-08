<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'PHPExcel.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/IOFactory.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/Cell/AdvancedValueBinder.php';

// -------- menampilkan data --------- //

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$batchHeaderCodes = $myDatabase->real_escape_string($_POST['batchHeaderCodes']);

// $sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
// $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// if($result->num_rows > 0) {
//     while($row = $result->fetch_object()) {
//         if($row->module_id == 27) {
//             $whereProperty = "";
// 			if($pLocations != ''){
// 			$whereProperty3 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
// 			}
//         }else{
// 			$whereProperty = "AND p.entry_by = {$_SESSION['userId']}";
// 		}
// 	}
// }

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
   // $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   // $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   // $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $periodFull = "To " . $periodTo . " ";
}

if ($batchHeaderCodes != '') {
	$whereProperty .= "AND bu_detail.batch_code IN ({$batchHeaderCodes})";
}

$sql = "SELECT bu_detail.*, p.payment_type FROM batch_upload_detail bu_detail
        LEFT JOIN batch_upload_header bu_header ON bu_detail.batch_code = bu_header.batch_code
        LEFT JOIN payment p ON p.payment_id = bu_detail.payment_id
        WHERE 1=1 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$fileName = "Batch Upload ". $periodFull . str_replace(" ", "-", $_SESSION['userName']) . ".xls";
$onSheet = 0;
$lastColumn = "R";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

$rowActive = 1;

$data = array();
while($rowData = mysqli_fetch_array($result)) {
    $data[] = $rowData;
}

$no = 0;
$grandTotal = 0;
foreach ($data as $row) {
	$no++;
	$grandTotal += $row['grand_total'];
}

$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "P");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", date("Ymd"), PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", "1020006401522", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $no, PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $grandTotal, PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode();

foreach ($data as $row) {
	$email = $row['email'].";";
	if ($row['email2'] != '') {
		$email .= $row['email2'].";";
	}
	if ($row['email3'] != '') {
		$email .= $row['email3'].";";
	}

	$rowActive++;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("A{$rowActive}", $row['no_rek'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row['benificiary']);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row['stockpile_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "IDR");

	if ($row['payment_type'] == 1) {
		$grand_total = $row['grand_total'] * -1;
	} else {
		$grand_total = $row['grand_total'];
	}

    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $grand_total);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row['kode3']);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row['kode1']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row['kode2'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row['bank_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row['branch']);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Y");
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $email);
	$no++;

	$objPHPExcel->getActiveSheet()->getStyle("G{$rowActive}")->getNumberFormat()->setFormatCode();
}

$bodyRowEnd = $rowActive;

// Set column width
for ($temp = ord("A"); $temp <= ord("R"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();

?>

<?php
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

header("Content-type: application/vnd.ms-word");

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");

header("Content-Transfer-Encoding: binary ");

// -------- menampilkan data --------- //

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$batchHeaderCodes = $myDatabase->real_escape_string($_POST['batchHeaderCodes']);

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
    $whereProperty .= "AND bu_header.batch_code IN ({$batchHeaderCodes})";
}

$sqlHeader = "SELECT bu_header.* from batch_upload_header bu_header WHERE 1=1 {$whereProperty} ";
$resultHeader = $myDatabase->query($sqlHeader, MYSQLI_STORE_RESULT);

header("Content-Disposition: attachment;Filename=Batch Upload (WA) ". $periodFull . str_replace(" ", "-", $_SESSION['userName']) .".doc");
?>
<!DOCTYPE html>
<html>
    
    <head>
        <title>Batch Upload</title>
    </head>
    
    <body>
        <style type="text/css">
            p {
            line-height: 1.0;
            font-family: "Calibri, sans-serif";
            font-size: 11pt;
        }
    </style>


<?php
$data = array();
while ($rowData = mysqli_fetch_array($resultHeader)) {
    $data[] = $rowData;
}

foreach ($data as $row) {

    $sqlDetail = "SELECT bu_detail.*, p.payment_type FROM batch_upload_detail bu_detail
            LEFT JOIN payment p ON p.payment_id = bu_detail.payment_id
            WHERE 1=1 AND bu_detail.batch_code = {$row['batch_code']} ";
    $resultDetail = $myDatabase->query($sqlDetail, MYSQLI_STORE_RESULT);
    if($resultDetail->num_rows > 0) {
        while($rowDetail = mysqli_fetch_array($resultDetail)) {
            if ($rowDetail['payment_type'] == 1) {
                $grand_total = $rowDetail['grand_total'] * -1;
            } else {
                $grand_total = $rowDetail['grand_total'];
            }
        
            echo '<p>'.$rowDetail['remarks'] . " / " . $rowDetail['benificiary'] . " / " . $rowDetail['stockpile_name'] . " : Rp. " . number_format($grand_total, 2, ".", ",") . '</p>';
        }
    }

    echo '<p> *Total Batch Upload ' . $row['batch_number'] . ' : Rp.' . number_format($row['total_trx'], 2, ".", ",") .'*</p><br/>';
}
?>
</html>
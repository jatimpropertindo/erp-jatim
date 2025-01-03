<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$whereProperty = '';
$whereProperty1 = '';
$whereProperty2 = '';
$whereProperty3 = '';
$vendorId = '';
$vendorIds = '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$paymentFrom = '';
$paymentTo = '';
$inputFrom = '';
$inputTo = '';
$adjustmentTo = '';
$status = '';
$rejectStatus = '';
$stockpileName = 'All';
$statusName = 'All';
$whereDiff = '';
$whereDiff2 = '';


/*if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
$vendorId = $_POST['vendorId'];
$whereProperty .= " AND c.vendor_id = {$vendorId} ";
}*/

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }
			
    $whereProperty .= " AND c.vendor_id IN ({$vendorIds}) ";
	
	$sql = "SELECT GROUP_CONCAT(vendor_name) AS vendor_name, count(vendor_id) AS totalv FROM vendor WHERE vendor_id IN ({$vendorIds})";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";
    
    $totalVendor = $rowVendor->totalv;
	
	if($totalVendor == 1){

    $whereDiff .= "ROUND((c.price_converted - (SELECT price_converted FROM contract WHERE contract_id != c.contract_id ORDER BY contract_id AND vendor_id = c.vendor_id DESC LIMIT 1)),2) AS priceDiff, ";
    $whereDiff2 .= "ROUND((SELECT price_converted FROM contract WHERE contract_id != c.contract_id AND vendor_id = c.vendor_id ORDER BY contract_id DESC LIMIT 1),2) AS price2, ";

   }
    
}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= "AND (SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) = {$stockpileId} ";

    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";	
}

if(isset($_POST['adjustmentTo']) && $_POST['adjustmentTo'] != '') {
    $adjustmentTo = $_POST['adjustmentTo'];
    $whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$adjustmentTo}','%d/%m/%Y') ";
	$adjustmentDate = "To " . $adjustmentTo . " ";
}

if(isset($_POST['rejectStatus']) && $_POST['rejectStatus'] != '') {
    $rejectStatus = $_POST['rejectStatus'];
    $whereProperty .= " AND contract_status = {$rejectStatus}";	
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
$statusName = $status;
$whereProperty .= " AND (CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +  
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) +
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0))= 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) +
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0)) > 0 THEN 'OPEN' ELSE 'OUTSTANDING' END) = UPPER('{$status}') ";
}


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$receiveDate = $periodFrom . " - " . $periodTo . " ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty1 .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	 $receiveDate = "From " . $periodFrom . " ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$receiveDate = "To " . $periodTo . " ";
}

if(isset($_POST['inputFrom']) && $_POST['inputFrom'] != '' && isset($_POST['inputTo']) && $_POST['inputTo'] != '') {
    $inputFrom = $_POST['inputFrom'];
    $inputTo = $_POST['inputTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	$whereProperty4 .= " AND loading_date BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y')";
	$whereProperty5 .= " AND transaction_date BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y')";
	 $inputDate = $inputFrom . " - " . $inputTo . " ";
} else if(isset($_POST['inputFrom']) && $_POST['inputFrom'] != '' && isset($_POST['inputTo']) && $_POST['inputTo'] == '') {
    $inputFrom = $_POST['inputFrom'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
	$whereProperty4 .= " AND loading_date >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y')";
	$whereProperty5 .= " AND transaction_date <= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y')";
	$inputDate = "From " . $inputFrom . " ";
	
	//$whereProperty3 .= " AND DATE_FORMAT(loading_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['inputFrom']) && $_POST['inputFrom'] == '' && isset($_POST['inputTo']) && $_POST['inputTo'] != '') {
    $inputTo = $_POST['inputTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') <= STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty4 .= " AND loading_date <= STR_TO_DATE('{$inputTo}', '%d/%m/%Y')";
	$whereProperty5 .= " AND transaction_date >= STR_TO_DATE('{$inputTo}', '%d/%m/%Y')";
	 $inputDate = "To " . $inputTo . " ";
}

if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentFrom = $_POST['paymentFrom'];
    $paymentTo = $_POST['paymentTo'];
    $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$paymentDate = $paymentFrom . " - " . $paymentTo . " ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {
    $paymentFrom = $_POST['paymentFrom'];
    $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$paymentDate = "From " . $periodFrom . " ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentTo = $_POST['paymentTo'];
	 $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) <= STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	 $paymentDate = "To " . $paymentTo . " ";
}

?>

<script type="text/javascript">
 $(document).ready(function () {
	 
	 $('#printpoSummary').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#poSummary").printThis();
//            $("#transactionContainer").hide();
        });
		
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/po-summary-report.php', {
                   stockpileId: $('input[id="stockpileId"]').val(), 
                //contractId: $('select[id="searchContractId"]').val(), 
				vendorId: 0, 
                periodFrom: $('input[id="periodFrom"]').val(),
                periodTo: $('input[id="periodTo"]').val(),
				paymentFrom: $('input[id="paymentFrom"]').val(),
                paymentTo: $('input[id="paymentTo"]').val(),
				inputFrom: $('input[id="inputFrom"]').val(),
                inputTo: $('input[id="inputTo"]').val(),
				adjustmentTo: $('input[id="adjustmentTo"]').val(),
                status: $('input[id="status"]').val(),
				rejectStatus: $('input[id="rejectStatus"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/po-summary-report-xls.php">
    
	<input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
	<input type="hidden" id="inputFrom" name="inputFrom" value="<?php echo $inputFrom; ?>" />
    <input type="hidden" id="inputTo" name="inputTo" value="<?php echo $inputTo; ?>" />
	<input type="hidden" id="adjustmentTo" name="adjustmentTo" value="<?php echo $adjustmentTo; ?>" />
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
	<input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
	<input type="hidden" id="rejectStatus" name="rejectStatus" value="<?php echo $rejectStatus; ?>" />
    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printpoSummary">Print</button>
</form>

<div id = "poSummary">
<h4>PT JATIM PROPERTINDO JAYA</h4>
<li class="active">Payment Date : <?php echo $paymentDate ?></li>
<li class="active">Received Date : <?php echo $receiveDate; ?> </li>
<li class="active">Entry Date : <?php echo $inputDate; ?></li>
<li class="active">Adjustment Date : <?php echo $adjustmentDate; ?></li>
<li class="active">Stockpile : <?php echo $stockpileName; ?></li>
<li class="active">Vendor Name : <?php echo $vendorName; ?></li>
<li class="active">Status : <?php echo $statusName; ?></li>
<h4 style="text-align:center;">Summary PO List</h4>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
			<th rowspan="2">No</th>
            <th rowspan="2">No PO</th>
			<th rowspan="2">Contract No</th>
            <th rowspan="2">Vendor</th>
            
            <th rowspan="2">Original Stockpile</th>
            <th colspan="2">ORDER</th>
            <th colspan="21">QTY RECEIVED in STOCKPILE</th>
            <th rowspan="2">Balance Qty Order</th>
			<th rowspan="2">Price / Kg</th>
            <th rowspan="2">Balance Amount Order</th>
			<th rowspan="2">Adjustment Notes</th>
			<th rowspan="2">Payment Voucher</th>
            <th rowspan="2">Payment Date</th>
            <th colspan="2">FIRST RECEIVED</th>
            <th colspan="2">LAST RECEIVED</th>
            <th rowspan="2">STATUS</th>
			<th colspan="4">aging</th>
           
        </tr>
        <tr>
        	<th>Qty Order</th>
            
        	<th>Amount Order</th>
            
            <th>JAMBI</th>
           
            <th>MAREDAN</th>
           
            <th>DUMAI</th>
           
            <th>PADANG</th>
            
            <th>RENGAT</th>
            
            <th>BENGKULU</th>
            
            <th>SAMPIT</th>
           
            <th>TANJUNG BUTON</th>
            
            <th>TAYAN</th>
           
            <th>JAKARTA</th>
			
			<th>PALEMBANG</th>
			
			<th>PANGKALAN BUN</th>
			
			<th>PONTIANAK</th>
			
			<th>SAMARINDA</th>
			
			<th>BATU LICIN</th>
			
			<th>BANGKA BELITUNG</th>
			
			<th>MALOY</th>
			
			<th>ADJUSTMENT</th>
			
			<th>IN TRANSIT</th>
			
			<th>LOCAL SALES</th>
			
			<th>TOTAL RECEIVED</th>
            
            
            <th>Slip No</th>
            
            <th>Date</th>
            
            <th>Slip No</th>
            
            <th>Date</th>
			
			<th>0 - 90</th>
            
            <th>91 - 180</th>
          	 
            <th>181 - 270</th>
           	
            <th>>270</th>
			<th>Date Diff Payment</th>
			<th>Date Diff Receive</th>
            
             </tr>
       
       
    </thead>
    <tbody>
<?php
     

$sql = "SELECT 
(SELECT stockpile_contract_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_contract, 
(SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_id, 
c.vendor_id, c.contract_id, DATE_FORMAT(c.entry_date, '%Y-%m-%d') AS entry_date,
CASE WHEN reject_date > '2023-01-31' THEN REPLACE(po_no,'REJECTED','')
ELSE po_no END AS po_no,
CASE WHEN reject_date > '2023-01-31' THEN REPLACE(contract_no,'REJECTED','')
ELSE contract_no END AS contract_no,
CASE WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015/2/206'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015/3/541'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015/6/203'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015/3/84'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015/4/299'
WHEN c.po_no = 'P-PBI' THEN '2015/2/28'
WHEN c.po_no = 'P-PBI2' THEN '2015/3/78'
WHEN c.po_no = 'P-PBI3' THEN '2015/5/392'
WHEN c.po_no = 'P-PBI4' THEN '2015/5/543'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015/1/117'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015/2/44'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015/5/328'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015/3/417'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015/3/578' 
ELSE(SELECT GROUP_CONCAT(p.payment_no) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  AND p.payment_status = 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_no,
(SELECT p.payment_type FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_type,  
(SELECT CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_location, 
(SELECT b.bank_code FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_code,
(SELECT b.bank_type FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_type,
(SELECT cur.currency_code FROM currency cur WHERE cur.currency_id = (SELECT p.currency_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS currency_code,
CASE WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 002' THEN '2015-05-25'
WHEN c.po_no = 'P-BSS/JPJ 003' THEN '2015-05-28'
WHEN c.po_no = 'P-BSS/JPJ 004' THEN '2015-06-15'
WHEN c.po_no = 'P-BSS/JPJ 005' THEN '2015-06-19'
WHEN c.po_no = 'P-BSS/JPJ 006' THEN '2015-06-22'
WHEN c.po_no = 'P-BSS/JPJ 007' THEN '2015-06-27'
WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015-02-16'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015-03-27'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015-06-09'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015-03-05'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015-04-17'
WHEN c.po_no = 'P-PBI' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI2' THEN '2015-03-05'
WHEN c.po_no = 'P-PBI3' THEN '2015-05-20'
WHEN c.po_no = 'P-PBI4' THEN '2015-05-27'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015-01-22'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015-02-26'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015-03-23'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015-05-31' 
ELSE (SELECT (p.payment_date) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND p.payment_status = 0 AND payment_type = 2 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_date,
(SELECT vendor_name FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_name, (SELECT vendor_address FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_address,
(SELECT s.stockpile_name FROM stockpile s LEFT JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS original_stockpile, 
ROUND(c.`price_converted`,2) AS price_converted, ROUND(c.`quantity`,2) AS quantity, ROUND(c.`price_converted` * c.`quantity`,2) AS amount_order, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) AS bengkulu, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) AS buton, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) AS maredan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) AS padang, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) AS jambi, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) AS dumai, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) AS rengat, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) AS sampit, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) AS tayan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) AS jakarta,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) AS palembang,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) AS pangkalan_bun,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) AS pontianak,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) AS samarinda,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) AS batu_licin,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) AS bangka_belitung,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) AS maloy,  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +    
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) +
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0) AS total_received, 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) AS adjustment,
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0) AS inTransit,
(SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}) AS localSales, 
(SELECT adjustment_notes FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2} ORDER BY adj_id DESC LIMIT 1) AS adjustment_notes,
(ROUND(c.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) +
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0))) AS balance_qty_order, 
(ROUND((ROUND(c.quantity,0) - 
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) + 
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0))) * c.price_converted, 2)) AS balance_amount_order, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_date, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1}  AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1}  AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_date, 
CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) + 
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0)) = 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} AND t.local_sales = 0 ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} AND t.local_sales = 0 ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} AND t.local_sales = 0 ),0) +   
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} AND t.local_sales = 0 ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.local_sales != 0 {$whereProperty1}),0) +
IFNULL((SELECT SUM(CASE WHEN a.status = 1 THEN (SELECT SUM(send_weight) FROM `transaction` WHERE stock_transit_id = a.stock_transit_id {$whereProperty5})
WHEN a.status = 0 THEN (SELECT SUM(send_weight) FROM stock_transit WHERE stock_transit_id = a.stock_transit_id {$whereProperty4})
ELSE 0 END) 
FROM stock_transit AS a 
LEFT JOIN stockpile_contract AS con 
ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
LEFT JOIN TRANSACTION t ON t.`stock_transit_id` = a.`stock_transit_id`
WHERE con.contract_id = c.`contract_id`),0)) > 0 THEN 'OPEN' 
WHEN c.contract_status = 2 THEN 'REJECTED' ELSE 'OUTSTANDING' END AS `status`, 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
(	SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'a', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'b', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 THEN 
	(SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'c', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL AND 
	(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'd',
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) AS diff_payment,
{$whereDiff}{$whereDiff2}
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1))) AS diff_receive
FROM contract c WHERE c.contract_type = 'P' {$whereProperty}
AND (CASE WHEN c.reject_date > STR_TO_DATE('{$inputTo}', '%d/%m/%Y') THEN 0
ELSE contract_status END) != 2 AND c.langsir = 0
GROUP BY c.po_no
ORDER BY c.contract_id ASC ";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

        <?php
		 $no = 1;
		// echo $sql;
        if($result->num_rows > 0) {
           
            while($row = $result->fetch_object()) {
				
			 $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->currency_code;
                
                if($row->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($row->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($row->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }
                
                if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }

			
                ?>
        <tr>
			<td><?php echo $no; ?></td>
            <td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            
            <td><?php echo $row->original_stockpile; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_order, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->jambi, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->maredan, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->dumai, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->padang, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->rengat, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->bengkulu, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->sampit, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->buton, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->tayan, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->jakarta, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->palembang, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->pangkalan_bun, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->pontianak, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->samarinda, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->batu_licin, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->bangka_belitung, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->maloy, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->adjustment, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->inTransit, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->localSales, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->total_received, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance_qty_order, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance_amount_order, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo $row->adjustment_notes; ?></td>
            <td><?php echo $voucherCode; ?>#<?php echo $row->payment_no; ?></td>
            <td><?php echo $row->payment_date; ?></td>
            <td><?php echo $row->first_slip; ?></td>
            <td><?php echo $row->first_date; ?></td>
            <td><?php echo $row->last_slip; ?></td>
            <td><?php echo $row->last_date; ?></td>
            <td><?php echo $row->status; ?></td>
			<td><?php echo $row->a; ?></td>
			<td><?php echo $row->b; ?></td>
			<td><?php echo $row->c; ?></td>
			<td><?php echo $row->d; ?></td>
			<td><?php echo $row->diff_payment; ?></td>
			<td><?php echo $row->diff_receive; ?></td>
			 
			 
        </tr>
                <?php
			
$no++;

if(($row->payment_no !== NULL || $row->payment_no !== '') && $totalVendor == 1){
	
	// echo $row->last_slip;

	$lastSlip = $row->last_slip;
	$price = $row->price_converted;
	$price2 = $row->price2;
	$priceDiff = $price - $price2;
	if($priceDiff < 0){
		$a = 'Turun';
	} else if($priceDiff > 0){
		$a = 'Naik';
	}else {
		$a = 'Tetap';
	}

    $sql = "SELECT * FROM stockpile WHERE stockpile_code = SUBSTR('{$lastSlip}',1,3)";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
	//echo $sql;
    
    $last_slip = $rowStockpile->stockpile_name ;
    $percent = number_format((($row->total_received/$row->quantity) * 100),2);

    $firstDate = $row->first_date;
    $lastDate = $row->last_date;

    $first_date = date_create($row->first_date);
    $last_date = date_create($row->last_date);
    $diff = date_diff($first_date,$last_date);
    $diff2 = $diff->format('%m Months %d Days');
    $diff3 = $diff->format('%a Days');

    $paymentDate = $row->payment_date;

    $payment_date = date_create($row->payment_date);
    //$last_date = date_create($row->last_date);
    $pDiff = date_diff($payment_date,$last_date);
    $pDiff2 = $pDiff->format('%m Months %d Days');
    $pDiff3 = $pDiff->format('%a Days');
}
				//echo $paymentDate;
            }
		} else{
			//echo $sql;
		}
		

        
        ?>
    </tbody>
</table>
<?php if ($totalVendor == 1) {?>
<table class="table table-bordered table-striped" style="font-size: 8pt; width: 75%;">
<tbody>
<tr>
<td><h5>1. Barang terakhir masuk di SP : <?php echo $last_slip ?> <?php echo $percent ?>%</h5></td>
</tr>
<tr>
<td><h5>2. Periode barang terakhir masuk di SP : <?php echo $diff2 ?> (<?php echo $firstDate ?> - <?php echo $lastDate ?> = <?php echo $diff3 ?>)</h5></td>
</tr>
<tr>
<td><h5>3. Date diff payment : <?php echo $pDiff2 ?> (<?php echo $paymentDate ?> - <?php echo $lastDate ?> = <?php echo $pDiff3 ?>)</h5></td>
</tr>
<tr>
<td><h5>4. Harga PKS : <?php echo $a ?> Rp <?php echo $priceDiff ?>/KG (dari <?php echo $price2 ?> menjadi <?php echo $price ?>)</h5></td>
</tr>
</tbody>
</table>
<?php } ?>
</div>

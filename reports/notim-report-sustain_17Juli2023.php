<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$sumProperty = '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = '';
$stockpileIds = '';
$stockpile_codes = '';
$periodFrom = $_POST['periodFrom'];
$periodTo = $_POST['periodTo'];
$stockpileCode = $_POST['stockpileId'];
/*if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileIds = $_POST['stockpileId'];
	
	
					
	$stockpile_code = array();				
    $sql = "SELECT stockpile_code FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_code[] = $row['stockpile_code'];
		
		$stockpile_codes =  implode("','", $stockpile_code);
		}
	}
    //echo $sql;
    //$whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
    //$sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
}*/
/*
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
	// $whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}*/

$sql = "Call SP_notimReport('{$periodFrom}','{$periodTo}','{$stockpileCode}')";
//echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/notim-report-sustain.php', {
                    // stockpileId: document.getElementById('periodFrom').value, 
                    stockpileId: document.getElementById('stockpileIds').value,
					periodFrom: document.getElementById('periodFrom').value,
					periodTo: document.getElementById('periodTo').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<form method="post" id="downloadxls" action="reports/notim-report-sustain-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileCode; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>Slip No.</th>
            <th>Stockpile</th>
            <th>Transaction Date</th>
            <th>PO No./Shipment Code</th>
            <th>PKS Name</th>
            <th>Certification</th>
            <th>Supplier/Customer</th>
            <th>Transaction Type</th>
            <th>Send Weight</th>
            <th>Contract Type</th>
            <th>Inventory</th>
            <th>Balance (Q)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			//echo $sql;
			
         /*   if($boolBalanceBefore) {
                $sql2 = "SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2
                        FROM transaction t
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        WHERE 1=1 {$sumProperty}";
                $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
                
                if($result2->num_rows > 0) {
                    while($row2 = $result2->fetch_object()) {
                        $balanceBefore = $balanceBefore + $row2->quantity2;
                    }*/
                ?>
        <!--<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>-->
                <?php
              //  }
            }
            
            //$balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                //$balanceQuantity = $balanceQuantity + $row->quantity2;
				
				/*if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
				}*/
        ?>

        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->transaction_date2; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->pks; ?></td>
            <td><?php echo $row->certification; ?></td>
            <td><?php echo $row->supplier; ?></td>
            <td><?php echo $row->transaction_type2; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 2, ".", ","); ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance, 2, ".", ","); ?></td>
           
        </tr>
                <?php
                $no++;
         //   }
        }
        ?>
    </tbody>
</table>

<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$pgId = $_POST['pgId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT 100 AS `termin`, 1 AS `exchange_rate`, 0 AS qty, a.total_amount AS price, a.total_amount AS amount_converted,
'0' AS ppn_converted, '0' AS pph_converted, a.`total_amount` AS tamount_converted, a.`remarks` AS notes  
FROM perdin_adv_settle a 
WHERE a.`sa_id` = {$pgId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  /*$sql = "SELECT * FROM contract WHERE contract_id = {$contractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) { 
        $row = $result->fetch_object();
        $contractNo = $row->contract_no;
		$po_no = $row->po_no;*/
    //}
// </editor-fold>

?>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            
            <th>Quantity</th>
			<th>Price</th>
            <th>Termin (%)</th>
            <th>Kurs (IDR)</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>Total</th>
            <th>Keterangan</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
			
			//if($rowData->general_ledger_module == 'CONTRACT'){
				//$debit_amount = $rowData->debitAmount;					
				//$credit_amount = $rowData->creditAmount;
			//}
                
        ?>
        <tr>
         
        <td style="text-align: right;"><?php echo number_format($rowData->qty, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->price, 10, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->termin, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->exchange_rate, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->amount_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->ppn_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->pph_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->tamount_converted, 2, ".", ","); ?></td>
        <td style="text-align: left;"><?php echo $rowData->notes; ?></td>
			
        </tr>
        <?php
            
            }
        } else {
        ?>
        <tr>
            <td colspan="7">
                No data to be shown.
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
    </div>
    <div class="span6 lightblue">
    </div>
</div>

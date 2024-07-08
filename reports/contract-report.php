<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$vendorId = '';
$periodFrom = '';
$periodTo = '';

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    
    $whereProperty .= " AND v.vendor_id = '{$vendorId}' ";
}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
	// $whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty .= " AND con.entry_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $whereProperty .= " AND con.entry_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereProperty .= " AND con.entry_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

$sql = "SELECT con.contract_id, con.po_no, con.contract_no, v.vendor_name, a.stockpile_name, con.notes,con.price_converted, c.quantity, con.entry_date AS contract_date,
(SELECT payment_id FROM payment WHERE stockpile_contract_id = c.`stockpile_contract_id` AND payment_status = 0 GROUP BY stockpile_contract_id) AS payment_id,
(CASE WHEN (SELECT payment_id FROM payment WHERE stockpile_contract_id = c.`stockpile_contract_id` AND payment_status = 0 GROUP BY stockpile_contract_id) IS NOT NULL THEN 'PAID'
WHEN c.`quantity` = 0 THEN '' ELSE 'UNPAID' END) AS payment_status, u.user_name, con.entry_date2
FROM contract con
LEFT JOIN currency cur ON cur.currency_id = con.currency_id
LEFT JOIN vendor v ON v.vendor_id = con.vendor_id
LEFT JOIN stockpile_contract c ON con.contract_id = c.contract_id
LEFT JOIN stockpile a ON a.stockpile_id = c.stockpile_id
LEFT JOIN `user` u ON u.`user_id` = con.`entry_by`
WHERE con.company_id = {$_SESSION['companyId']}
AND con.`contract_status` != 2
{$whereProperty}
ORDER BY a.stockpile_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<br />
<form method="post" action="reports/contract-report-xls.php">
    <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>" />
	<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
	<input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr><th>No</th>
            <th>PO No.</th>
			<th>Contract No.</th>
            <th>Vendor Name</th>
            <th>Stockpile</th>
            <th>Notes</th>
            <th>Price/KG</th>
            <th>Quantity Order</th>
            <th>Contract Date</th>
			<th>Payment Status</th>
			<th>Entry By</th>
			<th>Entry Date</th>
            
            
            
        </tr>
        
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            $no = 1;
			
            while($row = $result->fetch_object()) {
               // $totalCogs = $row->cogs_amount + $row->freight_total + $row->unloading_total;
                ?>
        <tr>
        	<td><?php echo $no?></td>
            <td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
			<td><?php echo $row->notes; ?></td>
			<td><div style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></div></td>
            <td><?php echo $row->contract_date; ?></td>
            <td><?php echo $row->payment_status; ?></td>
            <td><?php echo $row->user_name; ?></td>
            <td><?php echo $row->entry_date2; ?></td>
            
            
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>

<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

//$whereBalanceProperty = '';
//$whereTransactionProperty = '';
//$whereDeliveryProperty = '';
//$whereShipmentProperty = '';
//$whereSalesProperty = '';
//$whereAvailableProperty = '';
//$stockpileId = '';
$periodTo = '';
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
}*/

if (isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];

    $date = str_replace('/', '-', $periodTo);;
    $period = date("Y-m-d", strtotime($date));

    //$whereBalanceProperty .= " AND t.unloading_date < DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
    //$whereTransactionProperty .= " AND t.unloading_date >= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
    $whereAvailableProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereAvailableProperty2 .= " AND d.`delivery_date` > STR_TO_DATE('{$periodTo}', '%d/%m/%Y') AND t2.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    //$whereDeliveryProperty .= " AND d.delivery_date >= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
    //$whereShipmentProperty .= " AND sh.shipment_date <= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
    //$whereSalesProperty .= " AND sl.sales_date <= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
}

?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#contentTable a').click(function (e) {
            e.preventDefault();
            //alert(this.id);


            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'detail') {
                e.preventDefault();

                //$("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
                //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/detail-endstock.php', {
                    period: menu[1],
                    stockpileCode: menu[2]
                }, iAmACallbackFunction2);	//and hide the rotating gif

            }
        });
		
		var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#addDetailModalForm').load('forms/detail-endstock.php', {
                    // stockpileId: document.getElementById('periodFrom').value, 
					period: document.getElementById('periodTo').value,
                    stockpileCode: document.getElementById('stockpileCode').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="tabs/endstock-ls-all-xls.php">

    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>"/>
	<input type="hidden" id="stockpileCode" name="stockpileCode" value="<?php echo $stockpile_code; ?>"/>

    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
    <thead>
    <tr>
        <th>Period :<?php echo $period; ?></th>
        <th>Date Slip No.</th>
        <th>Available Inventory</th>
        <th>PKS Amount</th>
        <th>PKS Amount (/kg)</th>
        <th>Freight Cost</th>
        <th>Freight Cost (/kg)</th>
        <th>Unloading Cost</th>
        <th>Unloading Cost (/kg)</th>
        <th>Handling Cost</th>
        <th>Handling Cost (/kg)</th>
        <th>Total Amount</th>
        <th>Total Amount (/kg)</th>
    </tr>
    </thead>
    <tbody>
    <?php
$sqlContent = "SELECT (SELECT DATE_FORMAT(unloading_date, '%d %b %y') FROM `transaction` WHERE delivery_status = 2 AND  SUBSTRING(slip_no,1,3) = ayam.stockpile_code 
AND transaction_type = 1 ORDER BY slip_no ASC LIMIT 1) AS start_date,`stockpile_code`, `stockpile`, SUM(qty_available) AS qty_available,SUM(pks_amount) AS pks_amount,
SUM(`freight_amount`) AS freight_amount,
SUM(`unloading_amount`) AS unloading_amount ,
SUM(`handling_amount`) AS handling_amount  
FROM
(
SELECT
'' AS start_date, SUBSTRING(t.slip_no,1,3) AS `stockpile_code`, (SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTRING(t.slip_no,1,3)) AS `stockpile`, 
'' AS slip_no, '' AS unloading_date,
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.`quantity` END) -
SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available,

0 AS pks_amount,
0 AS freight_amount,
0 AS unloading_amount ,
0 AS handling_amount

FROM `transaction` t 
WHERE 1=1 AND t.local_sales != 0
AND (CASE WHEN t.`transaction_type` = 2 THEN (SELECT a.assign_po FROM sales a LEFT JOIN shipment b ON a.`sales_id` = b.`sales_id` WHERE b.`shipment_id` = t.`shipment_id`)
ELSE 1 END) = 1 
{$whereAvailableProperty}
GROUP BY SUBSTRING(t.slip_no,1,3)

UNION 
SELECT '' AS asatdate,	
SUBSTRING(t.slip_no,1,3) AS stockpile_code,
(SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTRING(t.slip_no,1,3)) AS stockpile, 
t2.slip_no,
DATE_FORMAT(t2.unloading_date,'%Y-%m-%d') AS entrydate,

0 AS qty_available,

CASE WHEN t2.transaction_type = 1  AND t2.delivery_status = 1
AND (SELECT c.quantity_rule FROM contract c 
LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id
WHERE sc.stockpile_contract_id = t2.stockpile_contract_id ) = 1 
THEN t2.quantity * t2.unit_price
WHEN t2.transaction_type = 1  AND t2.stock_transit_id IS NOT NULL AND t2.stock_transit_id <> 0 
THEN d.quantity * t2.`unit_cost`
ELSE ROUND(d.quantity * t2.unit_price,2) END AS pks_amount,


ROUND((
(CASE WHEN (fc.freight_id = 296 OR fc.freight_id = 309 ) AND d.`percent_taken` = 100 
THEN t2.send_weight
WHEN (fc.freight_id = 296 OR fc.freight_id = 309 ) AND d.`percent_taken` < 100 
THEN (t2.freight_quantity*d.`quantity`) / t2.quantity 
WHEN d.`percent_taken` < 100 
THEN (t2.freight_quantity*d.`quantity`) / t2.quantity   
ELSE t2.freight_quantity END) 
*
(CASE WHEN t2.adjustmentAudit_id IS NOT NULL AND t2.adjustmentAudit_id > 0 
THEN t2.freight_price
ELSE(SELECT CASE WHEN tx.tax_category = 1 
THEN t2.`freight_price` / ((100 - f.pph)/100)
ELSE t2.`freight_price`                                 
END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
WHERE fc.freight_cost_id = t2.freight_cost_id)END)
) - 
COALESCE((CASE WHEN d.`percent_taken` < 100 
THEN (SELECT (d.`quantity`) / t2.freight_quantity 
FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`) 
ELSE 1 END) 
* 
(SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT SUM(amt_claim) FROM transaction_shrink_weight 
WHERE transaction_id = t2.`transaction_id` ) / ((100 - f.pph)/100)
ELSE (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t2.`transaction_id`) END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t2.`freight_cost_id`)
,0)
- 
COALESCE((CASE WHEN d.`percent_taken` < 100 
THEN (SELECT (d.`quantity`) / t2.freight_quantity 
FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`) 
ELSE 1 END) 
* 
(CASE WHEN t2.transaction_date > '2022-09-15'
THEN 
( SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT SUM(tas.amt_claim) 
FROM transaction_additional_shrink tas LEFT JOIN `transaction` t3 ON t3.transaction_id = tas.transaction_id 
WHERE tas.transaction_id = d.`transaction_id`) / ((100 - f.pph)/100)
ELSE
(SELECT SUM(tas.amt_claim) 
FROM transaction_additional_shrink tas LEFT JOIN `transaction` t3 ON t3.transaction_id = tas.transaction_id 
WHERE tas.transaction_id = d.`transaction_id`)
END 
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t2.`freight_cost_id`    
)
ELSE 0 END
),0),2) AS freight_amount,


ROUND(CASE WHEN d.`percent_taken` < 100 THEN (d.`quantity`/t2.quantity) * t2.unloading_price 
ELSE t2.unloading_price END,2) AS unloading_amount,

ROUND((CASE WHEN d.`percent_taken` < 100 
THEN (t2.handling_quantity*d.`quantity`) / t2.quantity 
ELSE t2.`handling_quantity` END) * 
(SELECT CASE WHEN t2.handling_cost_id IS NULL THEN t2.handling_price 
ELSE    
(SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT price_converted FROM vendor_handling_cost 
WHERE handling_cost_id = t2.`handling_cost_id`) / ((100 - vh.pph)/100)
ELSE (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t2.`handling_cost_id`) 
END
FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id 
WHERE vhc.handling_cost_id = t2.handling_cost_id)
END )
,2) AS handling_amount
FROM delivery d
LEFT JOIN `transaction` t ON d.`shipment_id` = t.`shipment_id`
LEFT JOIN `transaction` t2 ON t2.`transaction_id` = d.`transaction_id`
LEFT JOIN freight_cost fc ON fc.`freight_cost_id` = t2.`freight_cost_id` 
LEFT JOIN `shipment` sh ON d.`shipment_id` = sh.`shipment_id`
WHERE 1=1 
{$whereAvailableProperty2}
AND t.`slip_retur` IS NULL AND t.`notim_status` = 0 AND t.local_sales != 0
AND (CASE WHEN t.`transaction_type` = 2 THEN (SELECT a.assign_po FROM sales a LEFT JOIN shipment b ON a.`sales_id` = b.`sales_id` WHERE b.`shipment_id` = t.`shipment_id`)
ELSE 1 END) = 1 
UNION	 
SELECT '' AS asatdate,	
SUBSTRING(t.slip_no,1,3) AS stockpile_code,
(SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTRING(t.slip_no,1,3)) AS stockpile,
t.`slip_no`,
DATE_FORMAT(t.unloading_date,'%Y-%m-%d') AS unloading_date,

0 AS qty_available,

ROUND((SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 
THEN t.quantity * t.unit_price
WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.stock_transit_id IS NOT NULL AND t.stock_transit_id != 0 
THEN t.quantity * t.`unit_cost`
WHEN t.transaction_type = 1 AND t.delivery_status <> 1
THEN t.quantity * (SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id 
WHERE sc.stockpile_contract_id = t.stockpile_contract_id)
ELSE 0 END) -
SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.unit_price 
WHEN t.transaction_type = 1 AND t.delivery_status = 2  AND t.stock_transit_id IS NOT NULL AND t.stock_transit_id != 0 
THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.`unit_cost`
WHEN t.transaction_type = 1 AND t.delivery_status = 2
THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) *
(SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id 
WHERE sc.stockpile_contract_id = t.stockpile_contract_id)
ELSE 0 END)),2) AS pks_amount,

ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 
THEN t.freight_quantity * t.freight_price
WHEN t.transaction_type = 1 AND t.delivery_status <> 1
THEN ((CASE WHEN (fc.freight_id = 296 OR fc.freight_id = 309) 
THEN t.send_weight ELSE t.freight_quantity END) *
(SELECT CASE WHEN tx.tax_category = 1 THEN t.freight_price / ((100 - f.pph)/100)
ELSE t.freight_price
END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
WHERE fc.freight_cost_id = t.`freight_cost_id`))-
COALESCE((SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT SUM(amt_claim) FROM transaction_shrink_weight 
WHERE transaction_id = t.`transaction_id`) / ((100 - f.pph)/100)
ELSE (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t.`transaction_id`) 
END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
WHERE fc.freight_cost_id = t.`freight_cost_id`),0) -
COALESCE((CASE WHEN t.`transaction_date` > '2022-09-15' 
THEN 
(SELECT CASE WHEN tx.tax_category = 1
THEN (SELECT SUM(amt_claim) FROM transaction_additional_shrink WHERE transaction_id = t.`transaction_id`)  / ((100 - f.pph)/100)
ELSE (SELECT SUM(amt_claim) FROM transaction_additional_shrink WHERE transaction_id = t.`transaction_id`)
END 
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
WHERE fc.freight_cost_id = t.`freight_cost_id`   
) 
ELSE 0 END),0)
ELSE 0 END) -
SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
THEN (SELECT  SUM((percent_taken / 100) * t.freight_quantity) 
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.freight_price 
WHEN t.transaction_type = 1 AND t.delivery_status = 2
THEN (SELECT SUM((percent_taken / 100) * t.freight_quantity) *
(SELECT CASE WHEN tx.tax_category = 1 THEN t.freight_price / ((100 - f.pph)/100)
ELSE t.freight_price END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t.`freight_cost_id`)
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) - 
(SELECT SUM(percent_taken / 100) *
COALESCE((SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT SUM(amt_claim) FROM transaction_shrink_weight 
WHERE transaction_id = t.`transaction_id` ) / ((100 - f.pph)/100)
ELSE (SELECT SUM(amt_claim) FROM transaction_shrink_weight 
WHERE transaction_id = t.`transaction_id` ) END
FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
WHERE fc.freight_cost_id = t.`freight_cost_id`),0)
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1 ) - 
(SELECT SUM(percent_taken / 100) *
COALESCE((CASE WHEN t.`transaction_date` > '2022-09-15' 
THEN 
(SELECT SUM(amt_claim) FROM transaction_additional_shrink 
WHERE transaction_id = t.`transaction_id`) ELSE 0 END),0)
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1)
ELSE 0 END),2) AS freight_amount,


ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 THEN t.unloading_price
WHEN t.transaction_type = 1 AND t.delivery_status <> 1
THEN t.unloading_price ELSE 0 END) -
SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
THEN (SELECT ((SUM(quantity))/t.quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.unloading_price 
WHEN t.transaction_type = 1 AND t.delivery_status = 2
THEN (SELECT ((SUM(quantity))/t.quantity) * t.`unloading_price`
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) ELSE 0 END),2) AS unloading_amount,

ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 
THEN t.handling_quantity * t.handling_price
WHEN t.transaction_type = 1 AND t.delivery_status <> 1
THEN t.handling_quantity * (SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT price_converted FROM vendor_handling_cost 
WHERE handling_cost_id = t.`handling_cost_id`) / ((100 - vh.pph)/100)
ELSE (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t.`handling_cost_id`)  END
FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id 
WHERE vhc.handling_cost_id = t.`handling_cost_id`)
ELSE 0 END) -
SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
THEN (SELECT SUM((t.`handling_quantity` * (percent_taken/100))) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.handling_price 
WHEN t.transaction_type = 1 AND t.delivery_status = 2
THEN (SELECT SUM((t.`handling_quantity` * (percent_taken/100))) * (SELECT CASE WHEN tx.tax_category = 1 
THEN (SELECT price_converted FROM vendor_handling_cost 
WHERE handling_cost_id = t.`handling_cost_id`) / ((100 - vh.pph)/100)
ELSE (SELECT price_converted FROM vendor_handling_cost 
WHERE handling_cost_id = t.`handling_cost_id`) 
END
FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id 
WHERE vhc.handling_cost_id = t.`handling_cost_id`)
FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) 
ELSE 0 
END)
,2) AS handling_amount  
FROM `transaction` t
LEFT JOIN freight_cost fc ON fc.`freight_cost_id` = t.`freight_cost_id`
WHERE 1=1 AND t.local_sales != 0
{$whereAvailableProperty}
AND t.delivery_status != 1
AND (CASE WHEN t.`transaction_type` = 2 THEN (SELECT a.assign_po FROM sales a LEFT JOIN shipment b ON a.`sales_id` = b.`sales_id` WHERE b.`shipment_id` = t.`shipment_id`)
ELSE 1 END) = 1 
GROUP BY SUBSTRING(t.slip_no,1,3)  ,  t.`slip_no`, t.unloading_date
) ayam GROUP BY stockpile_code 
ORDER BY stockpile ASC
 ";
    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
    while ($rowContent = $resultContent->fetch_object()) {
        $startDate = $rowContent->start_date;
        $stockpile_code = $rowContent->stockpile_code;
        $stockpile = $rowContent->stockpile;
        $qty_available = $rowContent->qty_available;
        $pks_amount = $rowContent->pks_amount;
        $freight_amount = $rowContent->freight_amount;
        $unloading_amount = $rowContent->unloading_amount;
		$handling_amount = $rowContent->handling_amount;
		
        if($rowContent->handling_amount < 0){
			//$handling_amount = 0;
		}else{
			//$handling_amount = $rowContent->handling_amount;
		}
		
        $totalPksKg = $pks_amount / ($qty_available);
        $totalFreightKg = $freight_amount / ($qty_available);
        $totalUnloadingKg = $unloading_amount / ($qty_available);
		$totalHandlingKg = $handling_amount / ($qty_available);
		
		if($rowContent->handling_amount < 0){
			//$totalHandlingKg = 0;
		}else{
			//$totalHandlingKg = $handling_amount / ($qty_available);
		}
        
        $total = $pks_amount + $freight_amount + $unloading_amount + $handling_amount;
        $totalKg = $totalPksKg + $totalFreightKg + $totalUnloadingKg + $totalHandlingKg;
        ?>
        <tr>
            <td><a href="#" id="detail|<?php echo $period; ?>|<?php echo $stockpile_code; ?>"
                   role="button"><?php echo $stockpile; ?></a></td>
            <td><?php echo $startDate; ?></td>
            <td style="text-align: right"><?php echo number_format($qty_available, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($pks_amount, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($totalPksKg, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($freight_amount, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($totalFreightKg, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($unloading_amount, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($totalUnloadingKg, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($handling_amount, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($totalHandlingKg, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($total, 2, ".", ","); ?></td>
            <td style="text-align: right"><?php echo number_format($totalKg, 2, ".", ","); ?></td>
        </tr>

        <?php
        $qtyTotal = $qtyTotal + $qty_available;
        $grandTotal = $grandTotal + $total;
    }
    ?>
    <tr>
        <td colspan="2" style="text-align: center">Total</td>
        <td style="text-align: right"><?php echo number_format($qtyTotal, 2, ".", ","); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right"><?php echo number_format($grandTotal, 2, ".", ","); ?></td>
        <td></td>
    </tr>
    </tbody>
</table>

<div id="addDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="detailForm" method="post" style="margin: 0px;" action="reports/detailEndStock-xls.php">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>
            <h3 id="addDetailModalLabel">Detail End Stock</h3>
        </div>


        <div class="modal-body" id="addDetailModalForm">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>
            <button class="btn btn-success">Download XLS</button>
            <!--<button class="btn btn-primary">Submit</button>-->
        </div>
    </form>
</div>

<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowReturnInvoice = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 49) {
            $allowReturnInvoice = true;
        }
    }
}


$invId = $myDatabase->real_escape_string($_POST['invId']);
$totalPrice = '';
$freight_id = '';
$labor_id = '';
$vendor_handling_id = '';
$sql = "SELECT inv.inv_notim_no, pp.invoice_no, inv.due_date_inv AS invoice_date, pp.tax_invoice, sp.stockpile_name AS payment_location,pp.payment_method, pp.payment_for AS payment_for2,
CASE WHEN pp.payment_method = '1' THEN 'Payment' 
	WHEN pp.payment_method = '2' THEN 'Down Payment' ELSE NULL END AS Payment, 
CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE 'IN' END AS tipe, 
CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
	WHEN pp.payment_for = 1 THEN 'Local Sales' 
	WHEN pp.payment_for = 2 THEN 'Freight Cost' 
	WHEN pp.payment_for = 9 THEN 'Handling Cost' 
	WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For,
pp.periodeFrom, pp.periodeTo, sl.stockpile_name AS stockpile_location, 
CASE WHEN pp.freight_id IS NOT NULL THEN fr.freight_supplier
WHEN pp.labor_id IS NOT NULL THEN lr.labor_name
WHEN pp.vendor_handling_id IS NOT NULL THEN hr.handling_name
ELSE '' END AS vendorName, 
CASE WHEN pp.freight_id IS NOT NULL THEN fb.bank_name
WHEN pp.labor_id IS NOT NULL THEN lb.bank_name
WHEN pp.vendor_handling_id IS NOT NULL THEN hb.bank_name
ELSE '' END AS bank_name,
CASE WHEN pp.freight_id IS NOT NULL THEN fb.beneficiary
WHEN pp.labor_id IS NOT NULL THEN lb.beneficiary
WHEN pp.vendor_handling_id IS NOT NULL THEN hb.beneficiary
ELSE '' END AS beneficiary,
CASE WHEN pp.freight_id IS NOT NULL THEN   fb.account_no
WHEN pp.labor_id IS NOT NULL THEN lb.account_no
WHEN pp.vendor_handling_id IS NOT NULL THEN hb.account_no
ELSE '' END AS account_no,
CASE WHEN pp.urgent_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS urgentType, pp.remarks, inv.file1, inv.idPP, inv.invoice_status, inv.status_payment, pp.freight_id,pp.labor_id,pp.vendor_handling_id,
inv.total_ppn, inv.total_pph, inv.amount_converted, inv.claimPayment, inv.claimPaymentRemarks          
FROM invoice_sales_oa inv 
LEFT JOIN pengajuan_payment_sales_oa pp ON inv.idPP = pp.idPP
LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
LEFT JOIN stockpile sl ON sl.stockpile_id = pp.stockpile_location
LEFT JOIN freight_local_sales fr ON fr.freight_id = pp.freight_id
LEFT JOIN freight_local_sales_bank fb ON fb.f_bank_id = pp.vendor_bank_id 
LEFT JOIN labor_local_sales lr ON lr.labor_id = pp.labor_id
LEFT JOIN labor_local_sales_bank lb ON lb.l_bank_id = pp.vendor_bank_id 
LEFT JOIN handling_local_sales hr ON hr.handling_id = pp.vendor_handling_id
LEFT JOIN handling_local_sales_bank hb ON hb.h_bank_id = pp.vendor_bank_id 
WHERE inv.inv_notim_id = {$invId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
    $invoice_status = $row->invoice_status;
    $status_payment = $row->status_payment;
    $freight_id = $row->freight_id;
    $labor_id = $row->labor_id;
    $vendor_handling_id = $row->vendor_handling_id;
    $claimPaymentRemarks = $row->claimPaymentRemarks;
    $claimPayment = $row->claimPayment;
    $amount_converted = $row->amount_converted;
    $total_pph = $row->total_pph;
    $total_ppn = $row->total_ppn;
    $payment_method = $row->payment_method;
	$payment_for = $row->payment_for2;
    $idPP = $row->idPP;
     /*$paymentStatus = $row->payment_status;
	 $invoiceStatus = $row->invoice_status;
	 $mutasi_status = $row->mutasi_status;
	 $edit_date = $row->edit_date;
	 $invoice_date2 = $row->invoice_date2;*/
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printInvoiceDetail').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#invoiceDetail").printThis();
//            $("#transactionContainer").hide();
        });
     /*$('#returnInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=return_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-invoice.php', {invoiceId: <?php echo $row->invoice_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });*/
		
		/*$("#returnInvoice").validate({
			rules: {
                returnInvoiceDate: "required"
            },
            messages: {
                returnInvoiceDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this invoice?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnInvoice").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invoiceId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-invoice.php', { invoiceId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });
			}
                    return false;
		});
		}
        });
		
		$('#jurnalInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
    
	$('#jurnalReturn').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_return&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		
	$('#jurnalAccrue').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_accrue',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });*/

        $("#returnInvoiceSalesOA").validate({
			rules: {
                returnInvoiceSalesDate: "required"
            },
            messages: {
                returnInvoiceSalesDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this Invoice?", function(form) {
                    if (form) {

            $.blockUI({ message: '<h4>Please wait...</h4>' });
			$('#loading').css('visibility','visible');
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnInvoiceSalesOA").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-invoice-local-sales.php', { invId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });

                $('#loading').css('visibility','hidden');
			}
                    return false;
		});
		}
        });

        $('#jurnalInvoiceSalesOA').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_sales_oa',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-invoice-local-sales.php', {invId: <?php echo $invId; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
    });
    
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/invoice-ls-oa.php', {}, iAmACallbackFunction);
    }
	
	$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>


<div id="invoiceDetail">
   
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->inv_notim_no; ?></td>
            
            
        </tr>
		<tr>
            <td width="24%"><b>Original Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_no; ?></td>
            <td width="24%"><b>Stockpile Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_location; ?></td>
            
        </tr>
        <tr>
            <td width="24%"><b>Invoice Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_date; ?></td>
            <td width="24%"><b>Vendor Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vendorName; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Tax Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->tax_invoice; ?></td>
            <td width="24%"><b>Bank Account</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->account_no; ?></td>
        </tr>
        <tr>
          
			<td width="24%"><b>Payment Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->payment_location; ?></td>
            <td width="24%"><b>Bank Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->bank_name; ?></td>
        </tr>
        <tr>
              <td width="24%"><b>Period From</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeFrom; ?></td>
            <td width="24%"><b>Beneficiary</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->beneficiary; ?></td>
        </tr>
		 <tr>
              <td width="24%"><b>Period To</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeTo; ?></td>
            <td width="24%"><b>Documents</b></td>
            <td width="2%">:</td>
            <td width="24%"><a href="<?php echo $row->file1 ?>" target="_blank">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>
        </tr>
		 
		<?php
        if($invoice_status == 2) {
            echo '<tr><td colspan="6" style="font-size: 14pt; font_weight: bold; color: red; text-align: center;">Returned</td></tr>';
        }
        ?>
		
		
    </table>
    
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr> 
                <td <?php if($row->remarks == '') echo 'style="height: 40px;"'; ?>><?php echo $row->remarks; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <?php
    if ($row->freight_id != ''){
        if ($payment_method == 2 && $payment_for == 2){
            $sql = "SELECT a.`qty`, a.`price`, a.`dpp`, b.`so_no`, c.`total_ppn_amount`, c.`total_pph_amount`, c.`grand_total` FROM pengajuan_sales_oa_dp a 
        LEFT JOIN sales_order b ON a.`so_id` = b.`so_id`
        LEFT JOIN pengajuan_payment_sales_oa c ON c.`idPP` = a.`idPP`
        WHERE a.idPP = {$idPP}";
        
            }else{

    	$sql = "SELECT t.*, fc.freight_id, sc.`shipment_no`, fc.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.customer_code, COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS amt_claim, ts.`trx_shrink_claim`,
                ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight_ls WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

                WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight_ls WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

                WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight_ls WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 

                WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight_ls WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight_ls WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim
            FROM `transaction` t
            LEFT JOIN freight_cost_local_sales fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight_local_sales f
                ON f.freight_id = fc.freight_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN customer v
				ON fc.vendor_id = v.customer_id
			LEFT JOIN shipment sc
		        ON sc.shipment_id = t.shipment_id
            LEFT JOIN pengajuan_sales_oa ps ON ps.`transaction_id` = t.`transaction_id`
            LEFT JOIN transaction_shrink_weight_ls ts
				ON t.transaction_id = ts.transaction_id
			LEFT JOIN transaction_additional_shrink_ls hsw
				ON t.transaction_id = hsw.transaction_id
            WHERE ps.`idPP` = {$row->idPP}
			ORDER BY t.transaction_date ASC";
    }
    }else if ($row->labor_id != ''){
        $sql = "SELECT t.*, fc.labor_id, sc.`shipment_no`, f.labor_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.customer_code, ps.qty
            FROM `transaction` t
            LEFT JOIN labor_cost_local_sales fc
                ON fc.labor_cost_id = t.unloading_cost_id
            LEFT JOIN labor_local_sales f
                ON f.labor_id = fc.labor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN customer v
				ON fc.vendor_id = v.customer_id
			LEFT JOIN shipment sc
		        ON sc.shipment_id = t.shipment_id
            LEFT JOIN pengajuan_sales_oa ps ON ps.`transaction_id` = t.`transaction_id`
            WHERE ps.`idPP` = {$row->idPP}
			ORDER BY t.transaction_date ASC";

    } else if ($row->vendor_handling_id != ''){
        $sql = "SELECT t.*, fc.handling_id, sc.`shipment_no`, f.handling_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.customer_code
            FROM `transaction` t
            LEFT JOIN handling_cost_local_sales fc
                ON fc.handling_cost_id = t.handling_cost_id
            LEFT JOIN handling_local_sales f
                ON f.handling_id = fc.handling_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN customer v
				ON fc.vendor_id = v.customer_id
			LEFT JOIN shipment sc
		        ON sc.shipment_id = t.shipment_id
            LEFT JOIN pengajuan_sales_oa ps ON ps.`transaction_id` = t.`transaction_id`
            WHERE ps.`idPP` = {$row->idPP}
			ORDER BY t.transaction_date ASC";

    }
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$totalPrice = 0;
$ppnPrice = 0;
$pphPrice = 0;
$dppTotalPrice = 0;
$grandTotal  = 0;
$amountPrice = 0;
$totalShrink = 0;
	?>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
            <?php if ($payment_method == 2 && $payment_for == 2){ ?>

                <th>No. Pengajuan</th>    
                <th>Sales Order</th>
                <th>Quantity</th>
                <th>Harga</th>
                <th>DPP</th>

                <?php }else{ ?> 

                <th>Slip No</th>
                <th>Transaction Date</th>
                <th>Shipment Code</th>
                <th>Vendor Code</th>
                <th>Vehicle No</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <?php }?>
            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {

    

        if ($payment_method == 2 && $payment_for == 2){ 
            $totalPrice = $row->dpp;
            $total_ppn = $row->total_ppn_amount;
            $total_pph = $row->total_pph_amount;
            $grandTotal = $row->grand_total;

            ?>
            <td><?php echo $idPP;?></td>
            <td><?php echo $row->so_no;?></td>
            <td style="text-align: right;"><?php echo number_format($row->qty, 2, ".", ",");?></td>
            <td style="text-align: right;"><?php echo number_format($row->price, 2, ".", ",");?></td>
            <td style="text-align: right;"><?php echo number_format($row->dpp, 2, ".", ",");?></td>

      <?php }else{ 
			if ($freight_id != ''){
		if($row->freight_rule == 1){
				$fp = $row->freight_price * $row->send_weight;
				$qty = $row->send_weight;
			}else{
				$fp = $row->freight_price * $row->freight_quantity;
				$qty = $row->freight_quantity;
			}
            $price = $row->freight_price;
			if($row->transaction_date >= '2015-10-05' && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
			    $dppTotalPrice = $fp;
				//$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;

                $dppShrinkPrice = $row->amt_claim;
				$hsw_amt_claim = $row->hsw_amt_claim;
			  } else{ 
			  if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $fp;
				//$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                $dppShrinkPrice = $row->amt_claim;
				$hsw_amt_claim = $row->hsw_amt_claim;
            }else {
					if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  ){
					$dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
					//$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                    $dppShrinkPrice = ($row->amt_claim) / ((100 - $row->pph_tax_value) / 100);
					$hsw_amt_claim = ($row->hsw_amt_claim) / ((100 - $row->pph_tax_value) / 100);
			} else {
                if($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
					//$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                    $dppShrinkPrice = ($row->amt_claim) / ((100 - $row->pph_tax_value) / 100);
					$hsw_amt_claim = ($row->hsw_amt_claim) / ((100 - $row->pph_tax_value) / 100);
				}else {
                  $dppTotalPrice = $fp;
				  //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                    $dppShrinkPrice = $row->amt_claim;
				    $hsw_amt_claim = $row->hsw_amt_claim;
               }
            }
		
		}
	}

    if($row->pph_tax_id != 0 || $row->pph_tax_id != '') {
        $pph = $dppTotalPrice * ($row->pph_tax_value / 100);
    } else {
        $pph = 0;
    }

    if($row->ppn_tax_id != 0 || $row->ppn_tax_id != '') {
        $ppn = $dppTotalPrice * ($row->ppn_tax_value / 100);
    } else {
        $ppn = 0;
    }

   // $amountPrice = $dppTotalPrice - ($dppShrinkPrice + $hsw_amt_claim) ;
    $amountPrice = $dppTotalPrice;
    $totalPrice = $totalPrice + $dppTotalPrice;
    $totalShrink = $totalShrink + $dppShrinkPrice + $hsw_amt_claim;

    
      
}else if ($labor_id != ''){ 

    $lp = $row->unloading_price * $row->qty;
	$qty = $row->qty;
    $price = $row->unloading_price;

    if($row->transaction_date >= '2015-10-05' && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
        $dppTotalPrice = $lp;
        //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
      } else{ 
      if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
        $dppTotalPrice = $lp;
        //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
    }else {
            if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  ){
            $dppTotalPrice = ($lp) / ((100 - $row->pph_tax_value) / 100);
            //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
    } else {
        if($row->pph_tax_category == 1) {
            $dppTotalPrice = ($lp) / ((100 - $row->pph_tax_value) / 100);
            //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
        }else {
          $dppTotalPrice = $lp;
          //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
       }
    }

}
}
            if($row->pph_tax_id != 0 || $row->pph_tax_id != '') {
                $pph = $dppTotalPrice * ($row->pph_tax_value / 100);
            } else {
                $pph = 0;
            }

            if($row->ppn_tax_id != 0 || $row->ppn_tax_id != '') {
                $ppn = $dppTotalPrice * ($row->ppn_tax_value / 100);
            } else {
                $ppn = 0;
            }

            $amountPrice = $dppTotalPrice;
            $totalPrice = $totalPrice + $amountPrice;

}else if ($vendor_handling_id != ''){ 

    $hp = $row->handling_price * $row->handling_quantity;
	$qty = $row->handling_quantity;
    $price = $row->handling_price;

    if($row->transaction_date >= '2015-10-05' && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
        $dppTotalPrice = $hp;
        //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
      } else{ 
      if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
        $dppTotalPrice = $hp;
        //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
    }else {
            if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  ){
            $dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);
            //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
    } else {
        if($row->pph_tax_category == 1) {
            $dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);
            //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
        }else {
          $dppTotalPrice = $hp;
          //$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
       }
    }

}
}

        if($row->pph_tax_id != 0 || $row->pph_tax_id != '') {
            $pph = $dppTotalPrice * ($row->pph_tax_value / 100);
        } else {
            $pph = 0;
        }

        if($row->ppn_tax_id != 0 || $row->ppn_tax_id != '') {
            $ppn = $dppTotalPrice * ($row->ppn_tax_value / 100);
        } else {
            $ppn = 0;
        }

        $amountPrice = $dppTotalPrice;
        $totalPrice = $totalPrice + $amountPrice;

}
	
	//$freightPrice = $fp;
	
	
    //$pphPrice = $pphPrice + $pph;
    //$ppnPrice = $ppnPrice + $ppn;
    $grandTotal = $amount_converted;
	 ?>
           
               
                <td><?php echo $row->slip_no;?></td>
                <td><?php echo $row->transaction_date;?></td>
                <td><?php echo $row->shipment_no;?></td>
                <td><?php echo $row->customer_code;?></td>
                <td><?php echo $row->vehicle_no;?></td>
                <td style="text-align: right;"><?php echo number_format($qty, 2, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($price, 2, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($amountPrice, 2, ".", ",");?></td>
                
                <?php } ?>
            </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>
        <tr>
        <?php if ($payment_method == 2 && $payment_for == 2){ ?>
        <td colspan="4" style="text-align: right;">DPP</td>
        <?php }else{ ?>
        <td colspan="7" style="text-align: right;">DPP</td> 
        <?php } ?>  
        <td style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ",")?></td>
       
        </tr>

        <tr>
        <?php if ($payment_method == 2 && $payment_for == 2){ ?>
        <td colspan="4" style="text-align: right;">Shrink Claim</td>
        <?php }else{ ?>
        <td colspan="7" style="text-align: right;">Shrink Claim</td>
        <?php } ?>  
        <td style="text-align: right;"><?php echo number_format($totalShrink, 2, ".", ",")?></td>

        <?php if ($payment_method == 1 && $payment_for == 2){ 
            
            $sqlDP = "SELECT b.`idPP`, b.`total_dpp`, b.`total_pph_amount`, b.`total_ppn_amount`, b.`grand_total`
FROM pengajuan_sales_oa_dp a
LEFT JOIN pengajuan_payment_sales_oa b ON a.`idPP` = b.`idPP`
WHERE a.`settle_idPP` = {$idPP} AND b.`payment_method` = 2 AND a.`inv_notim_id` <> 0 AND a.`payment_id` <> 0";   
$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
            if($resultDP !== false && $resultDP->num_rows > 0) {
                while($rowDP = $resultDP->fetch_object()) {
                    
        ?>
        <tr>
        <td colspan="6" style="text-align: right;">Down Payment</td>
        <td style="text-align: right;">Pengajuan No : <?php echo $rowDP->idPP ?></td>
        <td style="text-align: right;"><?php echo number_format($rowDP->total_dpp, 2, ".", ",")?></td>
        </tr>
        <?php }}} ?>
       
        </tr>
        <tr>
        <?php if ($payment_method == 2 && $payment_for == 2){ ?>
        <td colspan="4" style="text-align: right;">PPN</td>
        <?php }else{ ?>
        <td colspan="7" style="text-align: right;">PPN</td>
        <?php } ?>  
        <td style="text-align: right;"><?php echo number_format($total_ppn, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <?php if ($payment_method == 2 && $payment_for == 2){ ?>
        <td colspan="4" style="text-align: right;">PPh</td>
        <?php }else{ ?>
        <td colspan="7" style="text-align: right;">PPh</td>
        <?php } ?>  
        <td style="text-align: right;"><?php echo number_format($total_pph, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <?php if ($payment_method == 2 && $payment_for == 2){ ?>
        <td colspan="4" style="text-align: right;">Grand Total</td>
        <?php }else{ ?>
        <td colspan="7" style="text-align: right;">Grand Total</td>
        <?php } ?>  
        <td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ",")?></td>
       
        </tr>
        </tfoot>
    </table>
    
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printInvoiceDetail">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
		<?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {
?>
        <button class="btn btn-warning" id="jurnalInvoiceSalesOA">JP</button>
        <?php
}?>  
  </div>
</div>
<?php if($invoice_status != 2 && $status_payment != 1) {
?>
<form method="post" id="returnInvoiceSalesOA">
<input type="hidden" name="action" id="action" value="return_invoice_sales_oa" />
<input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnInvoiceSalesDate" name="returnInvoiceSalesDate"  data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="returnButton">Return</button>
</div>
</div>
</form>
<?php
  } 
}
?>

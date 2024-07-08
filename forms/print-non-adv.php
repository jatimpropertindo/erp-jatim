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


$sa_id = $myDatabase->real_escape_string($_POST['sa_id']);
$totalPrice = '';
$advance = '';
$sql = "SELECT a.*, b.nama,  e.shipment_no, CONCAT(g.level,' ',f.divisi) AS jabatan, h.stockpile_name, j.user_name,i.general_vendor_name
		FROM non_adv_settle a
		LEFT JOIN perdin_user b ON a.id_user = b.id_user
		
		LEFT JOIN shipment e ON e.shipment_id = a.shipment_id
		LEFT JOIN general_vendor i ON i.general_vendor_id = a.id_user
		LEFT JOIN perdin_level g ON g.level_id = i.level_id 
		LEFT JOIN perdin_divisi f ON f.div_id = i.div_id
		LEFT JOIN stockpile h ON h.stockpile_id = a.stockpile_id
		LEFT JOIN user j ON j.user_id = a.entry_by
		WHERE sa_id = {$sa_id}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
	$sa_method = $row->sa_method;
	//$origin2 = $row->origin2;
	$tanggal = $row->tanggal;
    $stockpile_name = $row->stockpile_name;
	$docs = $row->docs;
	$user_name = $row->user_name;
    // $paymentStatus = $row->payment_status;
	// $invoiceStatus = $row->invoice_status;
	// $mutasi_status = $row->mutasi_status;
	// $edit_date = $row->edit_date;
	// $invoice_date2 = $row->invoice_date2;
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printPerdin').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#perdin").printThis();

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
			submitHandler: function(form) {
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
        });*/
		$("#uploadAdvanceNonPerdin").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
               // _method: 'INSERT',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0) //if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/non-adv_settle.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
		
    });
    
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/non-adv_settle.php', {}, iAmACallbackFunction);
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


<div id="perdin">
<br>
	<br>
	<br>
	<br>
   <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td colspan="6" style="text-align: center; font-size: 14pt; font-weight: 600; ">
                PT. JATIM PROPERTINDO JAYA
            </td>
        </tr>
          <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">JL. MELUR NO.15 A Rt/Rw 04/04, PADANG TERUBUK, SENAPELAN,</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">KOTAMADYA  PEKANBARU, RIAU, INDONESIA 28155</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">TELP.+62-761-44633 FAX.+62-761-44622</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">E-MAIL : support@jatimpropertindo.com</td></tr>
    </table>
	<hr style="border: solid; border-width: 2px;">
	
	<table width="100%" style="table-layout:fixed; font-size: 9pt;">
        
          <tr><td colspan="6" style="text-align: center; font-size: 10pt; font-weight: 600;" >FORM ADVANCE</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt;">NO : <?php echo $row->sa_no; ?></td></tr>
		  
    </table>
	<br>
	<br>
	
    <table width="50%" style="table-layout:fixed; font-size: 9pt;">
	
		
        <tr>
			<td width="5%"></td>
            <td width="15%"><b>Nama</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->general_vendor_name; ?></td>
			
			
    
        </tr>
        <tr>
			<td width="5%"></td>
            <td width="10%"><b>Jabatan</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->jabatan; ?></td>
            
        </tr>
		<tr>
			<td width="5%"></td>
            <td width="10%"><b>Keperluan</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->remarks; ?></td>
            
        </tr>
		
		
    </table>
    
   <br>
    <?php
	
	if($row->sa_method == 2){
	$sql = "SELECT a.*
			FROM non_adv_detail a
			
			WHERE a.sa_id = {$sa_id}";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	
	}else{
	
	/*$sql = "SELECT a.items,a.notes, CONCAT(ROUND(a.qty,2), ' (',  b.`uom_type`, ')') AS qty, a.price, a.amount,
			COALESCE((SELECT SUM(amount) FROM non_dp WHERE settle_id = a.settle_id),0) AS advance
			FROM non_settle_detail a
			LEFT JOIN uom b ON b.`idUOM` = a.`uom`
			WHERE a.sa_id = {$sa_id}";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);	
		*/
	}

 
	?>
    <table width="50%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                
                
                <th style="text-align: center;">Keterangan</th>
				
				<th style="text-align: center;">Jumlah</th>
                
				
                
            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {
		
		
		$totalPrice = $totalPrice + $row->amount;
		$advance = 0;
		$grandTotal = $totalPrice - $advance;
	 ?>
           
               
                <td><?php echo $row->keterangan;?></td>
                
                <td style="text-align: right;"><?php echo number_format($row->amount, 2, ".", ",");?></td>
                
            
            </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>
		<?php 
		if($sa_method == 1){
		?>
		<tr>
        <td colspan="0" style="text-align: right;">Total</td>
        <td style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ",")?></td>
       
        </tr>
		
		<tr>
        <td colspan="0" style="text-align: right;">Advance</td>
        <td style="text-align: right;">(<?php echo number_format($advance, 2, ".", ",")?>)</td>
       
        </tr>
		<?php 
		}
		?>
        <tr>
        <td colspan="0" style="text-align: right;">Grand Total</td>
        <td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ",")?></td>
       
        </tr>
        </tfoot>
    </table>
   
	<br>
	<table width="100%">
        <tr>
            <td width="45%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
                    <tr>
                        <td width="28%"><b>Dikeluarkan di</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $stockpile_name; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Tanggal</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $tanggal; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Dibuat Oleh</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $user_name; ?></td>
                    </tr>
                </table>
            </td>
            <td width="55%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt; height: 186px;">
                    <thead>
                        <tr>
                            <th style="vertical-align: top; height: 30px; text-align: center;">Karyawan </th>
                            
                            <th style="vertical-align: top; height: 30px; text-align: center;">Menyetujui</th>

							
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          
                            <td style="width: 25%; height: 40px;"></td>
                            
							<td style="width: 25%; height: 40px;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printPerdin">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>

  </div>
</div>

<?php
    // </editor-fold>
}
?>
<hr>
<br>
<form method="post" id="uploadAdvanceNonPerdin" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="upload_advance_non_perdin" />
<input type="hidden" name="sa_id" id="sa_id" value="<?php echo $sa_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Upload Documents<span style="color: red;">*</span></label>
<input type="file" class="span12" id="file" name="file">
<br>
<br>
<?php if ($docs != '') { ?>
<a href="<?php echo $docs; ?>" target="_blank" role="button" title="view file">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
<?php } ?>
<br>
<br>
<button class="btn btn-success" >Submit</button>
</div>
</div>
</form>
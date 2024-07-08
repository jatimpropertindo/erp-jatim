<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';



$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$pprice_id = '';
$adjustment = '';
$adjustmentAcc = '';
$adjustmentNotes = '';
$adjustmentDate = '';
$ppn = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['pprice_id']) && $_POST['pprice_id'] != '') {
    
    $pprice_id = $_POST['pprice_id'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT a.pprice_id, d.`stockpile_name`, a.`po_adj_no`, a.`contract_adj_no`, b.`contract_no`, a.`harga_awal`, a.`harga_akhir`, a.`adjustment_price`,
    c.`quantity`, a.notes, a.`input_date`, e.`user_name` AS inputby, a.`edit_date`, f.`user_name` AS editby,
    CASE WHEN a.status = 0 THEN 'Pengajuan' ELSE 'Selesai' END AS status_proses, a.upload_doc, a.upload_doc_awal, g.vendor_name, DATE_FORMAT(a.adj_date,'%d/%m/%Y') AS adj_date,
    CASE WHEN a.ppn_awal = 0 THEN 'Exlude' ELSE 'Include' END AS ppn_awal1, a.ppn_awal,
    CASE WHEN a.ppn_akhir = 0 THEN 'Exlude' ELSE 'Include' END AS ppn_akhir1, a.ppn_akhir,
    a.vendor_id, a.contract_id, a.stockpile_id
    FROM contract_adjustment_pprice a
    LEFT JOIN contract b ON a.`contract_id` = b.`contract_id`
    LEFT JOIN stockpile_contract c ON c.`contract_id` = a.`contract_id`
    LEFT JOIN stockpile d ON d.`stockpile_id` = a.`stockpile_id`
    LEFT JOIN `user` e ON e.`user_id` = a.`input_by`
    LEFT JOIN `user` f ON f.`user_id` = a.`edit_by`
    LEFT JOIN vendor g ON g.vendor_id = b.vendor_id
    WHERE a.`pprice_id` = {$pprice_id}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $pprice_id = $rowData->pprice_id;
		$stockpile_name = $rowData->stockpile_name;
        $po_adj_no = $rowData->po_adj_no;
        $contract_adj_no = $rowData->contract_adj_no;
		$contract_no = $rowData->contract_no;
		$harga_awal = $rowData->harga_awal;
		$harga_akhir = $rowData->harga_akhir;
		$adjustment_price = $rowData->adjustment_price;
		$quantity = $rowData->quantity;
		$notes = $rowData->notes;
		$vendor_name = $rowData->vendor_name;
		$upload_doc = $rowData->upload_doc;
        $upload_doc_awal = $rowData->upload_doc_awal;
        $adj_date = $rowData->adj_date;
        $ppn_awal1 = $rowData->ppn_awal1;
        $ppn_awal = $rowData->ppn_awal;
        $ppn_akhir1 = $rowData->ppn_akhir1;
        $ppn_akhir = $rowData->ppn_akhir;
        $stockpile_id = $rowData->stockpile_id;
        $contract_id = $rowData->contract_id;
        $vendor_id = $rowData->vendor_id;
        
    }else {
    
 /*   if(isset($_SESSION['adjustment'])) {
		$pprice_id = $_SESSION['adjustment']['pprice_id'];
        $contractId = $_SESSION['adjustment']['contractId'];
        $adjustment = $_SESSION['adjustment']['adjustment'];
        $adjustmentAcc = $_SESSION['adjustment']['adjustmentAcc'];
		$adjustmentNotes = $_SESSION['adjustment']['adjustmentNotes'];
		$adjustmentDate = $_SESSION['adjustment']['adjustmentDate'];
		$contractNo = $_SESSION['adjustment']['contractNo'];
		$quantity = $_SESSION['adjustment']['quantity'];
		$ppn = $_SESSION['adjustment']['ppn'];
     
    }*/
}
    // </editor-fold>
    
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $('#adjustment').number(true, 10);
		
		 
		
		/*$('#contractId').change(function() {
            resetContractAdjustmentPriceDetail(' ');
            
            if(document.getElementById('contractId').value != '') {
				getContractAdjustmentPriceDetail($('select[id="contractId"]').val());
            } 
        });
        
		
		function resetContractAdjustmentPriceDetail() {
        document.getElementById('contractNo').value = '';
        document.getElementById('quantity').value = '';
		document.getElementById('price').value = '';
		document.getElementById('notim').value = '';
		//document.getElementById('availableQuantity').value = '';
		
    }
		function getContractAdjustmentPriceDetail(contractAdjustmentPriceDetail) {
        
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getContractAdjustmentPriceDetail',
                        contractAdjustmentPriceDetail: contractAdjustmentPriceDetail
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('contractNo').value = returnVal[1];
                        document.getElementById('quantity').value = returnVal[2];
						document.getElementById('price').value = returnVal[3];
						document.getElementById('notim').value = returnVal[4];
						//document.getElementById('availableQuantity').value = returnVal[3];
						
                    }
                }
            });
        
	}*/
       $("#adjustmentDataForm").validate({
            rules: {
                contractId: "required",
                adjustment: "required",
                adjustmentAcc: "required",
				adjustmentDate: "required",
				adjustmentNotes: "required",
				ppn: "required"
			},
            messages: {
                contractId: "PO Number is a required field.",
                adjustment: "Adjustment is a required field.",
                adjustmentAcc: "Account is a required field.",
				adjustmentDate: "Date is a required field.",
				adjustmentNotes: "This is a required field.",
				ppn: "PPN is a required field."
                
            },
            submitHandler: function(form) {
                $('#submitButton').attr("disabled", true);

                $.blockUI({ message: '<h4>Please wait...</h4>' });
			    $('#loading').css('visibility','visible');

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#adjustmentDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalpprice_id').value = returnVal[3];
                                
                              //  $('#dataContent').load('forms/adjustment-price.php', { pprice_id: returnVal[3] }, iAmACallbackFunction2);

                              $('#dataContent').load('views/adjustment-price.php', {}, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#submitButton').attr("disabled", false);
                        }
                    }
                });
                $('#loading').css('visibility','hidden');
            }
        });
    });
</script>

<script type="text/javascript">
                    
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

<form method="post" id="adjustmentDataForm">
    <input type="hidden" name="action" id="action" value="adjustment_price_data" />
	<input type="hidden" id="pprice_id" name="pprice_id" value="<?php echo $pprice_id;?>">
    <input type="hidden" id="vendor_id" name="vendor_id" value="<?php echo $vendor_id;?>">
    <input type="hidden" id="stockpile_id" name="stockpile_id" value="<?php echo $stockpile_id;?>">
    <input type="hidden" id="contract_id" name="contract_id" value="<?php echo $contract_id;?>">
    <div class="row-fluid"> 
        <div class="span3 lightblue">
            <label>Vendor</label>
            <input readonly type="text" class="span12" tabindex="" id="vendor_name" name="vendor_name" value="<?php echo $vendor_name; ?>">
        </div>
        <div class="span3 lightblue">
            <label>No. PO Adj</label>
            <input readonly type="text" class="span12" tabindex="" id="po_adj_no" name="po_adj_no" value="<?php echo $po_adj_no; ?>">
        </div>
        <div class="span3 lightblue">
            <label>No. Kontrak Adj</label>
            <input readonly type="text" class="span12" tabindex="" id="contract_adj_no" name="contract_adj_no" value="<?php echo $contract_adj_no; ?>">
        </div>
		<div class="span3 lightblue">
            <label>No. Kontrak Lama</label>
            <input readonly type="text" class="span12" tabindex="" id="contract_no" name="contract_no" value="<?php echo $contract_no; ?>">
        </div>
    </div>
	<div class="row-fluid"> 
        <div class="span3 lightblue">
            <label>Stockpile</label>
            <input readonly type="text" class="span12" tabindex="" id="stockpile_name" name="stockpile_name" value="<?php echo $stockpile_name; ?>">
        </div>
        <div class="span3 lightblue">
        	<label>Harga Baru</label>
            <input readonly type="text" class="span12" tabindex="" id="harga_akhir" name="harga_akhir" value="<?php echo number_format($harga_akhir, 10, ".", ",");?>">
        </div>
		<div class="span3 lightblue">
        	<label>Harga Awal</label>
            <input readonly type="text" class="span12" tabindex="" id="harga_awal" name="harga_awal" value="<?php echo number_format($harga_awal, 10, ".", ",");?>">
        </div>
		<div class="span3 lightblue">
        	<label>Selisih Harga</label>
            <input readonly type="text" class="span12" tabindex="" id="adjustment_price" name="adjustment_price" value="<?php echo number_format($adjustment_price, 10, ".", ",");?>">
        </div>
		<!--<div class="span3 lightblue">
        	<label>Available Quantity</label>
            <input readonly type="text" class="span12" tabindex="" id="availableQuantity" name="availableQuantity" value="<?php //echo number_format($availableQuantity, 0, ".", ",");?>">
        </div>-->
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Kuantiti<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo number_format($quantity, 2, ".", ",");?>">
        </div>
        <div class="span3 lightblue">
            <label>Tanggal Adj<span style="color: red;">*</span></label>
			<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="adj_date" name="adj_date" value="<?php echo $adj_date; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span3 lightblue">
		    <label>PPN Awal<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="ppn_awal1" name="ppn_awal1" value="<?php echo $ppn_awal1; ?>" >
            <input type="hidden" class="span12" tabindex="" id="ppn_awal" name="ppn_awal" value="<?php echo $ppn_awal; ?>" >
		</div>
		<div class="span3 lightblue">
            <label>PPN Baru<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="ppn_akhir1" name="ppn_akhir1" value="<?php echo $ppn_akhir1; ?>" >
            <input type="hidden" class="span12" tabindex="" id="ppn_akhir" name="ppn_akhir" value="<?php echo $ppn_akhir; ?>" >
        </div>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Dokumen Awal<span style="color: red;">*</span></label>
            <a href="<?php echo $upload_doc_awal ?>" target="_blank">Download Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <input type="hidden" class="span12" tabindex="" id="upload_doc_awal" name="upload_doc_awal" value="<?php echo $upload_doc_awal; ?>" >
        </div>
        <div class="span3 lightblue">
        <label>Dokumen Adj<span style="color: red;">*</span></label>
            <a href="<?php echo $upload_doc ?>" target="_blank">Download Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <input type="hidden" class="span12" tabindex="" id="upload_doc" name="upload_doc" value="<?php echo $upload_doc; ?>" >
        </div>
        <div class="span3 lightblue">
            
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span6 lightblue">
            <label>Notes<span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        <div class="span6 lightblue">
            
        </div>
       
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

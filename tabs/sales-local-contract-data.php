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

$date = new DateTime();

// <editor-fold defaultstate="collapsed" desc="Variable for Sales Data">

$salesId = '';
$salesNo = '';
$salesDate = $date->format('d/m/Y');
$salesType = 1;
$customerId = '';
$stockpileId = '';
$destination = '';
$notes = '';
$currencyId = '';
$exchangeRate = '';
$price = '';
$quantity = '';
$totalShipment = '';
$bkp_jkp = '';
$barang = '';
$peb_fp_no = '';
$boolEdit = true;

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['salesId']) && $_POST['salesId'] != '') {
    
    $salesId = $_POST['salesId'];
    
    
    
    // <editor-fold defaultstate="collapsed" desc="Query for Sales Data">
    
    $sql = "SELECT sl.*, DATE_FORMAT(sl.sales_con_date, '%d/%m/%Y') AS sales_date2,
    DATE_FORMAT(sl.layDate, '%d/%m/%Y') AS layDate2,
    DATE_FORMAT(sl.cancelDate, '%d/%m/%Y') AS cancelDate2
    FROM sales_local sl
LEFT JOIN account a ON sl.account_id = a.account_id
    WHERE sl.sales_con_id = {$salesId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		//$shipmentNo = $rowData->shipment_no;
        $sales_con_no = $rowData->sales_con_no;
        $sales_con_date = $rowData->sales_date2;
        $sales_con_type = $rowData->sales_con_type;
        $customerId = $rowData->customer_id;
        $stockpileId = $rowData->stockpile_id;
		$accountId = $rowData->account_id;
        $destination = $rowData->destination;
        $notes = $rowData->notes;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $quantity = $rowData->quantity;
		//$qty_bl = $rowData->qty_bl;
        $exchangeRate = $rowData->exchange_rate;
       // $totalShipment = $rowData->total_shipment;
		//$shipmentDate = $rowData->shipment_date2;
		$bkp_jkp = $rowData->bkp_jkp;
		//$barang = $rowData->barang;
		$peb_fp_no = $rowData->peb_fp_no;
		$peb_fp_date = $rowData->peb_fp_date;
		$pebDate = $rowData->pebDate;
		$salesStatus = $rowData->sales_con_status;
		$layDate = $rowData->layDate2;
		$cancelDate = $rowData->cancelDate2;
        $tm = $rowData->tm;
        $ncv = $rowData->ncv;
        $fm = $rowData->fm;
        $etc1 = $rowData->etc1;
        $etc2 = $rowData->etc2;
        $inv_rules = $rowData->inventory_rule;
        $sales_rule = $rowData->sales_rule;
		
		if($usedStatus == 1){
		$readonlyProperty = ' readonly ';
		$boolEdit = false;
		}
    }
    
    // </editor-fold>
    
} /*else {
    if(isset($_SESSION['sales'])) {
		$shipmentNo = $_SESSION['sales']['shipmentNo'];
        $salesNo = $_SESSION['sales']['salesNo'];
        $salesDate = $_SESSION['sales']['salesDate'];
        $salesType = $_SESSION['sales']['salesType'];
        $customerId = $_SESSION['sales']['customerId'];
		$accountId = $_SESSION['sales']['accountId'];
        $stockpileId = $_SESSION['sales']['stockpileId'];
        $destination = $_SESSION['sales']['destination'];
        $notes = $_SESSION['sales']['notes'];
        $currencyId = $_SESSION['sales']['currencyId'];
        $exchangeRate = $_SESSION['sales']['exchangeRate'];
        $price = $_SESSION['sales']['price'];
        $quantity = $_SESSION['sales']['quantity'];
        $totalShipment = $_SESSION['sales']['totalShipment'];
		$shipmentDate = $_SESSION['sales']['shipmentDate'];
		$bkp_jkp = $_SESSION['sales']['bkp_jkp'];
		$barang = $_SESSION['sales']['barang'];
		$peb_fp_no = $_SESSION['sales']['peb_fp_no'];
		$pebDate = $_SESSION['sales']['pebDate'];
		
    }
}*/

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
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $('#quantity').number(true, 2);
		$('#qty_bl').number(true, 2);
        
        $('#price').number(true, 10);
       
        
        
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
        
//        $('#exchangeRate').number(true, 2);
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });
		
		$('#salesType').change(function() {
            if(document.getElementById('salesType').value == 3) {
                $('#vendorLangsir').show();
				$('#stockpileLangsir').show();
            } else {
                $('#vendorLangsir').hide();
				$('#stockpileLangsir').hide();
            }
        });
        
        
		
		/*$('#stockpileId').change(function() {
            
			if(document.getElementById('stockpileId').value != '') {
                
				setStockpileContract($('select[id="stockpileId"]').val());
			}else{
				
			}
        });*/
		
		 $('#UpdatePriceRate').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSalesModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSalesModalForm').load('forms/priceRate-sales-local.php', {salesId: $('input[id="sales_con_id"]').val()});
            });
			
		$('#cancelSales').click(function(e){
                  $.ajax({
                      url: './data_processing.php',
                      method: 'POST',
                      data: 'action=cancel_sales&sales_Id=<?php echo $rowData->sales_id; ?>',
                      success: function(data) {
                          var returnVal = data.split('|');

                          if (parseInt(returnVal[4]) != 0)	//if no errors
                          {
                              alertify.set({ labels: {
                                  ok     : "OK"
                              } });
                              alertify.alert(returnVal[2]);

                              if (returnVal[1] == 'OK') {
                                  $('#dataContent').load('contents/sales-local-contract.php', {}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });
			  
		$('#returnSales').click(function(e){
                  $.ajax({
                      url: './data_processing.php',
                      method: 'POST',
                      data: 'action=return_sales&sales_Id=<?php echo $rowData->sales_id; ?>',
                      success: function(data) {
                          var returnVal = data.split('|');

                          if (parseInt(returnVal[4]) != 0)	//if no errors
                          {
                              alertify.set({ labels: {
                                  ok     : "OK"
                              } });
                              alertify.alert(returnVal[2]);

                              if (returnVal[1] == 'OK') {
                                  $('#dataContent').load('contents/sales-local-contract.php', {}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });
        
        $("#salesDataForm").validate({
            rules: {
                //shipmentNo: "required",
				salesNo: "required",
                salesDate: "required",
				//shipmentDate: "required",
                salesType: "required",
                customerId: "required",
                stockpileId: "required",
                currencyId: "required",
                exchangeRate: "required",
                price: "required",
				accountId: "required",
				totalShipment: "required",
				bkp_jkp: "required",
                quantity: "required"
            },
            messages: {
				//shipmentNo: "Shipment Code is a required field.",
                salesNo: "Sales Agreement No. is a required field.",
                salesDate: "Sales Agreement Date is a required field.",
				//shipmentDate: "Shipment Date is a required field.",
                salesType: "Type is a required field.",
                customerId: "Buyer is a required field.",
                stockpileId: "Loading is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                price: "Price/KG is a required field.",
				accountId: "Account is a required field.",
				totalShipment: "Total Truck is a required field.",
				bkp_jkp: "BKP/JKP is a required field.",
                quantity: "Quantity (KG) is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#salesDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalSalesId').value = returnVal[3];
                                
                                $('#dataContent').load('contents/sales-local-contract.php', {}, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
		
		 $("#addSalesForm").validate({
            rules: {
                priceUpdate: "required",
				exchangeRateUpdate: "required"
            },
            messages: {
				priceUpdate: "Price/KG is a required field.",
                exchangeRateUpdate: "Exchange Rate is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#addSalesForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalSalesId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/sales-local-contract.php', { salesId: returnVal[3] }, iAmACallbackFunction2);
								$('#addSalesModal').modal('hide');
//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
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
            startView: 0,
            orientation: "bottom auto"
           
        });
		$('.datepicker2').datepicker({
            minViewMode: 1,
            todayHighlight: true,
            autoclose: true,
            startView: 1,
            orientation: "bottom auto"
        });
    });
	
	function setStockpileContract(stockpileId) {

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getStockpileContractShipment',
					stockpileId:stockpileId
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }
					
					

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);

                        $("#stockpileContractId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 0;
                    x.text = 'NONE';
                    document.getElementById('stockpileContractId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }



                    
                }
				//setContract(contract);
            }

        });

		}
</script>

<form method="post" id="salesDataForm">
    <input type="hidden" name="action" id="action" value="sales_local_contract_data" />
    <input type="hidden" name="sales_con_id" id="sales_con_id" value="<?php echo $salesId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Sales Contract No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" <?php echo $readonlyProperty; ?> tabindex="1" id="sales_con_no" name="sales_con_no" value="<?php echo $sales_con_no; ?>" maxlength="50">
        </div>
        <div class="span4 lightblue">
            <label>Sales Contract Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="sales_con_date" name="sales_con_date" value="<?php echo $sales_con_date; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        
    </div>
    <div class="row-fluid">   
    <div class="span3 lightblue">
		<label>Account <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
                    FROM account acc where acc.account_type IN (0,1) and acc.account_no like '400%' ORDER BY acc.account_name ASC", $accountId, '', "accountId", "account_id", "account_full", 
                    "", 3, "select2combobox100", 1, "", false);
			}else{
			createCombo("SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
				FROM account acc WHERE acc.account_id = {$accountId}", $accountId, '', "accountId", "account_id", "account_full", 
				"", 3, "select2combobox100", "", "", false);
			}
            ?>
        </div>
        
        <div class="span3 lightblue">
		<label>Buyer <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT cust.customer_id, cust.customer_name 
                    FROM customer cust WHERE cust.sales_category > 1 ORDER BY cust.customer_name ASC", $customerId, '', "customerId", "customer_id", "customer_name", 
                    "", 4, "select2combobox100", 1, "", false);
			}else{
			createCombo("SELECT cust.customer_id, cust.customer_name 
                    FROM customer cust WHERE cust.sales_category > 1 AND cust.customer_id = {$customerId}", $customerId, '', "customerId", "customer_id", "customer_name", 
                    "", 4, "select2combobox100", "", "", false);
			}
            ?>
        </div>
        <div class="span3 lightblue">
		<label>Sales Weight Rules <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Lowest' as info UNION
                    SELECT '1' as id, 'PKS Weight' as info UNION
                    SELECT '2' as id, 'Buyer Weight' as info UNION
                    SELECT '3' as id, 'SP Weight' as info;", $inv_rules, '', "inv_rules", "id", "info", 
                "", 5, "select2combobox100");
            ?>
        </div>
        <!--<div class="span3 lightblue">
		<label>Sales Rules <span style="color: red;">*</span></label>
            <?php
            /*createCombo("SELECT '0' as id, 'PKS-Buyer' as info UNION
                    SELECT '1' as id, 'SP-Buyer' as info UNION
                    SELECT '2' as id, 'PKS-SP' as info;", $sales_rule, '', "sales_rule", "id", "info", 
                "", 5, "select2combobox100");*/
            ?>
        </div>-->
    </div>
        </br>   
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Type <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="6" id="sales_con_type2" name="sales_con_type2" value="Commit">
            <input type="hidden" class="span12"  tabindex="6" id="sales_con_type" name="sales_con_type" value="1">
        </div>
		<div class="span3 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" <?php echo $readonlyProperty; ?> tabindex="7" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT cur.currency_id, cur.currency_code
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", 8, "select2combobox100","", "", false);
			}else{
			 createCombo("SELECT cur.currency_id, cur.currency_code
                    FROM currency cur
                    WHERE currency_id = {$currencyId}", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", 8, "select2combobox100","", "", false);
			}
            ?>
        </div>
        <div class="span3 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="9" <?php echo $readonlyProperty; ?> id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
       
    </div>

    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", 10, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Quantity(KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="11" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>
        <div class="span4 lightblue">
        <label>Destination</label>
            <input type="text" class="span12" tabindex="12" id="destination" name="destination" value="<?php echo $destination; ?>">
		<!--<label>Quantity B/L(KG) <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="15" id="qty_bl" name="qty_bl" value="<?php //echo $qty_bl; ?>">-->
        </div>
    </div>

	<div class="row-fluid">   
        <div class="span4 lightblue">
            <label>BKP / JKP <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="13" id="bkp_jkp2" name="bkp_jkp2" value="Commit">
            <input type="hidden" class="span12"  tabindex="13" id="bkp_jkp" name="bkp_jkp" value="1">
        </div>
        <div class="span4 lightblue">
            <label>Start Date </label>
           <input type="text" tabindex="14" placeholder="DD/MM/YYYY" tabindex="" id="layDate" name="layDate" value="<?php echo $layDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
            <label>End Date </label>
           <input type="text" tabindex="15" placeholder="DD/MM/YYYY" tabindex="" id="cancelDate" name="cancelDate" value="<?php echo $cancelDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
        <label>TM <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="16" id="tm" name="tm" value="<?php echo $tm; ?>">
        </div>
        <div class="span4 lightblue">
            <label>NCV <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="17" id="ncv" name="ncv" value="<?php echo $ncv; ?>">
        </div>
        <div class="span4 lightblue">
        <label>FM</label>
            <input type="text" class="span12" tabindex="18" id="fm" name="fm" value="<?php echo $fm; ?>">
		<!--<label>Quantity B/L(KG) <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="15" id="qty_bl" name="qty_bl" value="<?php //echo $qty_bl; ?>">-->
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
        <label>Etc <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="19" id="etc1" name="etc1" value="<?php echo $etc1; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Etc <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="20" id="etc2" name="etc2" value="<?php echo $etc2; ?>">
        </div>
        <div class="span4 lightblue">
        
        </div>
    </div>
	
    <div class="row-fluid">  
        <div class="span8 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="3" tabindex="21" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
			<?php
			//if($salesId != '' && $salesStatus != 3){
			?>
			<button class="btn btn-warning" id="UpdatePriceRate">Update Price & Rate</button>
			<?php //}
			?>
			
        </div>
    </div>
</form>
<?php
// if($salesId != '' && $salesStatus == 0){
			?>
<!--<div class="row-fluid">  
        <button class="btn btn-warning" id="cancelSales">Cancel Sales</button>
</div>
	<?php // }?>
	<br>
<?php//  if($returnStatus == 0){?>
			
<div class="row-fluid">  
        <button class="btn btn-warning" id="returnSales">Return Sales</button>
	</div>-->
<?php// }?>

<div id="addSalesModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addSalesModalLabel" aria-hidden="true">
        <form id="addSalesForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddSalesModal">×</button>
                <h3 id="addSalesModalLabel">Update Price & Rate</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           
            <input type="hidden" name="action" id="action" value="update_price_rate_sales_local" />
            <div class="modal-body" id="addSalesModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddSalesModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

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
$salesDate = '';
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
    
    $sql = "SELECT sl.*, DATE_FORMAT(sl.so_date,'%d/%m/%Y') AS so_date2
    FROM sales_order sl
    WHERE sl.so_id = {$salesId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();

		$so_no = $rowData->so_no;
        $salesOrderDate = $rowData->so_date2;
        $qtySo = $rowData->qty;
        $notes = $rowData->notes;
        $sales_con_id = $rowData->sales_con_id;
		
    }
    
    // </editor-fold>
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">

/*function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
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
}*/

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
		$('#qtySo').number(true, 2);
       // $('#price').number(true, 10);

       
       
        
        
      /*  if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }*/
        
//        $('#exchangeRate').number(true, 2);
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
       /* $('#currencyId').change(function() {
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
        });*/
        
        
		
		/*$('#stockpileId').change(function() {
            
			if(document.getElementById('stockpileId').value != '') {
                
				setStockpileContract($('select[id="stockpileId"]').val());
			}else{
				
			}
        });*/

      /*  $('#sales_con_id').change(function() {
            resetSalesContract(' ');
			if(document.getElementById('sales_con_id').value != '') {
                
				setSalesContract($('select[id="sales_con_id"]').val());
                
			}
		});*/
		
		/* $('#UpdatePriceRate').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSalesModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSalesModalForm').load('forms/priceRate-sales.php', {salesId: $('input[id="salesId"]').val()});
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
                                  $('#dataContent').load('contents/sales-local.php', {}, iAmACallbackFunction2);

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
                                  $('#dataContent').load('contents/sales-local.php', {}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });*/
        
        $("#salesDataForm").validate({
            rules: {
                sales_con_id: "required",
				salesOrderDate: "required",
                qtySo: "required"
            },
            messages: {
				sales_con_id: "This is a required field.",
                salesOrderDate: "This is a required field.",
                qtySo: "This is a required field."
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
                                
                                $('#dataContent').load('contents/sales-local-order.php', { }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
		
		/* $("#addSalesForm").validate({
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
                                
                                $('#dataContent').load('forms/sales-local.php', { salesId: returnVal[3] }, iAmACallbackFunction2);
								$('#addSalesModal').modal('hide');
//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });*/
		
		
    });
</script>

<script type="text/javascript">
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
		$('.datepicker2').datepicker({
            minViewMode: 1,
            todayHighlight: true,
            autoclose: true,
            orientation: "bottom auto",
            startView: 1
        });
    });

    function resetSalesContract() {
        document.getElementById('salesNo').value = '';
        //document.getElementById('salesDate').value = '';
        //document.getElementById('shipmentNo').value = '';
        //document.getElementById('shipmentDate').value = '';
        document.getElementById('accountNo').value = '';
        document.getElementById('accountId').value = '';
        document.getElementById('customerName').value = '';
        document.getElementById('customerId').value = '';
        document.getElementById('salesType2').value = '';
        document.getElementById('salesType').value = '';
        document.getElementById('price').value = '';
        document.getElementById('currencyName').value = '';
        document.getElementById('currencyId').value = '';
        document.getElementById('exchangeRate').value = '';
        document.getElementById('stockpileName').value = '';
        document.getElementById('stockpileId').value = '';
        document.getElementById('destination').value = '';
        //document.getElementById('product_name').value = '';
        //document.getElementById('bkp_jkp').value = '';
        //document.getElementById('peb_fp_no').value = '';
        //document.getElementById('pebDate').value = '';
        document.getElementById('qty_contract').value = '';
    }

    function setSalesContract(sales_con_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSalesContract',
                sales_con_id: sales_con_id
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');
                  

                    
					document.getElementById('salesNo').value = returnVal[0];
					//document.getElementById('salesDate').value = returnVal[1];
					//document.getElementById('shipmentNo').value = returnVal[2];
					//document.getElementById('shipmentDate').value = returnVal[3];
					document.getElementById('accountNo').value = returnVal[2];
					document.getElementById('accountId').value = returnVal[3];
					document.getElementById('customerName').value = returnVal[4];
					document.getElementById('customerId').value = returnVal[5];
					document.getElementById('salesType2').value = returnVal[6];
					document.getElementById('salesType').value = returnVal[7];
					document.getElementById('price').value = returnVal[8];
					document.getElementById('currencyName').value = returnVal[9];
					document.getElementById('currencyId').value = returnVal[10];
					document.getElementById('exchangeRate').value = returnVal[11];
					document.getElementById('stockpileName').value = returnVal[12];
                    document.getElementById('stockpileId').value = returnVal[13];
                    document.getElementById('destination').value = returnVal[14];
                   // document.getElementById('product_name').value = returnVal[15];
					//document.getElementById('bkp_jkp').value = returnVal[16];
                    //document.getElementById('peb_fp_no').value = returnVal[17];
                   // document.getElementById('pebDate').value = returnVal[18];
					document.getElementById('qty_contract').value = returnVal[19];
                    //document.getElementById('qty_bl').value = returnVal[1];

					
                }
            }
        });
    }
	
	/*function setStockpileContract(stockpileId) {

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

		}*/
</script>
<script type="text/javascript">
    <?php if ($salesId != ''){ ?>

setSalesContract(<?php echo $sales_con_id;?>);

    <?php } ?>
    </script>

<form method="post" id="salesDataForm">
    <input type="hidden" name="action" id="action" value="sales_order_data" />
    <input type="hidden" name="soId" id="soId" value="<?php echo $salesId; ?>" />
    <div class="row-fluid">
     
        <div class="span3 lightblue">
            <label>Sales Order No. <span style="color: red;">*</span></label>
         
            <input type="text" readonly class="span12"  id="so_no" name="so_no" value="<?php echo $so_no; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Sales Contract No. <span style="color: red;">*</span></label>
         
            <input type="text" readonly class="span12"  id="salesNo" name="salesNo" value="<?php echo $salesNo; ?>">
            <input type="hidden" readonly class="span12"  id="sales_con_id" name="sales_con_id" value="<?php echo $sales_con_id; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Sales Order Date <span style="color: red;">*</span></label>
            <input type="text"  placeholder="DD/MM/YYYY" tabindex="1" id="salesOrderDate" name="salesOrderDate" value="<?php echo $salesOrderDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>  
        <div class="span3 lightblue">
           <label>Qty Sales Order<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="qtySo" name="qtySo" value="<?php echo $qtySo; ?>">
        </div>

    </div>
    <div class="row-fluid">   
        <div class="span3 lightblue">
        <label>Account <span style="color: red;">*</span></label>
        <input type="text" readonly class="span12"  id="accountNo" name="accountNo" value="<?php echo $accountNo; ?>">
        <input type="hidden" readonly class="span12"  id="accountId" name="accountId" value="<?php echo $accountId; ?>">
        </div>
        <div class="span3 lightblue">
        <label>Buyer <span style="color: red;">*</span></label>
        <input type="text" readonly class="span12"  id="customerName" name="customerName" value="<?php echo $customerName; ?>" >
        <input type="hidden" readonly class="span12"  id="customerId" name="customerId" value="<?php echo $customerId; ?>" >
        </div>
        <div class="span3 lightblue">
		<label>type <span style="color: red;">*</span></label>
        <input type="text" readonly class="span12"  id="salesType2" name="salesType2" value="<?php echo $salesType2; ?>">
        <input type="hidden" readonly class="span12"  id="salesType" name="salesType" value="<?php echo $salesType; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12"  id="price" name="price" value="<?php echo $price; ?>">
        </div>
    </div>
    
    
    
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12"  id="currencyName" name="currencyName" value="<?php echo $currencyName; ?>">
            <input type="hidden" readonly class="span12"  id="currencyId" name="currencyId" value="<?php echo $currencyId; ?>">
        </div>
		<div class="span3 lightblue">
            <label>Exchange Rate to IDR<span style="color: red;">*</span></label>
            <input type="text" readonly class="span12"  id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12"  id="stockpileName" name="stockpileName" value="<?php echo $stockpileName; ?>">
            <input type="hidden" readonly class="span12"  id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Destination</label>
            <input type="text" readonly class="span12"  id="destination" name="destination" value="<?php echo $destination; ?>">
        </div>
       
    </div>
    <div class="row-fluid">  
    
        <div class="span3 lightblue">
            <label>Balance Qty Contract Sales <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12"  id="qty_contract" name="qty_contract" value="<?php echo $qty_contract; ?>">
        </div>
    </div>
	
    <div class="row-fluid">  
        <div class="span8 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="3" tabindex="3" id="notes" name="notes"><?php echo $notes; ?></textarea>
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
		<!--	<button class="btn btn-warning" id="UpdatePriceRate">Update Price & Rate</button>
			<?php //}
			?>
			
        </div>
    </div>
</form>
<?php
//if($salesId != '' && $salesStatus == 0){
			?>
<div class="row-fluid">  
        <button class="btn btn-warning" id="cancelSales">Cancel Sales</button>
</div>
	<?php //}?>
	<br>
<?php // if($salesId != '' && $returnStatus == 0){?>
			
<div class="row-fluid">  
        <button class="btn btn-warning" id="returnSales">Return Sales</button>
	</div>
<?php // }?>
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
    </div>-->

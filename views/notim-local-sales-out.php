<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'Notim Local Sales OUT';


//$transactionId = $myDatabase->real_escape_string($_POST['transactionId']);
$date = new DateTime();
$transactionDate = $date->format('d/m/Y');
$transactionDate2 = $date->format('d/m/Y');
$unloadingDate = $date->format('d/m/Y');


/*$sql = "SELECT t.*, DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date2, 
            DATE_FORMAT(t.unloading_date, '%d/%m/%Y') AS unloading_date2, 
            DATE_FORMAT(t.loading_date, '%d/%m/%Y') AS loading_date2, 
            CASE WHEN t.transaction_type = 1 THEN sc.stockpile_id ELSE sl.stockpile_id END AS stockpile_id, 
            con.vendor_id, t.vendor_id AS supplier_id, sl.sales_id, sl.customer_id, sh.payment_id AS sh_payment_id,
			CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transactionType2,
			tu.slip,
			(SELECT vendor_name FROM vendor WHERE vendor_id = con.vendor_id) AS vendorName, con.po_no 
        FROM transaction t 
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
		LEFT JOIN transaction_timbangan tu
			ON tu.transaction_id = t.t_timbangan
        WHERE t.transaction_id = {$transactionId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows == 1) {
    $row = $result->fetch_object();
    $stockpileId = $row->stockpile_id;
    $transactionType = 2;
	$transactionType2 = 'OUT';
    $unloadingDate = $row->unloading_date2;
    $loadingDate = $row->loading_date2;
    $vendorId = $row->vendor_id;
	$vendorName = $row->vendorName;
    $unloadingCostId = $row->unloading_cost_id;
    $freightCostId = $row->freight_cost_id;
	$handlingCostId = $row->handling_cost_id;
    $stockpileContractId = $row->stockpile_contract_id;
    $laborId = $row->labor_id;
    $supplierId = $row->supplier_id;
    $vehicleNo = $row->vehicle_no;
    $driver = $row->driver;
    $permitNo = $row->permit_no;
	$permitNo2 = $row->do_no;
    $block = $row->block;
    $sendWeight = $row->send_weight;
    $brutoWeight = $row->bruto_weight;
    $tarraWeight = $row->tarra_weight;
    $nettoWeight = $row->netto_weight;
    $notes = $row->notes;
    $notes2 = $row->notes;
    $transactionDate2 = $row->transaction_date2;
    $customerId = $row->customer_id;
   // $salesId = $row->sales_id;
    $shipmentId = $row->shipment_id;
    $quantity = $row->quantity;
    $isTaxable = $row->is_taxable;
    $ppn = $row->ppn;
    $pph = $row->pph;
	$slipNo = $row->slip_no;
	$slipUpload = $row->t_timbangan;
	$slipUpload2 = $row->slip;
	$poNo = $row->po_no;
    
}*/

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        echo "<option value='NONE'>NONE</option>";
        if($setvalue == 'NONE') {
            echo "<option value='NONE' selected>NONE</option>";
        } else {
            echo "<option value='NONE'>NONE</option>";
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
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('.combobox').combobox();
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
//        $('.select2combobox').selectize({
//            allowEmptyOption: true,
//            create: true,
//            sortField: 'text'
//        });
        
        if(document.getElementById('transactionType').value == 2) {
            $('#inTransaction').hide();
            $('#outTransaction').show();
        }
        
        
            <?php
        
		 if($transactionType == 2) {
            ?>
			$('#inTransaction').hide();
            $('#outTransaction').show();
            //setSales(1, $('select[id="customerId"]').val(), <?php //echo $salesId; ?>);
            //setShipment(1, <?php //echo $salesId; ?>, <?php //echo $shipmentId; ?>);
            //setShipmentDetail(<?php //echo $shipmentId; ?>);
            <?php
        }
        ?>
        
        
        $('#customerId').change(function() {
            resetSalesCon(' Buyer ');
            resetSalesOrder(' Buyer ');
           // resetSales(' Buyer ');
            resetShipment(' Sales ');
            resetShipmentDetail();
            resetFreightCostSales(' ');
            resetFreightCostSalesDetail();

            resetLaborCostSales(' ');
            resetLaborCostSalesDetail();
            resetHandlingCostSales(' ');
            resetHandlingCostSalesDetail();
            if(document.getElementById('customerId').value != '' && document.getElementById('customerId').value != 'INSERT') {
                //resetSales(' ');
                //setSales(0, $('select[id="customerId"]').val(),<?php //echo $stockpileId;?>, 0);
                resetSalesCon(' Buyer ');
                setSalesCon(0, $('select[id="customerId"]').val(),$('select[id="stockpileId"]').val(), 0);
            } 
        });
        
        $('#salesConId').change(function() {

            resetSalesOrder(' Buyer ');
            resetShipment(' Sales ');
            resetShipmentDetail();
            resetFreightCostSales(' ');
            resetFreightCostSalesDetail();

            resetLaborCostSales(' ');
            resetLaborCostSalesDetail();
            resetHandlingCostSales(' ');
            resetHandlingCostSalesDetail();
            
            if(document.getElementById('salesConId').value != '' && document.getElementById('salesConId').value != 'INSERT') {
                //resetShipment(' ');
                //setShipment(0, $('select[id="salesId"]').val(), 0);
                resetSalesOrder(' Buyer ');
                setSalesOrder(0, $('select[id="salesConId"]').val(), 0);
                
            } 
        });

        $('#soId').change(function() {
            resetShipment(' Sales ');
            resetShipmentDetail();
            
            if(document.getElementById('soId').value != '' && document.getElementById('soId').value != 'INSERT') {
                resetShipment(' ');
                setShipment(0, $('select[id="soId"]').val(), $('select[id="stockpileId"]').val(), 0);
                setFreightCostSales(0, $('select[id="salesConId"]').val());
                setLaborCostSales(0, $('select[id="salesConId"]').val());
                setHandlingCostSales(0, $('select[id="salesConId"]').val());
            } 
        });
        
        
        $('#salesId').change(function() {
            if(document.getElementById('salesId').value != '' && document.getElementById('salesId').value != 'INSERT') {
                setShipmentDetail($('select[id="salesId"]').val());
                
            } else {
                resetShipmentDetail();
            }

            
        });
        
        $('#freightCostSalesId').change(function () {
            resetFreightCostSalesDetail();
            if (document.getElementById('freightCostSalesId').value != '' && document.getElementById('freightCostSalesId').value != 'INSERT') {
                setFreightCostSalesDetail($('select[id="freightCostSalesId"]').val());
            } 
        });

        $('#laborCostSalesId').change(function () {
            resetLaborCostSalesDetail();
            if (document.getElementById('laborCostSalesId').value != '' && document.getElementById('laborCostSalesId').value != 'INSERT') {
                setLaborCostSalesDetail($('select[id="laborCostSalesId"]').val());
            } 
        });
        $('#handlingCostSalesId').change(function () {
            resetHandlingCostSalesDetail();
            if (document.getElementById('handlingCostSalesId').value != '' && document.getElementById('handlingCostSalesId').value != 'INSERT') {
                setHandlingCostSalesDetail($('select[id="handlingCostSalesId"]').val());
            } 
        });
        
        $("#transactionDataForm").validate({
            rules: {
                stockpileId: "required",
                transactionType: "required",
                //loadingDate: "required",
                //stockpileContractId: "required",
                //vehicleNo: "required",
                driverSales: "required",
                //unloadingDate: "required",
                //unloadingCostId: "required",
                doSales: "required",
                //freightCostId: "required",
				//handlingCostId: "required",
                //sendWeight: "required",
                //brutoWeight: "required",
                //tarraWeight: "required",
                salesConId: "required",
                soId: "required",
                salesId: "required",
                customerId: "required",
                transactionDate2: "required",
                sendWeight2: "required",
                blWeight: "required",
                vehicleNo2: "required",
                salesId: "required"
            },
            messages: {
                stockpileId: "Stockpile is a required field.",
                transactionType: "Type is a required field.",
                //loadingDate: "Loading Date is a required field.",
                //stockpileContractId: "PO No. is a required field.",
                //vehicleNo: "Vehicle No. is a required field.",
                driverSales: "Driver is a required field.",
                //unloadingDate: "Unloading Date is a required field.",
                //unloadingCostId: "Vehicle is a required field.",
                doSales: "Do No. is a required field.",
                //freightCostId: "Supplier Freight is a required field.",
				//handlingCostId: "Handling Cost is a required field.",
                //sendWeight: "Sent Weight is a required field.",
                //brutoWeight: "Bruto Weight is a required field.",
                //tarraWeight: "Tarra Weight is a required field.",
                salesId: "This is a required field.",
                customerId: "Buyer is a required field.",
                transactionDate2: "Transaction Date is a required field.",
                sendWeight2: "Stockpile Weight is a required field.",
                blWeight: "BL Weight is a required field.",
                vehicleNo2: "Vessel Name is a required field.",
                salesId: "Sales Agreement No. is a required field."
            },
            submitHandler: function(form) {
                $('#submitButton').attr("disabled", true);
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#transactionDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[3]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#pageContent').load('views/search-notim-local-sales.php', {}, iAmACallbackFunction);
                            } 
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });
        
        
        
    });
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });


    function resetHandlingCostSalesDetail() {
        $('#labelHandlingCostSales').hide();
        document.getElementById('labelHandlingCostSales').innerHTML = '';
    }

    function setHandlingCostSalesDetail(handlingCostSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getHandlingCostSalesDetail',
                handlingCostSalesId: handlingCostSalesId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelHandlingCostSales').show();
                    document.getElementById('labelHandlingCostSales').innerHTML = 'handling cost/KG is ' + data;
                }
            }
        });
    }
    

    function resetLaborCostSalesDetail() {
        $('#labelLaborCostSales').hide();
        document.getElementById('labelLaborCostSales').innerHTML = '';
    }

    function setLaborCostSalesDetail(laborCostSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getLaborCostSalesDetail',
                laborCostSalesId: laborCostSalesId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelLaborCostSales').show();
                    document.getElementById('labelLaborCostSales').innerHTML = 'labor cost/KG is ' + data;
                }
            }
        });
    }
    
    function resetFreightCostSalesDetail() {
        $('#labelFreightCostSales').hide();
        document.getElementById('labelFreightCostSales').innerHTML = '';
    }

    function setFreightCostSalesDetail(freightCostSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightCostSalesDetail',
                freightCostSalesId: freightCostSalesId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelFreightCostSales').show();
                    document.getElementById('labelFreightCostSales').innerHTML = 'Freight cost/KG is ' + data;
                }
            }
        });
    }

    function resetHandlingCostSales(text) {
        document.getElementById('handlingCostSalesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('handlingCostSalesId').options.add(x);

        $("#handlingCostSalesId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });

    
    }
	
	function setHandlingCostSales(type, soId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getHandlingCostSales',
				soId: soId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('handlingCostSalesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('handlingCostSalesId').options.add(x);

                        $("#handlingCostSalesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('handlingCostSalesId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('handlingCostSalesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('handlingCostSalesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#handlingCostSalesId').find('option').each(function (i, e) {
                            if ($(e).val() == handlingCostSalesId) {
                                $('#handlingCostSalesId').prop('selectedIndex', i);

                                $("#handlingCostSalesId").select2({
                                    width: "100%",
                                    placeholder: handlingCostSalesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetLaborCostSales(text) {
        document.getElementById('laborCostSalesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('laborCostSalesId').options.add(x);

        $("#laborCostSalesId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });

    
    }
	
	function setLaborCostSales(type, soId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getLaborCostSales',
				soId: soId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('laborCostSalesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborCostSalesId').options.add(x);

                        $("#laborCostSalesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('laborCostSalesId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborCostSalesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('laborCostSalesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#laborCostSalesId').find('option').each(function (i, e) {
                            if ($(e).val() == laborCostSalesId) {
                                $('#laborCostSalesId').prop('selectedIndex', i);

                                $("#laborCostSalesId").select2({
                                    width: "100%",
                                    placeholder: laborCostSalesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetFreightCostSales(text) {
        document.getElementById('freightCostSalesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightCostSalesId').options.add(x);

        $("#freightCostSalesId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });

    
    }
	
	function setFreightCostSales(type, soId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightCostSales',
				soId: soId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('freightCostSalesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightCostSalesId').options.add(x);

                        $("#freightCostSalesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('freightCostSalesId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightCostSalesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('freightCostSalesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#freightCostSalesId').find('option').each(function (i, e) {
                            if ($(e).val() == freightCostSalesId) {
                                $('#freightCostSalesId').prop('selectedIndex', i);

                                $("#freightCostSalesId").select2({
                                    width: "100%",
                                    placeholder: freightCostSalesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetShipment(text) {
        document.getElementById('salesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('salesId').options.add(x);
        
        $("#salesId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
     
    
    function resetCustomer() {
        document.getElementById('customerId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('customerId').options.add(x);
        
        $("#customerId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }

    function resetSalesCon() {
        document.getElementById('salesConId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('salesConId').options.add(x);
        
        $("#salesConId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }

    function resetSalesOrder() {
        document.getElementById('soId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('soId').options.add(x);
        
        $("#soId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }

    function setSalesOrder(type, salesConId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSalesOrderNotim',
                salesConId: salesConId
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
                        document.getElementById('soId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('soId').options.add(x);
                        
                        $("#soId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('soId').options.add(x);
                    }

        
                                        
                    if(type == 1) {
                        $('#soId').find('option').each(function(i,e){
                            if($(e).val() == soId){
                                $('#soId').prop('selectedIndex',i);
                                
                                $("#soId").select2({
                                    width: "100%",
                                    placeholder: soId
                                });
                            }
                        });
                    }
                }
            }
        });
    }


    <!-- Start Add by Eva -->
	function getAmountClaim(qtyAddShrink, priceAddShrink) {
        if (qtyAddShrink.value != '' && priceAddShrink.value != '') {
            document.getElementById('newAmountClaim').value = qtyAddShrink.value.replace(new RegExp(",", "g"), "") * priceAddShrink.value.replace(new RegExp(",", "g"), "");
        } else {
            //document.getElementById('newAamountClaim').value = 0;
        }
    }
	<!-- End Add by Eva -->


    function setSalesCon(type, customerId,stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSalesCon',
                customerId: customerId,
                stockpileId: stockpileId
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
                        document.getElementById('salesConId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('salesConId').options.add(x);
                        
                        $("#salesConId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesConId').options.add(x);
                    }

                   
                                        
                    if(type == 1) {
                        $('#salesConId').find('option').each(function(i,e){
                            if($(e).val() == salesConId){
                                $('#salesConId').prop('selectedIndex',i);
                                
                                $("#salesConId").select2({
                                    width: "100%",
                                    placeholder: salesConId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setCustomer(type, customerId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getCustomer'
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
                        document.getElementById('customerId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('customerId').options.add(x);
                        
                        $("#customerId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('customerId').options.add(x);
                    }

                    <?php
                    if($allowCustomer) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('customerId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#customerId').find('option').each(function(i,e){
                            if($(e).val() == customerId){
                                $('#customerId').prop('selectedIndex',i);
                                
                                $("#customerId").select2({
                                    width: "100%",
                                    placeholder: customerId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    
    function setShipment(type, soId, stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSalesCode2',
                soId: soId,
                stockpileId: stockpileId
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
                        document.getElementById('salesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('salesId').options.add(x);
                        
                        $("#salesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesId').options.add(x);
                    }
                }
            }
        });
    }
    
    function resetShipmentDetail() {
//        document.getElementById('customerName').value = '';
        document.getElementById('quantityAvailable').value = '';
        document.getElementById('sales_rule').value = '';
        document.getElementById('invRule').value = '';
        document.getElementById('rules').value = '';
      
        //document.getElementById('salesId').value = '';
    }
    
    function setShipmentDetail(salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentDetailLS',
                sales_code: salesId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');

//                    document.getElementById('customerName').value = returnVal[0];
                    document.getElementById('quantityAvailable').value = returnVal[1];
                    document.getElementById('sales_rule').value = returnVal[3];
                    document.getElementById('invRule').value = returnVal[4];
                    document.getElementById('rules').innerHTML = returnVal[5];
                    
                    //document.getElementById('salesId').value = returnVal[2];
                    
                }
            }
        });
    }

   
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/search-notim-local-sales.php', {}, iAmACallbackFunction);
    }
</script>


<!--<h4>Transaction Form</h4>-->

<form method="post" id="transactionDataForm">
    <input type="hidden" name="action" id="action" value="transaction_data" />
    <input type="hidden" name="_method" value="INSERT_PREVIEW_2">
    <input type="hidden" name="rsb" id="rsb" value="0" />
    <input type="hidden" name="ggl" id="ggl" value="0" />
    <input type="hidden" name="rg" id="rg" value="0" />
    <input type="hidden" name="un" id="un" value="1" />
    <input type="hidden" name="rsb1" id="rsb1" value="0" />
    <input type="hidden" name="qty_rsb" id="qty_rsb" value="0" />
    <input type="hidden" name="ggl1" id="ggl1" value="0" />
    <input type="hidden" name="qty_ggl" id="qty_ggl" value="0" />
    <input type="hidden" name="rsb_ggl" id="rsb_ggl" value="0" />
    <input type="hidden" name="qty_RG" id="qty_RG" value="0" />
    <input type="hidden" name="uncertified" id="uncertified" value="1" />
    <input type="hidden" name="qtyUN_error1" id="qtyUN_error1" value="0" />
    <div class="row-fluid" style="margin-bottom: 7px;">
		<div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
         
			<input type="text" readonly class="span12" tabindex="15" id="transactionType2" name="transactionType2" value="OUT">
			<input type="hidden" readonly class="span12" tabindex="15" id="transactionType" name="transactionType" value="2">
        </div>
        <div class="span2 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 1, "select2combobox75");
            ?>
        </div>
        
    </div>
    
   
    <div id="outTransaction" style="display: none;">
        
        <div class="row-fluid">   
            <div class="span2 lightblue">
                <label>Transaction Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="transactionDate2" name="transactionDate2" value="" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
            <div class="span2 lightblue">
                <label>Buyer</label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("SELECT cust.customer_id, cust.customer_name
                            FROM customer cust WHERE cust.sales_category IN (2,3) ORDER BY cust.customer_name ASC", "", "", "customerId", "customer_id", "customer_name", 
                            "", 11, "select2combobox100", 1, "");
                ?>
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Vehicle No <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="3" id="vehicleNo2" name="vehicleNo2" >
            </div>
            <div class="span2 lightblue">
                <label>Sales Contract No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "salesConId", "sales_con_id", "sales_con_no", 
                            "", 12, "select2combobox100", 1, "");
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Inventory/SP Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="4" id="sendWeight2" name="sendWeight2">
            </div>
            <div class="span2 lightblue">
                <label>Sales Order <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "soId", "so_id", "so_no", 
                            "", 13, "select2combobox100", 1, "");
                ?>
            </div>
        </div>
        <div class="row-fluid">
           <!-- <div class="span2 lightblue">
                <label>B/L Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="5" id="blWeight" name="blWeight">
            </div>-->
            <div class="span2 lightblue">
                <label>PKS Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="4" id="pks_weight" name="pks_weight">
            </div>
            <div class="span2 lightblue">
                <label>Sales Code <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "salesId", "sales_id", "salesCode", 
                            "", 13, "select2combobox100", 1, "");
                ?>
    
            </div>
        </div>
        <div class="row-fluid">
        <div class="span2 lightblue">
                <label>Buyer Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="5" id="buyer_weight" name="buyer_weight" >
            </div>
            <div class="span2 lightblue">
                <label>Quantity Agreed (KG)</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="14" id="quantityAvailable" name="quantityAvailable">
            </div>
        </div>
        <div class="row-fluid">
        <div class="span2 lightblue">
                <label>Sales Freight <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "freightCostSalesId", "freight_cost_id", "freight_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCostSales" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>DO No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="doSales" name="doSales"
                       value="<?php echo $permitNo; ?>">
            </div>
        </div>
        <div class="row-fluid">
        <div class="span2 lightblue">
                <label>Sales Labor <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "laborCostSalesId", "labor_cost_id", "labor_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelLaborCostSales" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Driver <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="driverSales" name="driverSales"
                       value="<?php echo $driver; ?>">
            </div>
        </div>
        
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Sales Handling <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "handlingCostSalesId", "handling_cost_id", "handling_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelHandlingCostSales" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Terms & Conditions <span style="color: red;">*</span></label>
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;"><b>: <span id="rules"></span></b></label>
                <input type="hidden" class="span12" tabindex="16" id="sales_rule" name="sales_rule">
                <input type="hidden" class="span12" tabindex="16" id="invRule" name="invRule">
            </div>
        </div>

        <!-- Start Add by Eva -->
		<hr>
		<div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span5 lightblue">
                <label><span style="color: blue;">** </span>Additional Shrink (Additional Value from Standard Shrinkage)<span style="color: red;"></span></label>
            </div>
        </div>
		<div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Quantity <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="30" id="qtyAddShrink" name="qtyAddShrink"  onblur="getAmountClaim(this, document.getElementById('priceAddShrink'));">
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Price <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="31" id="priceAddShrink" name="priceAddShrink" onblur="getAmountClaim(document.getElementById('qtyAddShrink'), this);">
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Amount Claim (Additional Shrink) <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="32" id="newAmountClaim" name="newAmountClaim" >
            </div>
        </div>
		<br>
		<!-- End Add by Eva -->
        <br>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes2" name="notes2"></textarea>
            </div>
        </div>
    </div>
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" >
    <form id="insertForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="insertModalLabel">Insert New</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="insertModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowViewJurnal = false;
$allowUnlock = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 25) {
            $allowViewJurnal = true;
        }elseif($row->module_id == 19) {
            $allowUnlock = true;
        }
    }
}

$poNo = '';
$contractNo = '';
$vendorName = '';

if(isset($_POST['poNo']) && $_POST['poNo'] != '') {
    $poNo = $_POST['poNo'];
}
if(isset($_POST['contractNo']) && $_POST['contractNo'] != '') {
    $contractNo = $_POST['contractNo'];
}

if(isset($_POST['vendorName']) && $_POST['vendorName'] != '') {
    $vendorName = $_POST['vendorName'];
}

$sql = "CALL SearchPoPKS ('{$poNo}','{$contractNo}','{$vendorName}')";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


$allowImport = true;

?>

<script type="text/javascript">
    $(document).ready(function(){	//executed after the page has loaded
        
        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table      : 'table table-bordered',
            header     : 'bootstrap-header', // give the header a gradient background
            footerRow  : '',
            footerCells: '',
            icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone   : 'bootstrap-icon-unsorted',
            sortAsc    : 'icon-chevron-up',
            sortDesc   : 'icon-chevron-down',
            active     : '', // applied when column is sorted
            hover      : '', // use custom css here - bootstrap class may not override it
            filterRow  : '', // filter row class
            even       : '', // odd row zebra striping
            odd        : ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme : "bootstrap",

            widthFixed: true,

            headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets : [ 'zebra', 'filter', 'uitheme' ],
                    
            headers: { 0: { sorter: false, filter: false } },

            widgetOptions : {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra : ["even", "odd"],
                        
                filter_functions : {
                    4: false
                },
                // reset filters button
//                filter_reset : ".reset"

                // set the uitheme widget to use the bootstrap theme class names
                // this is no longer required, if theme is set
                // ,uitheme : "bootstrap"

            }
        })
        .tablesorterPager({

            // target the pager markup - see the HTML block below
            container: $(".pager"),

            // target the pager page select dropdown - choose a page
            cssGoto  : ".pagenum",

            // remove rows from the table to speed up the sort of large tables.
            // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
            removeRows: false,
            // output string - default is '{page}/{totalPages}';
            // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

        });
        
        $('#importContract').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/contract-import.php');
        });
        
        $('#addNew').click(function(e){
            
            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
        
        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/contract2.php', {contractId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'view') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/search-contract.php', {contractId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'jurnal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/contract-account.php', {contractId: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }else if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_contract',
                                    contractId: menu[2]
                            },
                            success: function(data){
                                var returnVal = data.split('|');
                                if(parseInt(returnVal[3])!=0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                    if(returnVal[1] == 'OK') {
                                        $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<a href="#addNew|contract" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Contract</a>

 <?php
if($allowImport) {
?>
<!-- <a href="#" id="importContract"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Import Data Contract</a> -->
<?php
}
$date_now = date('Y-m');
?>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <?php if($allowViewJurnal) {?>
			<th>Journal</th>
			<?php } ?>
            <th>PO No.</th>
			<th>Stockpile</th>
            <th>Vendor</th>
            <th>Contract No.</th>
			<th>Notes</th>
            <th>Return Shipment</th>
            <th>Price/KG</th>
            <th>Quantity</th>
            
            <th>Payment</th>
			<th>Contract Date</th>
			<th>Entry By</th>
			<th>Entry Date</th>
			
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
             $lock_date = $rowData->lock_date;
				//echo $lock_date;
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
                	<a href="#" id="view|contract|<?php echo $rowData->contract_id; ?>" role="button" title="View"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php if($date_now == $lock_date && $rowData->payment_status == 0){?>	
					<a href="#" id="edit|contract2|<?php echo $rowData->contract_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <a href="#" id="delete|contract|<?php echo $rowData->contract_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
				   <?php }elseif($allowUnlock){?>
					 <a href="#" id="edit|contract2|<?php echo $rowData->contract_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <!--<a href="#" id="delete|contract|<?php //echo $rowData->contract_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>-->
				   <?php } ?>
				   
					
                    
                </div>
            </td>
            <?php if($allowViewJurnal) {?>
			<td><a href="#" id="jurnal|contract|<?php echo $rowData->contract_id; ?>" role="button"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /> View Journal</a></td>
			<?php } ?>
            <td><?php echo $rowData->po_no; ?></td>
			<td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->vendor_full; ?></td>
            <td><?php echo $rowData->contract_no; ?></td>
			<td><?php echo $rowData->notes; ?></td>
            <td><?php echo $rowData->returnShipment; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->price, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->quantity2, 2, ".", ","); ?></div></td>
            <?php 
			/*if($rowData->contract_status == 1){
			echo "<td style='font_weight: bold; color: green;'>"; 
			echo "LOCKED";
			echo "</td>";
			}else{
			echo "<td style='font_weight: bold; color: red;'>"; 
			echo "UNLOCKED";
			echo "</td>";
			}*/
			?>
            <?php 
			if($rowData->payment_status == 1 || ($rowData->paymentCurah != '' && $rowData->paymentCurah != 'NULL')){
			echo "<td style='font_weight: bold; color: green;'>"; 
			echo "PAID";
			echo "</td>";
			}else{
			echo "<td style='font_weight: bold; color: red;'>"; 
			echo "UNPAID";
			echo "</td>";
			}
			?>
			<td><?php echo $rowData->entry_date; ?></td>
			<td><?php echo $rowData->user_name; ?></td>
			<td><?php echo $rowData->entry_date2; ?></td>
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

<div class="pager">
    Page: <select class="pagenum input-mini"></select>	
    <i class="first icon-step-backward" alt="First" title="First page"></i>
    <i class="prev icon-arrow-left" alt="Prev" title="Previous page"></i>
    <button type="button" class="btn first"><i class="icon-step-backward"></i></button>
    <button type="button" class="btn prev"><i class="icon-arrow-left"></i></button>
    <span class="pagedisplay"></span>  
    <i class="next icon-arrow-right" alt="Next" title="Next page"></i>
    <i class="last icon-step-forward" alt="Last" title="Last page"></i>
    <button type="button" class="btn next"><i class="icon-arrow-right"></i></button>
    <button type="button" class="btn last"><i class="icon-step-forward"></i></button>
    <select class="pagesize input-mini">
            <option selected="selected" value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
    </select>
</div>
<div id="addJurnalModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="jurnalModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="jurnalForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">×</button>
                <h3 id="addJurnalModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalContractId" id="modalContractId" value="<?php echo $rowData->contract_id; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addJurnalModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>
<div id="importModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="importModalLabel">Import Wizard <label id="approveDesc" /></h3>
    </div>
    <div class="alert fade in alert-error" id="modalErrorMsg4" style="display:none;">
        Error Message
    </div>
    <div class="modal-body" id="importModalForm" style="max-height: 450px;">
    </div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>-->
    </div>

</div>
<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT a.pprice_id, d.`stockpile_name`, a.`po_adj_no`, a.`contract_adj_no`, b.`contract_no`, a.`harga_awal`, a.`harga_akhir`, a.`adjustment_price`,
c.`quantity`, a.notes, a.`input_date`, e.`user_name` AS inputby, a.`edit_date`, f.`user_name` AS editby,
CASE WHEN a.status = 0 THEN 'Pengajuan' ELSE 'Selesai' END AS status_proses, a.upload_doc, g.vendor_name, a.status
FROM contract_adjustment_pprice a
LEFT JOIN contract b ON a.`contract_id` = b.`contract_id`
LEFT JOIN stockpile_contract c ON c.`contract_id` = a.`contract_id`
LEFT JOIN stockpile d ON d.`stockpile_id` = c.`stockpile_id`
LEFT JOIN `user` e ON e.`user_id` = a.`input_by`
LEFT JOIN `user` f ON f.`user_id` = a.`edit_by`
LEFT JOIN vendor g ON g.vendor_id = b.vendor_id
WHERE c.`quantity` > 0";
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
                
                $('#dataContent').load('forms/adjustment-price.php', {pprice_id: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'view') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/adjustment-price-view.php', {pprice_id: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            }
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<!--<a href="#addNew|adjustment-price" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Adjustment</a>-->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Stockpile</th>
            <th>Vendor</th>
            <th>No. PO Adj</th>
            <th>No. Kontrak Adj</th>
            <th>No Kontrak</th>
            <th>Kuantiti</th>
			<th>Harga Awal</th>
            <th>Harga Akhir</th>
            <th>Harga Adj</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Input By</th>
            <th>Input Date</th>
			<th>Edit By</th>
			<th>Edit Date</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                
        ?>
        <tr>
            <td>
                <div style="text-align: center;">

                    <?php if($rowData->status == 0){?>	
                   <a href="#" id="edit|adjustment|<?php echo $rowData->pprice_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
				   <?php }else{ ?>
                    <a href="#" id="view|adjustment|<?php echo $rowData->pprice_id; ?>" role="button" title="View"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    
                   <?php }?>
					
                    
                </div>
            </td>
            
            <td><?php echo $rowData->stockpile_name; ?></td>
			<td><?php echo $rowData->vendor_name; ?></td>
            <td><?php echo $rowData->po_adj_no; ?></td>
            <td><?php echo $rowData->contract_adj_no; ?></td>
            <td><?php echo $rowData->contract_no; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->quantity, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->harga_awal, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->harga_akhir, 2, ".", ","); ?></div></td>
           	<td><div style="text-align: right;"><?php echo number_format($rowData->adjustment_price, 2, ".", ","); ?></div></td>
            <td><?php echo $rowData->notes; ?></td>
            <td><?php echo $rowData->status_proses; ?></td>
			<td><?php echo $rowData->inputby; ?></td>
            <td><?php echo $rowData->input_date; ?></td>
            <td><?php echo $rowData->editby; ?></td>
            <td><?php echo $rowData->edit_date; ?></td>
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
<div id="addAdjustmentModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="adjustmentModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="adjustmentForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAdjustmentModal">×</button>
                <h3 id="addAdjustmentModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalContractId" id="modalContractId" value="<?php echo $rowData->contract_id; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addAdjustmentModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAdjustmentModal">Close</button>
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
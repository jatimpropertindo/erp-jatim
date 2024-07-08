<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connectiongen
require_once PATH_INCLUDE . DS . 'db_init.php';

$allowDelete = false;
$allowApprovePG = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 54) {
            $allowApprovePG = true;
        }
//         elseif ($row->module_id == 18) {
//            $allowDelete = true;
//        }
    }
}

/*    $sql = "SELECT a.*,b.general_vendor_name, e.user_name, g.stockpile_name, f.user_name AS reject_by,
CONCAT(c.stockpile_name, ' To ', d.stockpile_name) AS trip, 
CONCAT(a.date_from, ' To ', a.date_to) AS date_trip,
CASE WHEN a.sa_method = 2 THEN 'Advance' ELSE 'Settlement' END AS metode,
(SELECT invoice_id FROM invoice WHERE sa_id = a.sa_id AND invoice_status = 0) AS invoice_id
FROM perdin_adv_settle a
LEFT JOIN general_vendor b ON a.id_user = b.general_vendor_id
LEFT JOIN stockpile c ON c.stockpile_id = a.origin
LEFT JOIN stockpile d ON d.stockpile_id = a.destination
LEFT JOIN user e ON e.user_id = a.entry_by
LEFT JOIN stockpile g ON g.stockpile_id = a.stockpile_id
LEFT JOIN user f ON f.user_id = a.sync_by
WHERE (CASE WHEN a.sa_method = 1 THEN a.approval_status ELSE 1 END) = 1 AND a.upload_status = 1 AND a.payment_from = 0
ORDER BY a.sa_id DESC"; */

// Updated By Vembri : Added h.user_name AS hrApproval - LEFT JOIN USER h ON h.user_id = a.approval_by
    $sql = "SELECT a.*,b.general_vendor_name, e.user_name, g.stockpile_name, f.user_name AS reject_by,
CONCAT(c.stockpile_name, ' To ', d.stockpile_name) AS trip, 
CONCAT(a.date_from, ' To ', a.date_to) AS date_trip,
CASE WHEN a.sa_method = 2 THEN 'Advance' ELSE 'Settlement' END AS metode,
(SELECT invoice_id FROM invoice WHERE sa_id = a.sa_id AND invoice_status = 1 LIMIT 1) AS invoice_id,
h.user_name AS hrApproval
FROM perdin_adv_settle a
LEFT JOIN general_vendor b ON a.id_user = b.general_vendor_id
LEFT JOIN stockpile c ON c.stockpile_id = a.origin
LEFT JOIN stockpile d ON d.stockpile_id = a.destination
LEFT JOIN user e ON e.user_id = a.entry_by
LEFT JOIN stockpile g ON g.stockpile_id = a.stockpile_id
LEFT JOIN user f ON f.user_id = a.sync_by
LEFT JOIN USER h ON h.user_id = a.approval_by
WHERE (CASE WHEN a.sa_method = 1 THEN a.approval_status ELSE 1 END) = 1 AND a.upload_status = 1 AND a.payment_from = 0
ORDER BY a.sa_id DESC";

$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowImport = true;

?>

<script type="text/javascript">
    $(document).ready(function () {	//executed after the page has loaded

        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table: 'table table-bordered',
            header: 'bootstrap-header', // give the header a gradient background
            footerRow: '',
            footerCells: '',
            icons: '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone: 'bootstrap-icon-unsorted',
            sortAsc: 'icon-chevron-up',
            sortDesc: 'icon-chevron-down',
            active: '', // applied when column is sorted
            hover: '', // use custom css here - bootstrap class may not override it
            filterRow: '', // filter row class
            even: '', // odd row zebra striping
            odd: ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets: ['zebra', 'filter', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra: ["even", "odd"],

                filter_functions: {
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
                cssGoto: ".pagenum",

                // remove rows from the table to speed up the sort of large tables.
                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                removeRows: false,
                // output string - default is '{page}/{totalPages}';
                // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

            });

        $('#importContract').click(function (e) {
            e.preventDefault();

            $('#importModal').modal('show');
            $('#importModalForm').load('forms/contract-import.php');
        });

        $('#addNew').click(function (e) {

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
        $('#exportExcel').click(function (e) {
            $("#pengajuanGeneralForm").submit(); // Submit the for
        });
        $('#contentTable a').click(function (e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'approve') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/approve-pengajuan-perdin.php', {pgId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'print') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/print-invoice.php', {invoiceId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
                
            }else if (menu[0] == 'download') {
                e.preventDefault();
                //window.location.href = menu[2];
				window.open(menu[2],'_blank');
            }else if (menu[0] == 'printHR') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#printApproval').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#printApprovalForm').load('forms/print-perdin-settle-approval.php', {sa_id: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
            }else if (menu[0] == 'delete') {
                alertify.set({
                    labels: {
                        ok: "Yes",
                        cancel: "No"
                    }
                });
                alertify.confirm("Are you sure want to delete this record?", function (e) {
                    if (e) {
                        $.ajax({
                            url: './irvan.php',
                            method: 'POST',
                            data: {
                                action: 'pengajuan_general_data',
                                _method: 'DELETE',
                                pgId: menu[2]
                            },
                            success: function (data) {
                                var returnVal = data.split('|');
                                if (parseInt(returnVal[3]) != 0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({
                                        labels: {
                                            ok: "OK"
                                        }
                                    });
                                    alertify.alert(returnVal[2]);
                                    if (returnVal[1] == 'OK') {
                                        $('#dataContent').load('contents/pengajuan-general.php', {}, iAmACallbackFunction2);
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

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>Type</th>
        <th>Advance/Settle/Reimburse No.</th>
        <th>Date</th>
        <th>On Behalf</th>
        <th>Stockpile</th>
        <th>Amount</th>
        <th>Remarks</th>
        <th>Status</th>
        <th>Input By</th>
        <th>Input Date</th>
		<!-- Added by Vembri -->
        <th>Approved By</th>
        <th>Approval Date</th>
        <!-- End of Addition -->
		<th>Reject Remarks</th>
        <th>Reject Date</th>
		<th>Reject By</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($resultData !== false && $resultData->num_rows > 0) {
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td>
                    <div style="text-align: center;">

                        <?php if ($rowData->invoice_status == 0 && $rowData->print_status == 1) { ?>
                            <a href="#" id="approve|pg|<?php echo $rowData->sa_id; ?>"
                               role="button"
                               title="Approve">
                                <img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                     style="margin-bottom: 5px;"/>
                            </a>
                        <?php }else{ ?>
						<a href="#" id="print|pg|<?php echo $rowData->invoice_id; ?>" role="button" title="Print"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                        <a href="#" id="download|pg|<?php echo $rowData->docs; ?>" role="button" title="Doc"><img src="assets/ico/doc-user.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            <?php if($rowData->sa_method == 1){?>
                        <a href="#" id="printHR|pg|<?php echo $rowData->sa_id; ?>" role="button" title="Approval HR"><img src="assets/ico/doc-approve.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
						<?php }
                            } ?>
                    </div>
                </td>
                
                <td><?php echo $rowData->metode; ?></td>
                <td><?php echo $rowData->sa_no; ?></td>
                <td><?php echo $rowData->tanggal; ?></td>
                <td><?php echo $rowData->general_vendor_name; ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo number_format($rowData->total_amount, 2, ".", ","); ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                
                
                <?php
                if ($rowData->invoice_status == 1) {
                    echo "<td style='font_weight: bold; color: #0e90d2;'>";
                    echo "INVOICED";
                    echo "</td>";
                }  elseif ($rowData->invoice_status == 2) {
                    echo "<td style='font_weight: bold; color: green;'>";
                    echo "PAID";
                    echo "</td>";
                } elseif ($rowData->invoice_status == 3) {
                    echo "<td style='font_weight: bold; color: red;'>";
                    echo "REJECTED";
                    echo "</td>";
                } elseif ($rowData->invoice_status == 4) {
                    echo "<td style='font_weight: bold; color: red;'>";
                    echo "CANCEL";
                    echo "</td>";
                } else {
                    echo "<td style='font_weight: bold; color: blue;'>";
                    echo "ON PROCESS";
                    echo "</td>";
                }
                ?>
                <td><?php echo $rowData->user_name; ?></td>
                <td><?php echo $rowData->entry_date; ?></td>
				<!-- Added by Vembri -->
                <td><?php echo $rowData->hrApproval; ?></td>
                <td><?php echo $rowData->approval_date; ?></td>
                <!-- End of Addition -->
				<td><?php echo $rowData->reject_remarks; ?></td>
                <td><?php echo $rowData->sync_date; ?></td>
				<td><?php echo $rowData->reject_by; ?></td>
            </tr>
            <?php

        }
    } else {
        ?>
        <tr>
            <td colspan="15">
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

<div id="printApproval" class="modal hide fade modal-lg" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">     
    <form id="approvalForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeApprovalModal">×</button>
            <h3 id="printApprovalLabel">Approved Documents</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
            Error Message
        </div>
        <input type="hidden" name="modalTransactionId" id="modalTransactionId" value="<?php echo $rowData->payment_id; ?>" />
        <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
        <div class="modal-body" id="printApprovalForm">
            
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeApprovalModal">Close</button>
            <!--<button class="btn btn-primary">Submit</button>-->
        </div>
    </form>
</div>

<style>
    .modal {
        position: fixed;
        top: 10%;
        left: 50%;
        z-index: 1050;
        width: 720px;
        margin-left: -360px;
        margin-top: -50px;
        background-color: #ffffff;
        border: 1px solid #999;
        border: 1px solid rgba(0, 0, 0, 0.3);
        *border: 1px solid #999;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        outline: none;
        -webkit-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
        -moz-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
        box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
    }
</style>
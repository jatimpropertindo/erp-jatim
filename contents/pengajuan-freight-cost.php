<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';
$whereProperty = '';

$statusId = $myDatabase->real_escape_string($_POST['statusId']);
$sql = "SELECT pfc.*, CONCAT(f.freight_code, '-', v.vendor_code, ' - ', f.freight_supplier, ' - ', CONCAT(v.vendor_name, ' (', v.vendor_code, ')')) AS freight_full, 
CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full, cur.currency_code, 
CASE WHEN pfc.active_from IS NULL THEN DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s')
ELSE DATE_FORMAT(pfc.active_from, '%d %b %Y') END AS active_from,
CASE WHEN pfc.status = 1 THEN DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s')
WHEN pfc.status = 2 THEN DATE_FORMAT(pfc.approved_date, '%d %b %Y %H:%i:%s')
 ELSE DATE_FORMAT(pfc.cancel_date, '%d %b %Y %H:%i:%s')  END  AS action_date,
 CASE WHEN pfc.status = 1 THEN DATEDIFF(CURRENT_DATE,pfc.entry_date)
 WHEN pfc.status = 2 THEN DATEDIFF(pfc.approved_date,pfc.entry_date)
 ELSE  DATEDIFF(pfc.cancel_date,pfc.entry_date) END AS aging, DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s') as entry_date
FROM pengajuan_freight_cost pfc
INNER JOIN freight f
ON f.freight_id = pfc.freight_id
INNER JOIN vendor v
ON v.vendor_id = pfc.vendor_id
INNER JOIN stockpile s
ON s.stockpile_id = pfc.stockpile_id
INNER JOIN currency cur
ON cur.currency_id = pfc.currency_id
WHERE pfc.status = {$statusId}
ORDER BY pfc.entry_date DESC LIMIT 1000 ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
    $(document).ready(function() { //executed after the page has loaded

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
            odd: '' // even row zebra striping
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

                //headers: { 0: { sorter: false, filter: false } },

                widgetOptions: {
                    // using the default zebra striping class name, so it actually isn't included in the theme variable above
                    // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                    zebra: ["even", "odd"],

                    filter_functions: {
                        4: true
                    },
                    // reset filters button

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

        $('#addNew').click(function(e) {
            e.preventDefault();
            loadContent($('#addNew').attr('href'));
        });

        $('#importContract').click(function(e) {
            e.preventDefault();
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/pkhoa-import.php');
        });

        $('#contentTable a').click(function(e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
            var purchasing_id = menu[2];
            if (menu[0] == 'edit') {
                e.preventDefault();

                $('#importModal').modal('show');
                $('#importModalForm').load('forms/pengajuan-freight-cost-data.php', {
                    pFreightCostId: menu[2],
					pStatus: menu[3],
                }, iAmACallbackFunction2);
            } else if (menu[0] == 'reject') {
                e.preventDefault();

                $('#importModal').modal('show');
                $('#importModalForm').load('forms/pengajuan-freight-cost-data.php', {
                    pFreightCostId: menu[2],
                    pStatus: menu[3],
                }, iAmACallbackFunction2);
            } else if (menu[0] == 'delete') {
                alertify.set({
                    labels: {
                        ok: "Yes",
                        cancel: "No"
                    }
                });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './irvan.php',
                            method: 'POST',
                            data: {
                                action: 'delete_pengajuan_freight_cost',
                                actionType: 'DELETE',
                                pFreightCostId: menu[2],
                            },
                            success: function(data) {
                                var returnVal = data.split('|');
                                if (parseInt(returnVal[3]) != 0) //if no errors
                                {
                                    //alert(msg);
                                    alertify.set({
                                        labels: {
                                            ok: "OK"
                                        }
                                    });
                                    alertify.alert(returnVal[1] + ' | ' + returnVal[2]);
                                    if (returnVal[1] == 'OK') {
                                        $('#contentTable').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction2);
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
        $("#contentTable").fadeIn("slow");
    }

    function OpenInNewTab() {
        var url = document.getElementById("url");
        var win = window.open(url, '_blank');
        win.focus();
    }

    function back() {
        $.blockUI({
            message: '<h4>Please wait...</h4>'
        });
        $('#pageContent').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction);
    }
</script>

<div style="margin-bottom: 5px;">
    <button class="btn" type="button" onclick="back()">Back</button>
	<br>
	<form method="post" id="downloadxls" action="reports/pengajuan-freight-cost-approve-xls.php">
        <input type="hidden" id="statusId" name="statusId" value="<?php echo $statusId; ?>"/>
        <button class="btn btn-success">Download XLS</button>
    </form>
</div>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th>Action</th>
        <th>Freight</th>
        <th>Currency</th>
        <th>Price/KG</th>
        <th>Active From</th>
        <th>Status</th>
		<th>Entry Date</th>
        <th>Action Date</th>
        <th>Agings</th>
        <th>Remarks</th>
        <th>Notes For Approval</th>
    </tr>
    </thead>
    <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                if ($rowData->status == 1) {
                    $status = '<span class="badge badge-info">New</span>';
                } elseif ($rowData->status == 2) {
                    $status = '<span class="badge badge-success">Approved</span>';
                } elseif ($rowData->status == 3) {
                    $status = '<span class="badge badge-danger">Canceled</span>';
                }
                ?>

                <tr>
                    <td>
                        <?php if ($rowData->status == 1) { ?>
                            <div style="text-align: center">
                                <a href="#" id="edit|stockpile-freight|<?php echo $rowData->p_freight_cost_id; ?>|<?php echo $rowData->status; ?>" role="button" title="Approve">
                                    <img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </div>
                            <div style="text-align: center">
                                <a href="#" id="delete|stockpile-freight|<?php echo $rowData->p_freight_cost_id; ?>|<?php echo $rowData->stockpile_id; ?>" role="button" title="Delete">
                                    <img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </div>
                        <?php } elseif ($rowData->status == 2){?>
                            <div style="text-align: center">
                                <a href="#" id="reject|stockpile-freight|<?php echo $rowData->p_freight_cost_id; ?>|<?php echo $rowData->status; ?>" role="button" title="Reject">
                                    <img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </div>
                            <div style="text-align: center">
                                <a href="<?php echo $rowData->file1; ?>" target="_blank" role="button" id="url" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </div>
                        <?php }else { ?>
                            <div style="text-align: center">
                                <a href="<?php echo $rowData->file1; ?>" target="_blank" role="button" id="url" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </div>
                        <?php }  ?>
                </td>
                <td><?php echo $rowData->freight_full; ?></td>
                <td><?php echo $rowData->currency_code; ?></td>
                <td>
                    <div style="text-align: right"><?php echo number_format($rowData->price, 4, ",", "."); ?></div>
                </td>
                <td><?php echo $rowData->active_from; ?></td>
                <td><?php echo $status; ?></td>
				<td><?php echo $rowData->entry_date; ?></td>
                <td><?php echo $rowData->action_date; ?></td>
                <td><?php echo $rowData->aging; ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                <td><?php echo $rowData->notes_for_approval; ?></td>
            </tr>
            <?php
                }
            } else {
                ?>
            <tr>
                <td colspan="6">
                    No data to be shown.
                </td>
            </tr>
        <?php } ?>
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

<div id="importModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="importModalLabel">Approve Freight Cost <label id="approveDesc" /></h3>
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

<div id="editModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="importModalLabel">Edit Wizard <label id="approveDesc" /></h3>
    </div>
    <div class="alert fade in alert-error" id="modalErrorMsg4" style="display:none;">
        Error Message
    </div>
    <div class="modal-body" id="editModalForm" style="max-height: 450px;">
    </div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>-->
    </div>

</div>

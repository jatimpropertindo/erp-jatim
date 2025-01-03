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
    }
}

$noPengajuan = '';
$vendorName = '';

if(isset($_POST['noPengajuan']) && $_POST['noPengajuan'] != '') {
    $noPengajuan = $_POST['noPengajuan'];
}
if(isset($_POST['vendorName']) && $_POST['vendorName'] != '') {
    $vendorName = $_POST['vendorName'];
	
}

$sql = "CALL SearchRegisterInvoice ('{$noPengajuan}', '{$vendorName}')";
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

                $('#dataContent').load('forms/approve-pengajuan-general.php', {pgId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'delete') {
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

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }

</script>

    <form method="POST" action="p_invoice_general.php" id="InvoiceGeneralForm">
        <div class="row-fluid">
            <button class="btn btn-success">Download XLS</button>
        </div>

        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
            <thead>
            <tr>
                <th style="width: 100px;">
                    <div style="text-align: center">
                        <input type="checkbox" onClick="toggle(this)" />
                    </div>
                </th>
                <th style="width: 100px;">Action</th>
                <th>Payment Type</th>
                <th>Request Payment Date</th>
                <th>Pengajuan No.</th>
                <th>Invoice No.</th>
                <th>Invoice Date</th>
                <th>Original Invoice No.</th>
                <th>Vendor</th>
                <th>Request Date</th>
                <th>Stockpile</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th>Remarks Reject/Cancel</th>
                <th>Status</th>
                <th>Input By</th>
                <th>Input Date</th>
                <th>Reject/Cancel Date</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($resultData !== false && $resultData->num_rows > 0) {
                while ($rowData = $resultData->fetch_object()) {
                    ?>
                    <tr>
                        <td>
                            <div style="text-align: center">
                                <input type="checkbox" name="checks[]" value="<?php echo $rowData->pengajuan_general_id; ?>" />
                            </div>
                        </td>
                        <td>
                            <div style="text-align: center;">
                                <?php if ($rowData->invoice_status == 0 && $rowData->status_pengajuan != 4) { ?>
                                    <a href="#" id="approve|pg|<?php echo $rowData->pengajuan_general_id; ?>"
                                    role="button"
                                    title="Approve">
                                        <img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/>
                                    </a>
                                <?php } ?>
                            </div>
                        </td>
                        <?php
                        if ($rowData->payment_type == 1) {
                            echo "<td style='font_weight: bold; color: red;'>";
                            echo "URGENT";
                            echo "</td>";
                        } else {
                            echo "<td style='font_weight: bold; color: black;'>";
                            echo "NORMAL";
                            echo "</td>";
                        }
                        ?>
                        <td><?php echo $rowData->request_payment_date; ?></td>
                        <td><?php echo $rowData->pengajuan_no; ?></td>
                        <td><?php echo $rowData->invoice_no; ?></td>
                        <td><?php echo $rowData->invoice_date; ?></td>
                        <td><?php echo $rowData->invoice_no2; ?></td>
                        <td><?php echo $rowData->general_vendor_name; ?></td>
                        <td><?php echo $rowData->request_date; ?></td>
                        <td><?php echo $rowData->stockpile_name; ?></td>
                        <td><?php echo number_format($rowData->amount_total, 2, ".", ","); ?></td>
                        <td><?php echo $rowData->remarks; ?></td>
                        <td><?php echo $rowData->reject_remarks; ?></td>
                        <?php
                        if ($rowData->status_pengajuan == 1) {
                            echo "<td style='font_weight: bold; color: #0e90d2;'>";
                            echo "INVOICE";
                            echo "</td>";
                        } elseif ($rowData->status_pengajuan == 2) {
                            echo "<td style='font_weight: bold; color: yellowgreen;'>";
                            echo "PAYMENT";
                            echo "</td>";
                        } elseif ($rowData->status_pengajuan == 3) {
                            echo "<td style='font_weight: bold; color: green;'>";
                            echo "PAID";
                            echo "</td>";
                        } elseif ($rowData->status_pengajuan == 4) {
                            echo "<td style='font_weight: bold; color: red;'>";
                            echo "REJECTED";
                            echo "</td>";
                        } elseif ($rowData->status_pengajuan == 5) {
                            echo "<td style='font_weight: bold; color: red;'>";
                            echo "CANCEL";
                            echo "</td>";
                        } else {
                            echo "<td style='font_weight: bold; color: blue;'>";
                            echo "PENGAJUAN";
                            echo "</td>";
                        }
                        ?>
                        <td><?php echo $rowData->user_name; ?></td>
                        <td><?php echo $rowData->entry_date; ?></td>
                        <td><?php echo $rowData->reject_date; ?></td>
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
    </form>

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
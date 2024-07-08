<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$vendorId = '';
$general_vendor_name = '';

// If ID is in the parameter
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    
    $vendorId = $_POST['vendorId'];
    
    $sql = "SELECT * FROM general_vendor WHERE general_vendor_id = {$vendorId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $general_vendor_name = $row->general_vendor_name;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addGeneralVendorPPn').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addGeneralVendorPPnModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addGeneralVendorPPnModalForm').load('forms/general-vendor-ppn.php', {vendorId: $('input[id="generalVendorId"]').val()});
            });
            
            $("#addGeneralVendorPPnForm").validate({
                rules: {
                    vendorId: "required",
                    taxId: "required"
                },

                messages: {
                    vendorId: "General Vendor is a required field.",
                    taxId: "PPN is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addGeneralVendorPPnForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addGeneralVendorPPnModal').modal('hide');
                                    $('#general-vendor-ppn').load('tabs/general-vendor-ppn.php', {vendorId: $('input[id="generalVendorId"]').val()});
                                    
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                } else {
                                    //show error message
                                    document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
                                    $("#modalErrorMsg").show();
                                }
                            }
                        }
                    });
                }
            });
        
            $('#tabContent3b').load('contents/general-vendor-ppn.php', { vendorId: <?php echo $vendorId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3b').fadeIn();
        } 
    </script>
    
    <h4>Vendor Name: <?php echo $general_vendor_name; ?></h4>
    
    <a href="#addGeneralVendorPPnModal" id="addGeneralVendorPPn" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add PPN</a>
    
    <div id="tabContent3b">
        
    </div>
    
    <div id="addGeneralVendorPPnModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addGeneralVendorPPnModalLabel" aria-hidden="true">
        <form id="addGeneralVendorPPnForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                
                <h3 id="addGeneralVendorPPnModalLabel">Add PPN</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalVendorId" id="modalVendorId" value="<?php echo $vendorId; ?>" />
            <input type="hidden" name="action" id="action" value="general_vendor_ppn" />
            <div class="modal-body" id="addGeneralVendorPPnModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddGeneralVendorPPnModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>General Vendor is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';


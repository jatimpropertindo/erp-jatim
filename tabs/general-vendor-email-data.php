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

$generalVendorId = '';

// If ID is in the parameter
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    
    $generalVendorId = $_POST['vendorId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addGeneralVendorEmail').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addGeneralVendorEmailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addGeneralVendorEmailModalForm').load('forms/general-vendor-email-form.php', {generalVendorId: $('input[id="generalVendorId"]').val()});
            });
            
            $("#addGeneralVendorEmailForm").validate({
                rules: {
                    gvEmail: "required",
                    status: "required"
                },

                messages: {
                    gvEmail: "Email is required",
                    status: "Status is required"
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addGeneralVendorEmailForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
                        //    alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addGeneralVendorEmailModal').modal('hide');
                                    $('#general-vendor-email-data').load('tabs/general-vendor-email-data.php', {vendorId: $('input[id="generalVendorId"]').val()});
                                    
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
        
            $('#tabContent3a').load('contents/general-vendor-email-content.php', { generalVendorId: <?php echo $generalVendorId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3a').fadeIn();
        }
    </script>
    
    <a href="#addGeneralVendorEmailModal" id="addGeneralVendorEmail" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Email</a>
    
    <div id="tabContent3a">
        
    </div>
    
    <div id="addGeneralVendorEmailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addGeneralVendorEmailModalLabel" aria-hidden="true">
        <form id="addGeneralVendorEmailForm" method="post" style="margin: 0px;">
            <input type="hidden" name="generalVendorId" id="generalVendorId" value="<?php echo $generalVendorId; ?>" />
            <input type="hidden" name="action" id="action" value="gv_email_data" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddGeneralVendorEmailModalx">×</button>
                <h3 id="addGeneralVendorEmailModalLabel">Add Email</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="addGeneralVendorEmailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddGeneralVendorEmailModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>User is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';


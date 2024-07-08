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

$vendorHandlingId = '';

// If ID is in the parameter
if(isset($_POST['vendorHandlingId']) && $_POST['vendorHandlingId'] != '') {
    $vendorHandlingId = $_POST['vendorHandlingId'];
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        
        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addVhEmail').click(function(e){
                e.preventDefault();
                $("#modalErrorMsg").hide();
                $('#addVhEmailModal').modal('show');
                //alert($('#addNew').attr('href'));
                $('#addVhEmailModalForm').load('forms/vh-email-form.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
            });
            
            $("#addVhEmailForm").validate({
                rules: {
                    vhEmail: "required",
                    status: "required"
                },

                messages: {
                    vhEmail: "Email is required",
                    status: "Status is required"
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addVhEmailForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addVhEmailModal').modal('hide');
                                    $('#vh-email-data').load('tabs/vh-email-data.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
                                    
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
            $('#tabContent3').load('contents/vh-email-content.php', { vendorHandlingId: <?php echo $vendorHandlingId; ?> }, iAmACallbackFunction3);
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addVhEmailModal" id="addVhEmail" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Email</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addVhEmailModal" class="modal hide fade" tabindex="+1" role="dialog" aria-labelledby="addVhEmailModalLabel" aria-hidden="true">
        <form id="addVhEmailForm" method="post" style="margin: 0px;">
            <input type="hidden" name="action" id="action" value="vh_email_data" />
            <input type="hidden" name="vendorHandlingId" id="vendorHandlingId" value="<?php echo $vendorHandlingId; ?>" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddVhEmailModal">×</button>
                <h3 id="addVhEmailModalLabel">Add Email</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="addVhEmailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddVhEmailModal">Close</button>
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


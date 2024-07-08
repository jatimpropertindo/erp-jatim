<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$salesId = '';
$assign_po = '';

if(isset($_POST['salesId']) && $_POST['salesId'] != '') {
    $salesId = $_POST['salesId'];
    $assign_po = $_POST['assign_po'];
    $boolShow = true;
}


?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){	//executed after the page has loaded
        loadConfig();
        
        $('#myTab a').click(function (e) {
            e.preventDefault();
            //alert(this);
            $("#successMsg").hide();
            $("#errorMsg").hide();
            
            $(this).tab('show');
            
            loadConfig();
        });
        
        
        
    });
    
    function loadConfig() {
        var url = $('#myTab .active a').attr('href');
        //alert(url)
        var menu = url.split('#');
        
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        //alert(menu[1]);
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {salesId: $('input[id="generalSalesId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/sales-local.php', {}, iAmACallbackFunction);
        
        saveForm(4);
    }
    
    function saveForm(id) {
        if(id == 4) {
            $.ajax({
                url: 'session_processing.php',
                method: 'POST',
                data: $("#salesDataForm").serialize(),
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {

                    }
                }
            });
        }
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#salesCode-local-data2" data-toggle="tab">Local Sales Code Details</a></li>
    <?php if ($assign_po == 1){?>
    <li><a href="#salesCode-local-po" data-toggle="tab">Assign PO PKS</a></li>
    <?php } ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalSalesId" value="<?php echo $salesId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="salesCode-local-data2">
</div>    
<?php if ($assign_po == 1){?>
    <div class="tab-pane" id="salesCode-local-po">
        
    </div>
    <?php } ?>
</div>
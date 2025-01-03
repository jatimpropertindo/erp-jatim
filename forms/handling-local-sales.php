<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$handlingId = '';

if(isset($_POST['handlingId']) && $_POST['handlingId'] != '') {
    $handlingId = $_POST['handlingId'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {handlingId: $('input[id="generalhandlingId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/handling-local-sales.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#handling-local-sales-data" data-toggle="tab">handling Details</a></li>
	<?php
    if($boolShow) {
    ?>
    <li><a href="#handling-cost-local-sales-data" data-toggle="tab">handling Cost</a></li>
    <li><a href="#handling-local-sales-bank-data" data-toggle="tab">Bank</a></li>
    
    <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalhandlingId" value="<?php echo $handlingId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="handling-local-sales-data">
        
    </div>
	<?php
    if($boolShow) {
    ?>
    <div class="tab-pane" id="handling-cost-local-sales-data">
        
    </div>

    <div class="tab-pane" id="handling-local-sales-bank-data">
        
    </div>
    
    
    <?php
    }
    ?>
    
</div>
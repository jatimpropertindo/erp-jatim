<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Vehicle Data">

$vehicleId = '';
$vehicleName = '';
$vehicleSize = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['vehicleId']) && $_POST['vehicleId'] != '') {
    
    $vehicleId = $_POST['vehicleId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vehicle Data">
    
    $sql = "SELECT v.*
            FROM vehicle v
            WHERE v.vehicle_id = {$vehicleId}
            ORDER BY v.vehicle_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vehicleName = $rowData->vehicle_name;
        $vehicleSize = $rowData->vehicle_size;
    }
    
    // </editor-fold>
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#vehicleDataForm").validate({
            rules: {
                vehicleName: "required"
            },
            messages: {
                vehicleName: "Vehicle name is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#vehicleDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalVehicleId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/vehicle.php', { vehicleId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });
    });
</script>

<form method="post" id="vehicleDataForm">
    <input type="hidden" name="action" id="action" value="vehicle_data" />
    <input type="hidden" name="vehicleId" id="vehicleId" value="<?php echo $vehicleId; ?>" />
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Vehicle Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="vehicleName" name="vehicleName" value="<?php echo $vehicleName; ?>">
        </div>
        <div class="span4 lightblue">
        <label>Vehicle Size<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT 'BESAR' as id, 'Besar' as info UNION SELECT 'BESAR' as id, 'Kecil' as info", $vehicleSize, "", "vehicleSize", "id", "info", "", 9, "");
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

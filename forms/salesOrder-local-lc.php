<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for labor Cost Data">

$so_lc_id = '';
//$laborId = '';
//$vendorId = '';
$salesId = $_POST['salesId'];
//$stockpile = $_POST['stockpile'];
$lc_id = '';
//$qtyAvailable = '';



// </editor-fold>

/*if(isset($_POST['sl_po_id']) && $_POST['sl_po_id'] != '') {
    $sl_po_id = $_POST['sl_po_id'];

    // <editor-fold defaultstate="collapsed" desc="Query for labor Cost Data">

    $sql = "SELECT *
            FROM sales_local_po a
            WHERE a.sl_po_id = {$sl_po_id}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $po_id = $rowData->po_id;
        $qtyAvailable = $rowData->quantity;
        $inv_rules = $rowData->inv_rules;

    }

    // </editor-fold>
}*/


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo1($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
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
    $(document).ready(function(){
		$("select.select3combobox100").select2({
            width: "100%"
        });

        $("select.select3combobox50").select2({
            width: "50%"
        });

        $("select.select3combobox75").select2({
            width: "75%"
        });

       /* if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRatelabor').hide();
        } else {
            $('#exchangeRatelabor').show();
        }*/

       

    });
</script>

<script type="text/javascript">

function resetlcDetail() {

        document.getElementById('labor').value = '';
        document.getElementById('customer').value = '';
        document.getElementById('priceConverted').value = '';
       
    }

    function setlcDetail(lc_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getlcDetail',
                lc_id: lc_id
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');

					document.getElementById('labor').value = returnVal[0];
                    document.getElementById('customer').value = returnVal[1];
                    document.getElementById('priceConverted').value = returnVal[2];
		
                }
            }
        });
    }

    $('#lc_id').change(function() {

    resetlcDetail(' ');
    if(document.getElementById('lc_id').value != '') {
        
        setlcDetail($('select[id="lc_id"]').val());
        
    }
    });
</script>

<input type="hidden" id="so_lc_id" name="so_lc_id" value="<?php echo $so_lc_id; ?>">
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>labor Cost Contract<span style="color: red;">*</span></label>
        <?php
        createCombo1("SELECT a.`labor_cost_id`, a.`contract_pkhoa`
        FROM labor_cost_local_sales a
        LEFT JOIN customer b ON a.`vendor_id` = b.`customer_id`
        LEFT JOIN sales_local c ON c.`customer_id` = b.`customer_id`
        LEFT JOIN labor_local_sales e ON e.`labor_id` = a.`labor_id`
        WHERE a.`status` = 1 AND c.`sales_con_id` = {$salesId} ORDER BY a.`labor_cost_id` DESC", $lc_id, "", "lc_id", "labor_cost_id", "contract_pkhoa",
                "", 1, "select3combobox100");
        ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>labor <span style="color: red;">*</span></label>
        <input type="text" class="span12" readonly id="labor" name="labor" >
    </div>
</div>
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>Customer<span style="color: red;">*</span></label>
        <input type="text" class="span12" readonly id="customer" name="customer" >
    </div>
</div>
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>Price <span style="color: red;">*</span></label>
        <input type="text" class="span12" readonly id="priceConverted" name="priceConverted" >
    </div>
</div>


<!--<div class="row-fluid">
    <div class="span8 lightblue">
    <label>Inventory Rules <span style="color: red;">*</span></label>
            <?php
           /* createCombo1("SELECT '0' as id, 'Lowest' as info UNION
                    SELECT '1' as id, 'Send Weight' as info UNION
                    SELECT '2' as id, 'Netto Weight' as info;", $inv_rules, '', "inv_rules", "id", "info", 
                "", 8, "select3combobox100");*/
            ?>
    </div>
</div>-->




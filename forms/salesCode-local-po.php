<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$sl_po_id = '';
//$freightId = '';
//$vendorId = '';
$salesId = $_POST['salesId'];
$stockpile = $_POST['stockpile'];
$po_id = '';
$qtyAvailable = '';



// </editor-fold>

if(isset($_POST['sl_po_id']) && $_POST['sl_po_id'] != '') {
    $sl_po_id = $_POST['sl_po_id'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

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
}


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
            $('#exchangeRateFreight').hide();
        } else {
            $('#exchangeRateFreight').show();
        }*/
        
        

    });
</script>

<script type="text/javascript">

function resetQtyDetail() {

        document.getElementById('qtyAvailable').value = '';
       
    }

    function setQtyDetail(po_id,sl_po_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getContractDetail',
                stockpileContractId: po_id,
                sl_po_id: sl_po_id
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');

                    <?php if ($po_id == ''){ ?>

					document.getElementById('qtyAvailable').value = returnVal[2];

                    <?php } ?>

                    document.getElementById('qtyAvailable2').value = returnVal[2];
		
                }
            }
        });
    }

    <?php if ($po_id !== ''){ ?>

    setQtyDetail(<?php echo $po_id;?>,<?php echo $sl_po_id;?>);

    <?php } ?>



    $('#po_id').change(function() {

    resetQtyDetail(' ');
    if(document.getElementById('po_id').value != '') {
        
        setQtyDetail($('select[id="po_id"]').val(),0);
        
    }
    });
</script>

<input type="hidden" id="sl_po_id" name="sl_po_id" value="<?php echo $sl_po_id; ?>">

<div class="row-fluid">
    <div class="span8 lightblue">
        <label>PO PKS <span style="color: red;">*</span></label>
        <?php
        createCombo1("SELECT a.stockpile_contract_id, c.po_no
        FROM stockpile_contract a
        LEFT JOIN stockpile b ON a.stockpile_id = b.`stockpile_id`
        LEFT JOIN contract c ON c.`contract_id` = a.`contract_id`
        WHERE a.stockpile_id = {$stockpile}
        AND c.`contract_status` != 2
        AND (a.`quantity` - 
        (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id) - 
	(SELECT CASE WHEN c.contract_type = 'C' AND c.qty_rule = 0 THEN COALESCE(SUM(t.send_weight), 0)
	WHEN c.contract_type = 'C' AND c.qty_rule != 0 THEN COALESCE(SUM(t.quantity), 0)
	ELSE COALESCE(SUM(t.send_weight), 0) END 
	FROM TRANSACTION t WHERE t.stockpile_contract_id = a.stockpile_contract_id)) > 0
        ORDER BY a.stockpile_contract_id DESC", $po_id, "", "po_id", "stockpile_contract_id", "po_no",
                "", 1, "select3combobox100");
        ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>Available Quantity<span style="color: red;">*</span></label>
        <input type="text" readonly class="span8" tabindex="5" id="qtyAvailable2" name="qtyAvailable2">
    </div>
</div>
<div class="row-fluid">
    <div class="span8 lightblue">
        <label>Quantity<span style="color: red;">*</span></label>
        <input type="text" class="span8" tabindex="5" id="qtyAvailable" name="qtyAvailable" value="<?php echo $qtyAvailable; ?>">
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




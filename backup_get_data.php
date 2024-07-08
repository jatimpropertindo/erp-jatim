<?php
function getContractDetail($stockpileContractId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id =  {$stockpileContractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $contractId = $row->contract_id;

        $sql = "SELECT con.contract_no, con.contract_type, con.vendor_id,
                FORMAT(((SELECT COALESCE(SUM(sc.quantity),0) FROM stockpile_contract sc WHERE sc.contract_id = con.contract_id) - (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = con.contract_id)) - 
                (SELECT CASE WHEN c.contract_type = 'C' AND c.qty_rule = 0 THEN COALESCE(SUM(t.send_weight), 0)
				WHEN c.contract_type = 'C' AND c.qty_rule != 0 THEN COALESCE(SUM(t.quantity), 0)
                ELSE COALESCE(SUM(t.send_weight), 0) END 
                FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
                LEFT JOIN contract c ON c.contract_id = sc.contract_id
                WHERE sc.`contract_id` = {$contractId}), 2) AS quantity_available
            FROM stockpile_contract sc
            LEFT JOIN contract con
                ON con.contract_id = sc.contract_id
            WHERE sc.stockpile_contract_id = {$stockpileContractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $returnValue = $row->contract_type . '||' . $row->contract_no . '||' . $row->quantity_available . '||' . $row->vendor_id;
        }
    }
    echo $returnValue;
}
?>
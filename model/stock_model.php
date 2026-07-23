<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Stock
{

    public function getAllStockItems()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_items si
        LEFT JOIN stock_categories sc ON si.stock_category_id = sc.stock_category_id
        LEFT JOIN stock_units su ON si.stock_unit_id  = su.stock_unit_id
        WHERE stock_item_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllStockCategories()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_categories WHERE stock_category_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllStockUnits()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_units WHERE stock_unit_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }


    //stock unit

    public function addStockUnit($stock_unit_name, $stock_unit_short_name, $stock_unit_description)
    {

        $con = $GLOBALS["con"];
        $sql = "INSERT INTO stock_units(stock_unit_name,stock_unit_short_name,stock_unit_description)VALUES('$stock_unit_name','$stock_unit_short_name','$stock_unit_description')";
        $con->query($sql) or die($con->error);
    }

    public function updateStockUnit($stock_unit_id, $stock_unit_name, $stock_unit_short_name, $stock_unit_description)
    {

        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_units SET stock_unit_name='$stock_unit_name', stock_unit_short_name='$stock_unit_short_name',stock_unit_description='$stock_unit_description' WHERE stock_unit_id='$stock_unit_id'";
        $con->query($sql) or die($con->error);
    }

    public function activateStockUnit($stock_unit_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_units SET stock_unit_status='1' WHERE stock_unit_id='$stock_unit_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivateStockUnit($stock_unit_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_units SET stock_unit_status='0' WHERE stock_unit_id='$stock_unit_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deleteStockUnit($stock_unit_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_units SET stock_unit_status='-1' WHERE stock_unit_id='$stock_unit_id'";
        $result = $con->query($sql) or die($con->error);
    }


    // stock category

    public function addStockCategory($stock_category_name, $stock_category_description)
    {

        $con = $GLOBALS["con"];
        $sql = "INSERT INTO stock_categories(stock_category_name,stock_category_description)VALUES('$stock_category_name','$stock_category_description')";
        $con->query($sql) or die($con->error);
    }

    public function deleteStockCategory($stock_category_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_categories SET stock_category_status='-1' WHERE stock_category_id='$stock_category_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function activateStockCategory($stock_category_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_categories SET stock_category_status='1' WHERE stock_category_id='$stock_category_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivateStockCategory($stock_category_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_categories SET stock_category_status='0' WHERE stock_category_id='$stock_category_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function updateStockCategory($stock_category_id, $stock_category_name, $stock_category_description)
    {

        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_categories SET stock_category_name='$stock_category_name', stock_category_description='$stock_category_description' WHERE stock_category_id='$stock_category_id'";
        $con->query($sql) or die($con->error);
    }

    // stock item

    public function addStockItem($stock_item_name, $stock_category_id, $stock_unit_id, $stock_item_color_code, $min_stock_level)
    {

        $con = $GLOBALS["con"];
        $sql = "INSERT INTO stock_items(stock_item_name,stock_category_id,stock_unit_id,stock_item_color_code,min_stock_level)VALUES('$stock_item_name','$stock_category_id','$stock_unit_id','$stock_item_color_code','$min_stock_level')";
        $con->query($sql) or die($con->error);
    }

    public function deleteStockItem($stock_item_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_items SET stock_item_status='-1' WHERE stock_item_id='$stock_item_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function activateStockItem($stock_item_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_items SET stock_item_status='1' WHERE stock_item_id='$stock_item_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivateStockItem($stock_item_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_items SET stock_item_status='0' WHERE stock_item_id='$stock_item_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function updateStockItem($stock_item_id, $stock_item_name, $stock_category_id, $stock_unit_id, $stock_item_color_code, $min_stock_level)
    {

        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_items SET stock_item_name='$stock_item_name', stock_category_id='$stock_category_id', stock_unit_id='$stock_unit_id',stock_item_color_code='$stock_item_color_code',min_stock_level='$min_stock_level' WHERE stock_item_id='$stock_item_id'";
        $con->query($sql) or die($con->error);
    }

    public function getAllStocks()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT 
                s.quantity,
                s.last_updated,
                si.stock_item_id,
                si.stock_item_name,
                si.stock_item_color_code,
                si.min_stock_level,
                sc.stock_category_name,
                su.stock_unit_name,
                su.stock_unit_short_name
            FROM stock s
            JOIN stock_items si ON s.stock_item_id = si.stock_item_id
            JOIN stock_categories sc ON si.stock_category_id = sc.stock_category_id
            JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id";

        return $con->query($sql);
    }

    public function addInventoryStockItem($stock_item_id, $quantity)
    {
        $con = $GLOBALS["con"];

        $check = $con->query("SELECT * FROM stock WHERE stock_item_id = '$stock_item_id'");

        if ($check->num_rows > 0) {

            $sql = "UPDATE stock 
                SET quantity = quantity + '$quantity' 
                WHERE stock_item_id = '$stock_item_id'";
        } else {

            $sql = "INSERT INTO stock (stock_item_id, quantity) 
                VALUES ('$stock_item_id', '$quantity')";
        }

        $con->query($sql) or die($con->error);
    }

    public function addStockTransaction($stock_item_id, $transaction_type, $quantity, $reference)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO stock_transactions(stock_item_id,transaction_type,quantity,reference)VALUES('$stock_item_id','$transaction_type','$quantity','$reference')";
        $con->query($sql) or die($con->error);
    }

    public function getAllTransactions()
    {
        $con = $GLOBALS['con'];

        $sql = "SELECT 
                st.*,
                si.stock_item_name,
                si.stock_item_color_code,
                su.stock_unit_short_name
            FROM stock_transactions st
            JOIN stock_items si ON st.stock_item_id = si.stock_item_id
            JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id
            ORDER BY st.transaction_date DESC";

        return $con->query($sql);
    }

    public function getLowStockItems()
    {
        $con = $GLOBALS['con'];

        $sql = "SELECT 
                s.stock_item_id,
                si.stock_item_name,
                si.stock_item_color_code,
                s.quantity,
                si.min_stock_level,
                su.stock_unit_name,
                su.stock_unit_short_name,
                sc.stock_category_name
            FROM stock s
            JOIN stock_items si 
                ON s.stock_item_id = si.stock_item_id
            JOIN stock_units su 
                ON si.stock_unit_id = su.stock_unit_id
            JOIN stock_categories sc 
                ON si.stock_category_id = sc.stock_category_id

            LEFT JOIN stock_purchase_request spr 
                ON s.stock_item_id = spr.stock_item_id
                AND spr.request_status IN ('Pending','Sent','PO Created')

            WHERE s.quantity <= si.min_stock_level
            AND spr.stock_purchase_request_id IS NULL";

        return $con->query($sql);
    }

    public function getOutOfStocksCount()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT COUNT(stock_id) as total FROM stock WHERE quantity = 0";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllStockRequests()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM stock_requests sr, plan p, orders o, buyer_company bc WHERE sr.stock_request_status != 'Hold' AND sr.stock_request_status != 'Rejected' AND sr.plan_id = p.plan_id AND p.order_id = o.order_id AND o.company_id = bc.company_id";
        return $con->query($sql);
    }

    public function addPurchaseRequest($stock_item_id, $requested_qty)
    {
        $con = $GLOBALS['con'];
        $sql = "INSERT INTO stock_purchase_request(stock_item_id,requested_qty)VALUES('$stock_item_id','$requested_qty')";
        $con->query($sql) or die($con->error);
    }

    public function getAllPurchaseRequests()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * 
        FROM stock_purchase_request spr
        JOIN stock s ON spr.stock_item_id = s.stock_item_id
        JOIN stock_items si ON spr.stock_item_id = si.stock_item_id
        JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id
        JOIN stock_categories sc ON si.stock_category_id = sc.stock_category_id";
        $result = $con->query($sql);
        return $result;
    }

    public function approvePurchaseRequest($stock_purchase_request_id, $approved_by, $approved_date)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_purchase_request SET request_status='Approved',approved_rejected_by='$approved_by',approved_rejected_date='$approved_date' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        return $con->query($sql) or die($con->error);
    }

    public function rejectPurchaseRequest($stock_purchase_request_id, $rejected_by, $rejected_date, $remarks)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_purchase_request SET request_status='Rejected',approved_rejected_by='$rejected_by',approved_rejected_date='$rejected_date', remarks='$remarks' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        return $con->query($sql) or die($con->error);
    }

    public function getApprovePurchaseRequestsCount()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT COUNT(*) as approved_purchase_requests_count 
        FROM stock_purchase_request spr
        JOIN stock s ON spr.stock_item_id = s.stock_item_id
        JOIN stock_items si ON spr.stock_item_id = si.stock_item_id
        JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id
        JOIN stock_categories sc ON si.stock_category_id = sc.stock_category_id
        LEFT JOIN user u ON spr.requested_by = u.user_id
        WHERE spr.request_status = 'Approved'";
        return $con->query($sql);
    }

    public function updatePurchaseRequestStatus($stock_purchase_request_id, $request_status)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_purchase_request SET request_status='$request_status' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        return $con->query($sql) or die($con->error);
    }

    public function updatePOIDPurchaseRequest($stock_purchase_request_id, $po_id)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_purchase_request SET po_id='$po_id' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        return $con->query($sql) or die($con->error);
    }


    public function addStockRequest($plan_id)
    {
        $con = $GLOBALS['con'];
        $sql = "INSERT INTO stock_requests(plan_id) VALUES ('$plan_id');";
        $con->query($sql) or die($con->error);
        $stock_request_id = $con->insert_id;
        return $stock_request_id;
    }

    public function addStockRequestItem($stock_request_id, $stock_item_id, $requested_qty)
    {
        $con = $GLOBALS['con'];
        $sql = "INSERT INTO stock_request_items(stock_request_id, stock_item_id, requested_qty) VALUES ('$stock_request_id','$stock_item_id','$requested_qty');";
        $con->query($sql) or die($con->error);
    }

    public function getStockRequest($plan_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_requests WHERE plan_id = '$plan_id'";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function getStockRequestItems($plan_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_requests sr, stock_request_items sri, stock_items si, stock_units su WHERE sr.plan_id = '$plan_id' AND sr.stock_request_id = sri.stock_request_id AND sri.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function updateStockRequestStatus($plan_id, $status)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_requests SET stock_request_status='$status' WHERE plan_id = '$plan_id'";
        $con->query($sql) or die($con->error);
    }

    public function updateStockRequestItemStatus($stock_request_id, $status)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE stock_request_items SET stock_request_item_status='$status' WHERE stock_request_id = '$stock_request_id'";
        $con->query($sql) or die($con->error);
    }

    public function getStockRequestItemsForStocks($stock_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_requests sr, stock_request_items sri, stock_items si, stock_units su, stock_categories sc, stock s, plan p, orders o WHERE sr.stock_request_id = '$stock_request_id' AND sr.stock_request_id = sri.stock_request_id AND sri.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id AND si.stock_category_id = sc.stock_category_id AND s.stock_item_id = si.stock_item_id AND sr.plan_id = p.plan_id AND p.order_id = o.order_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function issueStockItems($stock_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_request_items SET stock_request_item_status ='Issued' WHERE stock_request_id = '$stock_request_id'";
        $con->query($sql) or die($con->error);
    }

    public function issueStockRequest($stock_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_requests SET stock_request_status ='Issued' WHERE stock_request_id = '$stock_request_id'";
        $con->query($sql) or die($con->error);
    }

    public function checkStockExist($stock_item_id, $quantity)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock WHERE stock_item_id = '$stock_item_id' AND quantity >= '$quantity'";
        $result = $con->query($sql) or die($con->error);
        return $result->num_rows > 0;
    }

    public function outInventoryStockItem($stock_item_id, $quantity)
    {
        $con = $GLOBALS["con"];
        $check = $con->query("SELECT * FROM stock WHERE stock_item_id = '$stock_item_id'");
        if ($check->num_rows > 0) {
            $sql = "UPDATE stock 
                SET quantity = quantity - '$quantity' 
                WHERE stock_item_id = '$stock_item_id'";
        }
        $con->query($sql) or die($con->error);
    }

    public function getAllProductionStockRequest()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM production_stock_request psr, stock_items si, stock_units su WHERE psr.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }
    
    public function rejectProductionStockRequest($psr_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE production_stock_request SET psr_status='Rejected' WHERE psr_id = '$psr_id'";
        $con->query($sql) or die($con->error);
    }
    
    public function issueProductionStockRequest($psr_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE production_stock_request SET psr_status='Issued' WHERE psr_id = '$psr_id'";
        $con->query($sql) or die($con->error);
    }

    public function getTopRequestedMaterials()
{
    $con = $GLOBALS["con"];

    $sql = "SELECT 
                s.stock_item_name,
                s.stock_item_color_code,
                SUM(psr.psr_qty) AS total_qty
            FROM production_stock_request psr
            JOIN stock_items s 
            ON psr.stock_item_id = s.stock_item_id
            GROUP BY psr.stock_item_id
            ORDER BY total_qty DESC
            LIMIT 10";

    $result = $con->query($sql) or die($con->error);

    return $result;
}
}

<?php
include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Order
{
    // Add a new order
    public function addOrder($company_id, $user_id, $address_line_1, $address_line_2, $address_line_3, $district_id, $expected_delivery_date, $total_amount, $delivery_charge, $comments, $file_name)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO orders (company_id, user_id, address_line_1, address_line_2, address_line_3, district_id, expected_delivery_date, total_amount, delivery_charge, comments, design, status_id)
                VALUES ('$company_id', '$user_id', '$address_line_1', '$address_line_2', '$address_line_3', '$district_id', '$expected_delivery_date', '$total_amount', '$delivery_charge', '$comments', '$file_name', '1')";
        $con->query($sql) or die($con->error);
        return $con->insert_id;
    }

    // Add an order item
    public function addOrderItem($order_id, $product_id, $size_id, $qty, $unit_price)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO order_item (order_id, product_id, size_id, qty, unit_price) 
                VALUES ('$order_id', '$product_id', '$size_id', '$qty', '$unit_price')";
        $con->query($sql) or die($con->error);
    }

    // Add a status log entry
    public function addOrderStatusLog($order_id, $status_id, $changed_by, $remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO order_status_log (order_id, status_id, changed_by, remarks, changed_at) 
                VALUES ('$order_id', '$status_id', '$changed_by', '$remarks', NOW())";
        $con->query($sql) or die($con->error);
    }

    // Add a payment
    public function addOrderPayment($order_id, $amount, $payment_method, $payment_status, $reference_no)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO order_payment (order_id, amount, payment_method, payment_status, reference_no, payment_datetime) 
                VALUES ('$order_id', '$amount', '$payment_method', '$payment_status', '$reference_no', NOW())";
        $con->query($sql) or die($con->error);
    }


    // Get all orders
    public function getAllOrders()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM orders o
                LEFT JOIN buyer_company b ON o.company_id = b.company_id
                LEFT JOIN buyer_contact_person bcp ON o.company_id = bcp.company_id
                LEFT JOIN user u ON o.user_id = u.user_id
                LEFT JOIN district d ON o.district_id = d.district_id
                LEFT JOIN order_status os ON o.status_id = os.status_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllPendingOrders()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM orders o
                LEFT JOIN buyer_company b ON o.company_id = b.company_id
                LEFT JOIN buyer_contact_person bcp ON o.company_id = bcp.company_id
                LEFT JOIN user u ON o.user_id = u.user_id
                LEFT JOIN order_status os ON o.status_id = os.status_id
                WHERE o.status_id=1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllPendingOrdersCount()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT COUNT(*) as pending_orders_count FROM orders WHERE status_id = 1;";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllConfirmedOrders()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM orders o
                LEFT JOIN buyer_company b ON o.company_id = b.company_id
                LEFT JOIN buyer_contact_person bcp ON o.company_id = bcp.company_id
                LEFT JOIN user u ON o.user_id = u.user_id
                LEFT JOIN order_status os ON o.status_id = os.status_id
                WHERE o.status_id=2";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    


    // Get single order by ID
    public function getOrder($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM orders o 
        LEFT JOIN buyer_company bc ON o.company_id = bc.company_id
        LEFT JOIN buyer_contact_person bcp ON o.company_id = bcp.company_id
        LEFT JOIN district d ON o.district_id = d.district_id 
        LEFT JOIN order_status os ON o.status_id = os.status_id WHERE order_id='$order_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }



    // Get items for an order
    public function getOrderItems($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_item oi
                LEFT JOIN product_type p ON oi.product_id = p.product_type_id
                LEFT JOIN sizing s ON oi.size_id = s.size_id
                WHERE oi.order_id='$order_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    // Get payment for an order
    public function getOrderPayments($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM order_payment WHERE order_id='$order_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function cancelOrder($order_id, $user_id, $remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE orders SET status_id='10' WHERE order_id='$order_id'";
        $sql2 = "INSERT INTO order_status_log(order_id, status_id, changed_by, remarks) VALUES ('$order_id','10','$user_id','$remarks');";
        $con->query($sql) or die($con->error);
        $con->query($sql2) or die($con->error);
    }

    public function confirmOrder($order_id, $user_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE orders SET status_id='2' WHERE order_id='$order_id'";
        $sql2 = "INSERT INTO order_status_log(order_id, status_id, changed_by, remarks) VALUES ('$order_id','2','$user_id','Order confirmed');";
        $con->query($sql) or die($con->error);
        $con->query($sql2) or die($con->error);
    }

    public function addNewOrderPayment($order_id, $amount, $payment_method, $reference_no)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO order_payment(order_id, amount, payment_method, reference_no) VALUES ('$order_id','$amount','$payment_method','$reference_no');";
        $con->query($sql) or die($con->error);
    }

    // Get status log for an order
    public function getOrderStatusLogs($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT osl.*, s.*, u.user_fname, u.user_lname
                FROM order_status_log osl
                LEFT JOIN order_status s ON osl.status_id = s.status_id
                LEFT JOIN user u ON osl.changed_by = u.user_id
                WHERE osl.order_id='$order_id'
                ORDER BY osl.changed_at ASC";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllOrderPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_payment op
                LEFT JOIN orders o ON op.order_id = o.order_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getApprovedOrderPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_payment op
                LEFT JOIN orders o ON op.order_id = o.order_id
                WHERE op.payment_status = 'Approved'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getPendingOrderPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_payment op
                LEFT JOIN orders o ON op.order_id = o.order_id
                WHERE op.payment_status = 'Pending'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getPendingOrderPaymentsCount()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT COUNT(*) as pending_count
            FROM order_payment op
            LEFT JOIN orders o ON op.order_id = o.order_id
            WHERE op.payment_status = 'Pending'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getRejectedOrderPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_payment op
                LEFT JOIN orders o ON op.order_id = o.order_id
                WHERE op.payment_status = 'Rejected'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function approveOrderPayment($order_payment_id, $payment_remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE order_payment SET payment_status='Approved' , payment_remarks='$payment_remarks' WHERE order_payment_id ='$order_payment_id '";
        $result = $con->query($sql) or die($con->error);
    }

    public function rejectOrderPayment($order_payment_id, $payment_remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE order_payment SET payment_status='Rejected' , payment_remarks='$payment_remarks' WHERE order_payment_id ='$order_payment_id '";
        $result = $con->query($sql) or die($con->error);
    }

    public function updateOrderStatus($order_id, $user_id, $status_id, $remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE orders SET status_id='$status_id' WHERE order_id='$order_id'";
        $sql2 = "INSERT INTO order_status_log(order_id, status_id, changed_by, remarks) VALUES ('$order_id','$status_id','$user_id','$remarks');";
        $con->query($sql) or die($con->error);
        $con->query($sql2) or die($con->error);
    }

}

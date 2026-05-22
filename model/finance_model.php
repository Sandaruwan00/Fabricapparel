<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Finance
{
    public function addExpense($expense_category, $expense_amount, $expense_date, $expense_description)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO expenses(expense_category, expense_amount, expense_date, expense_description) VALUES ('$expense_category','$expense_amount','$expense_date','$expense_description')";
        $con->query($sql) or die($con->error);
    }

    public function getAllExpenses()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM expenses";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
    
    public function getAllApprovedPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM order_payment WHERE payment_status = 'Approved'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function rejectExpense($expense_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE expenses SET expense_status = 'Rejected' WHERE expense_id = '$expense_id'";
        $con->query($sql) or die($con->error);
    }
    
    public function approveExpense($expense_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE expenses SET expense_status = 'Approved' WHERE expense_id = '$expense_id'";
        $con->query($sql) or die($con->error);
    }

    public function processRefund($refund_id,$remarks,$payment_method,$reference_no)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE order_refunds SET remarks='$remarks',payment_method='$payment_method',reference_no='$reference_no',refund_status='Processed' WHERE refund_id = '$refund_id'";
        $con->query($sql) or die($con->error);
    }

    public function getAllPOPayments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM purchase_order_payments pop, purchase_orders po, supplier s WHERE pop.po_id = po.po_id AND po.supplier_id = s.supplier_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function payPOPayment($po_payment_id,$payment_method,$reference_no)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE purchase_order_payments SET payment_method='$payment_method',reference_no='$reference_no',po_payment_status='Paid' WHERE po_payment_id='$po_payment_id'";
        $con->query($sql) or die($con->error);
    }
}

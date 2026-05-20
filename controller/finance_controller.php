<?php
include '../commons/session.php';
include '../model/finance_model.php';

$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];

$financeObj = new Finance();

switch ($status) {

    case "add_expense":

        $expense_category = $_POST["expense_category"];
        $expense_amount = $_POST["expense_amount"];
        $expense_date = $_POST["expense_date"];
        $expense_description = $_POST["expense_description"];

        try {

            $financeObj->addExpense($expense_category, $expense_amount, $expense_date, $expense_description);

            $msg = "Expenses Added";
            $msg = base64_encode($msg);
    ?>
            <script>
                window.location = "../view/view-expenses.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-expense.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "reject_expense":

        $expense_id = $_POST["expense_id"];

        try {

            $financeObj->rejectExpense($expense_id);

            $msg = "Expense Rejected";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-expenses.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-expenses.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;
    
        case "approve_expense":

        $expense_id = $_POST["expense_id"];

        try {

            $financeObj->approveExpense($expense_id);

            $msg = "Expense Approved";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-expenses.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-expenses.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;

        case "process_refund":
        
                $refund_id = $_POST["refund_id"];
                $remarks = $_POST["remarks"];
                $payment_method = $_POST["payment_method"];
                $reference_no = $_POST["reference_no"];
        
                try {
        
                    $financeObj->processRefund($refund_id,$remarks,$payment_method,$reference_no);
        
                    $msg = "Refund Processed";
                    $msg = base64_encode($msg);
                ?>
                    <script>
                        window.location = "../view/refund.php?msg=<?php echo $msg; ?>";
                    </script>
                <?php
        
                } catch (Exception $ex) {
                    $msg = $ex->getMessage();
                    $msg = base64_encode($msg);
                ?>
                    <script>
                        window.location = "../view/refund.php?msg=<?php echo $msg; ?>";
                    </script>
                <?php
                }
                break;
}

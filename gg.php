<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Order Reports</button>




<div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-order-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Order Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>

                    <br>

                    <label>End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Generate Report
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>


<script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>


<?php

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 10)) {
    header("Location: access_denied.php");
    exit();
}

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 10)) {
    throw new Exception("Access Denied");
}


?>


<?php if ($permissionObj->hasPermission($userrow["user_id"], 10)) { ?>

<?php } ?>
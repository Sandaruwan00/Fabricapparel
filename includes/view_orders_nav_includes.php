<?php
$pendingCount = $orderObj->getAllPendingOrdersCount();
$badge = $pendingCount->fetch_assoc();

?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= $currentPage == 'view-orders' ? 'active' : ''; ?>" aria-current="page" href="view-orders.php">All Orders</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $currentPage == 'pending-orders' ? 'active' : ''; ?>" href="view-orders-pending.php">Pending Orders <span class="badge text-bg-warning"><?php echo $badge["pending_orders_count"];  ?></span></a>
    </li>
</ul>
<?php
$approvedCount = $stockObj->getApprovePurchaseRequestsCount();
$badge = $approvedCount->fetch_assoc();

?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= $currentPage == 'rfq-sent' ? 'active' : ''; ?>" href="rfq.php">Sent RFQ</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $currentPage == 'approved-purchase-requests' ? 'active' : ''; ?>" aria-current="page" href="rfq-approved-purchase-requests.php">Approved Purchase Requests <span class="badge text-bg-warning"><?php echo $badge["approved_purchase_requests_count"];  ?></span></a>
    </li>
</ul>
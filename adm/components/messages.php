<?php
if (!isset($_SESSION['error']) && !isset($_SESSION['success']) && !isset($_SESSION['warning'])) {
    return;
}
?>

<div class="container pt-2">
    <?php
    require_once 'error.php';
    require_once 'success.php';
    require_once 'warning.php';
    ?>
</div>

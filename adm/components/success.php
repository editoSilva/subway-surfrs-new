<?php if (isset($_SESSION['success'])) { ?>
    <?php foreach ($_SESSION['success'] as $msg) { ?>
        <div class="alert alert-success" role="alert">
            <div class="d-flex flex-row align-items-center">
                <i class="fas fa-check-circle"></i>
                <p class="m-0 pl-2"><?php echo $msg ?></p>
            </div>
        </div>
    <?php } ?>
    <?php $_SESSION['success'] = [] ?>
<?php } ?>
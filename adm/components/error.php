<?php if (isset($_SESSION['errors'])) { ?>
    <?php foreach ($_SESSION['errors'] as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <div class="d-flex flex-row align-items-center">
                <i class="fas fa-exclamation-circle"></i>
                <p class="m-0 pl-2"><?php echo $error ?></p>
            </div>
        </div>
    <?php } ?>
    <?php $_SESSION['errors'] = [] ?>
<?php } ?>
<?php if (isset($_SESSION['warning'])) { ?>
    <?php foreach ($_SESSION['warning'] as $warning) { ?>
        <div class="alert alert-warning" role="alert">
            <div class="d-flex flex-row align-items-start">
                <i class="fas fa-exclamation-circle pt-1"></i>
                <p class="m-0 pl-2"><?php echo $warning ?></p>
            </div>
        </div>
    <?php } ?>
    <?php $_SESSION['warning'] = [] ?>
<?php } ?>
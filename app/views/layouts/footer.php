<?php
/**
 * Footer Layout
 * Closes the main content structure and includes scripts
 */
?>
</div> <!-- Close container-fluid -->
</div> <!-- Close page-content-wrapper -->
</div> <!-- Close wrapper -->

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/select2-init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
    crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/public/js/currency-formatter.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/form-enhancements.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/transaction-verification.js"></script>

<!-- Additional scripts for specific pages -->
<?php if (isset($data['scripts'])): ?>
    <?php foreach ($data['scripts'] as $script): ?>
        <script src="<?php echo URLROOT; ?>/js/<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Session Management (only for logged-in users) -->
<?php if (isLoggedIn()): ?>
    <!-- <script src="<?php echo URLROOT; ?>/assets/js/session-monitor.js"></script> -->
<?php endif; ?>

</body>

</html>
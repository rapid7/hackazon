<?php include __DIR__ . '/common/start.php'; ?>
<?php include __DIR__ . '/common/header.php'; ?>

<?php if (isset($subview)): ?>
    <?php include __DIR__ . '/' . $subview . '.php' ; ?>
<?php endif; ?>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/end.php'; ?>
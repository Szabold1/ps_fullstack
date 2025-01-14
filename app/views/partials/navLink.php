<?php
$href = htmlspecialchars($href) ?? '#';
$text = htmlspecialchars($text) ?? '';
?>

<?php if ($href === '/logout') : ?>
    <form action="<?= $href ?>" method="post" class="block px-3.5 py-2 hover:bg-slate-100 rounded-lg">
        <button type="submit" class="w-full h-full">
            <?= $text ?>
        </button>
    </form>
    <?php return; ?>
<?php endif; ?>

<a href="<?= $href ?>" class="block px-3.5 py-2 hover:bg-slate-100 rounded-lg">
    <?= $text ?>
</a>
<?php
$href = htmlspecialchars($href) ?? '#';
$text = htmlspecialchars($text) ?? '';
?>

<a href="<?= $href ?>" class="block px-3.5 py-2 hover:bg-slate-100 rounded-lg">
    <?= $text ?>
</a>
<?php loadPartial('top'); ?>

<main class="px-3 py-6 mx-auto flex flex-col items-center justify-center">
    <h2 class="mt-20 mb-8 text-center text-2xl/9 font-bold"><?= $message ?? 'Hiba történt' ?></h2>
    <a href="/" class="text-blue-600 hover:underline">Vissza a főoldalra</a>
</main>

<?php loadPartial('bottom'); ?>
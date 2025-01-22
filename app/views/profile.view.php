<?php

use Framework\Helper;
use Framework\Session;

$nickname = Session::getFlash(Session::USER)['nickname'] ?? '';

Session::unsetFlashAll();
?>

<?php Helper::loadPartial('top'); ?>
<?php Helper::loadPartial('navbar'); ?>

<main class="px-3 py-6 mx-auto flex flex-col items-center justify-center">
    <h2 class="mt-4 mb-8 text-center text-2xl/9 font-bold">Profil</h2>

    <p class="text-lg">Üdvözöljük honlapunkon, <?= $nickname ?></p>
</main>

<?php Helper::loadPartial('bottom'); ?>
<?php

use Framework\Helper;

$errors ??= [];
$user ??= [];
$successMsg ??= '';
?>

<?php Helper::loadPartial('top'); ?>
<?php Helper::loadPartial('navbar'); ?>

<main class="px-3 py-6 mx-auto flex flex-col items-center justify-center">
    <?php if ($successMsg) : ?>
        <div class="text-green-800 bg-green-100 rounded-md px-2.5 py-1 mb-3">
            <?= $successMsg ?>
        </div>
    <?php endif; ?>

    <h2 class="mt-4 mb-8 text-center text-2xl/9 font-bold">Profil szerkesztése</h2>

    <form class="flex flex-col items-center gap-4 max-w-72 w-full" action="/profile/edit" method="post">
        <input type="hidden" name="_method" value="PUT">

        <?php Helper::loadPartial('formInput', [
            'label' => 'Becenév',
            'name' => 'nickname',
            'value' => $user['nickname'] ?? '',
            'type' => 'text',
            'placeholder' => 'Peti',
            'required' => true,
            'error' => $errors['nickname'] ?? ''
        ]); ?>

        <?php Helper::loadPartial('formInput', [
            'label' => 'Születési dátum',
            'name' => 'birthdate',
            'value' => $user['birthdate'] ?? '',
            'type' => 'date',
            'required' => true,
            'error' => $errors['birthdate'] ?? ''
        ]); ?>

        <?php Helper::loadPartial('formInput', [
            'label' => 'Új jelszó',
            'name' => 'password',
            'value' => $user['password'] ?? '',
            'type' => 'password',
            'error' => $errors['password'] ?? ''
        ]); ?>

        <div class="w-full mt-4">
            <?php Helper::loadPartial('formBtn', ['text' => 'Mentés']); ?>
        </div>
    </form>
</main>

<?php Helper::loadPartial('bottom'); ?>
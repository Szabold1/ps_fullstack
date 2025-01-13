<?php
$errors ??= [];
$user ??= [];
$successMsg ??= '';
?>

<?php loadPartial('top'); ?>
<?php loadPartial('navbar'); ?>

<main class="px-3 py-6 mx-auto flex flex-col items-center justify-center">
    <?php if ($successMsg) : ?>
        <div class="text-green-800 bg-green-100 rounded-md px-2.5 py-1 mb-3">
            <?= $successMsg ?>
        </div>
    <?php endif; ?>

    <h2 class="mt-4 mb-8 text-center text-2xl/9 font-bold">Regisztráció</h2>

    <form class="flex flex-col items-center gap-4 max-w-72 w-full" action="/register" method="post">
        <?php loadPartial('formInput', [
            'label' => 'Email',
            'name' => 'email',
            'value' => $user['email'] ?? '',
            'type' => 'email',
            'placeholder' => 'peter@gmail.com',
            'required' => true,
            'error' => $errors['email'] ?? ''
        ]); ?>

        <?php loadPartial('formInput', [
            'label' => 'Becenév',
            'name' => 'nickname',
            'value' => $user['nickname'] ?? '',
            'type' => 'text',
            'placeholder' => 'Peti',
            'required' => true,
            'error' => $errors['nickname'] ?? ''
        ]); ?>

        <?php loadPartial('formInput', [
            'label' => 'Születési dátum',
            'name' => 'birthdate',
            'value' => $user['birthdate'] ?? '',
            'type' => 'date',
            'required' => true,
            'error' => $errors['birthdate'] ?? ''
        ]); ?>

        <?php loadPartial('formInput', [
            'label' => 'Jelszó',
            'name' => 'password',
            'type' => 'password',
            'required' => true,
            'error' => $errors['password'] ?? ''
        ]); ?>

        <div class="w-full mt-4">
            <?php loadPartial('formBtn', ['text' => 'Regisztráció']); ?>
        </div>
    </form>
</main>

<?php loadPartial('bottom'); ?>
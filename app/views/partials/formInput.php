<?php
$label ??= '';
$name ??= '';
$value ??= '';
$type ??= '';
$placeholder ??= '';
$required ??= '';
$error ??= '';
?>

<div class="w-full">
    <label class="block text-sm/6 font-medium">
        <?= $label ?>
    </label>
    <input
        type="<?= $type ?>"
        name="<?= $name ?>"
        value="<?= $value ?? '' ?>"
        autoComplete="<?= $name ?>"
        <?= $required ? 'required' : '' ?>
        placeholder="<?= $placeholder ?>"
        class="block w-full rounded-md bg-white mt-2 px-3 py-1.5 text-base  outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
    <?php if ($error) : ?>
        <div class="text-red-800 bg-red-100 rounded-md text-sm px-2 py-1 mt-2">
            <?= $error ?>
        </div>
    <?php endif; ?>
</div>
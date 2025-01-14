<header class="my-4 mx-auto flex flex-wrap items-center justify-center">
    <nav class="flex items-center justify-center bg-slate-200 py-1.5 px-1.5 rounded-xl max-w-max font-semibold">
        <?php if (!Framework\Session::get('user')): ?>
            <?php loadPartial('navLink', ['href' => '/', 'text' => 'Kezdőlap']); ?>
            <?php loadPartial('navLink', ['href' => '/register', 'text' => 'Regisztráció']); ?>
            <?php loadPartial('navLink', ['href' => '/login', 'text' => 'Bejelentkezés']); ?>
        <?php else: ?>
            <?php loadPartial('navLink', ['href' => '/profile', 'text' => 'Profil']); ?>
            <?php loadPartial('navLink', ['href' => '/profile/edit', 'text' => 'Profil szerkesztése']); ?>
            <?php loadPartial('navLink', ['href' => '/logout', 'text' => 'Kijelentkezés']); ?>
        <?php endif; ?>
    </nav>
</header>
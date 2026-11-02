<?php

use App\Services\Hook;
use Blessing\Filter;
use Blessing\Rejection;
use Tacraft\TcsNoRegister\Middleware\BlockRegistration;

return function (Filter $filter) {
    // The login page used to show a "register" link. Drop it and explain what
    // replaced it: the third party buttons that the OAuth plugins provide.
    $filter->add('auth_page_rows:login', function (array $rows): array {
        $rows = array_values(array_filter(
            $rows,
            fn ($row) => $row !== 'auth.rows.login.registration-link'
        ));

        // Keep the notice at the very bottom of the card: it belongs after the
        // sign-in button, below the third party buttons and the form.
        if (! in_array('Tacraft\TcsNoRegister::notice', $rows, true)) {
            $rows[] = 'Tacraft\TcsNoRegister::notice';
        }

        return $rows;
    });

    // Server side counterpart: even a hand crafted POST cannot create an
    // account with an email and a password. Third party logins do not go
    // through this hook, so they keep working. Existing accounts keep their
    // email/password login and can still change both on their profile page.
    $filter->add('can_register', function ($can) {
        return new Rejection(trans('Tacraft\TcsNoRegister::general.register-disabled'));
    });

    try {
        Hook::pushMiddleware(BlockRegistration::class);
    } catch (\Throwable $e) {
        report($e);
    }
};

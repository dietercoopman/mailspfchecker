<?php

use Dietercoopman\Mailspfchecker\Mailspfchecker;

it('check you can not send email as info@dietse.dev', function () {
    $checker = app(Mailspfchecker::class);
    expect($checker->canISendAs('info@dietse.dev'))->toBeFalse();
});

it('it can show the dns record', function () {
    $checker = app(Mailspfchecker::class);
    expect($checker->howCanISendAs('info@dietse.dev'))->toContain('Generate a txt-record with a name of')->toContain('_spf.mailgun.org');
});

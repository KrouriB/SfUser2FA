<?php

beforeEach(function () {
    $user = new User();
    $user->setEmail('test@tset.tt');
    // $user->setPseudo('Test');
    $user->sethashedPassword('MotDePasse');
});

it('', function () {
    expect(true)->toBeTrue();
});

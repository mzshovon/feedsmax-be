<?php

it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with('emails');



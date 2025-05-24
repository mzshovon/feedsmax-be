<?php

test("contracts are interfaces")
    ->arch('app')
    ->expect('App\Services\Contracts')
    ->toBeInterfaces();

test("CMS contracts are interfaces")
    ->arch('app')
    ->expect('App\Services\CMS\Contracts')
    ->toBeInterfaces();

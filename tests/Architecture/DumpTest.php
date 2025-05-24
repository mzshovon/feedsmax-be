<?php

test("has dd or dump in app")
    ->arch('app')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();

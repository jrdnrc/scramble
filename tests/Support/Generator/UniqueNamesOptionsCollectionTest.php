<?php

use Dedoc\Scramble\Support\Generator\UniqueNameOptions;
use Dedoc\Scramble\Support\Generator\UniqueNamesOptionsCollection;

it('uses the short fallback when the only other name sharing it resolves to its own eloquent name', function () {
    $named = new UniqueNameOptions(eloquent: 'companies.storeUser', unique: ['companies', 'users', 'store']);
    $unnamed = new UniqueNameOptions(eloquent: null, unique: ['users', 'users', 'store']);

    $names = (new UniqueNamesOptionsCollection)->push($named)->push($unnamed);

    expect($names->getUniqueName($named))->toBe('companies.storeUser')
        ->and($names->getUniqueName($unnamed))->toBe('users.store');
});

it('uses the full fallback when another unnamed name shares the short fallback', function () {
    $a = new UniqueNameOptions(eloquent: null, unique: ['companies', 'users', 'store']);
    $b = new UniqueNameOptions(eloquent: null, unique: ['users', 'users', 'store']);

    $names = (new UniqueNamesOptionsCollection)->push($a)->push($b);

    expect($names->getUniqueName($a))->toBe('companies.users.store')
        ->and($names->getUniqueName($b))->toBe('users.users.store');
});

it('counts names with a duplicated eloquent name as competing for the fallback', function () {
    $a = new UniqueNameOptions(eloquent: 'duplicate', unique: ['companies', 'users', 'store']);
    $b = new UniqueNameOptions(eloquent: 'duplicate', unique: ['admin', 'users', 'store']);
    $c = new UniqueNameOptions(eloquent: null, unique: ['users', 'users', 'store']);

    $names = (new UniqueNamesOptionsCollection)->push($a)->push($b)->push($c);

    expect($names->getUniqueName($a))->toBe('companies.users.store')
        ->and($names->getUniqueName($b))->toBe('admin.users.store')
        ->and($names->getUniqueName($c))->toBe('users.users.store');
});

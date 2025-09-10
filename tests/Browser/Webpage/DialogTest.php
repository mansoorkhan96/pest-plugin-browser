<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\ExpectationFailedException;

it('can handle alert dialog with custom handler', function (): void {
    Route::get('/', fn (): string => '
        <button id="alert-btn" onclick="alert(\'Hello World!\'); document.getElementById(\'result\').textContent = \'Alert handled\';">Show Alert</button>
        <div id="result"></div>
    ');

    $dialogHandled = false;
    $dialogMessage = '';

    $page = visit('/')->onDialog(function ($dialog) use (&$dialogHandled, &$dialogMessage) {
        $dialogHandled = true;
        $dialogMessage = $dialog->message();
        expect($dialog->type())->toBe('alert');
        $dialog->accept();
    });

    $page->click('#alert-btn');

    expect($dialogHandled)->toBeTrue();
    expect($dialogMessage)->toBe('Hello World!');
    expect($page->text('#result'))->toBe('Alert handled');
});

it('can auto dismiss dialog', function (): void {
    Route::get('/', fn (): string => '
        <button id="alert-btn" onclick="alert(\'Hello World!\');">Show Alert</button>
        <p>Normal text on page.</p>
    ');

    $page = visit('/');

    $page->assertSee('Normal text on page.');
});

it('can not auto dismiss dialog when interupted', function (): void {
    Route::get('/', fn (): string => '
        <button id="alert-btn" onclick="alert(\'Hello World!\');">Show Alert</button>
        <p>Normal text on page.</p>
    ');

    $page = visit('/')
        ->onDialog(function ($dialog) {
            //
        });

    $page->click('#alert-btn');
})->throws(ExpectationFailedException::class);


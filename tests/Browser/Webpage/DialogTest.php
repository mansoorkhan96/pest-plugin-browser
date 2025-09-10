<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Pest\Browser\Playwright\Dialog;
use PHPUnit\Framework\ExpectationFailedException;

it('can handle alert dialog with custom handler', function (): void {
    Route::get('/', fn (): string => '
        <button id="alert-btn" onclick="alert(\'Hello World!\'); document.getElementById(\'result\').textContent = \'Alert handled\';">Show Alert</button>
        <div id="result"></div>
    ');

    $page = visit('/')->onDialog(function (Dialog $dialog) {
        expect($dialog->type())->toBe('alert');
        expect($dialog->message())->toBe('Hello World!');

        $dialog->accept();
    });

    $page->click('#alert-btn');

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
        ->onDialog(function (Dialog $dialog) {
            //
        });

    $page->click('#alert-btn');
})->throws(ExpectationFailedException::class);

it('can handle confirm dialog with acceptance', function (): void {
    Route::get('/', fn (): string => '
        <button id="confirm-btn" onclick="
            var result = confirm(\'Are you sure?\');
            document.getElementById(\'result\').textContent = result ? \'Confirmed\' : \'Cancelled\';
        ">Show Confirm</button>
        <div id="result"></div>
    ');

    $page = visit('/')->onDialog(function ($dialog) {
        expect($dialog->type())->toBe('confirm');
        expect($dialog->message())->toBe('Are you sure?');
        $dialog->accept();
    });

    $page->click('#confirm-btn');

    expect($page->text('#result'))->toBe('Confirmed');
});

it('can handle confirm dialog with dismissal', function (): void {
    Route::get('/', fn (): string => '
        <button id="confirm-btn" onclick="
            var result = confirm(\'Are you sure?\');
            document.getElementById(\'result\').textContent = result ? \'Confirmed\' : \'Cancelled\';
        ">Show Confirm</button>
        <div id="result"></div>
    ');

    $page = visit('/')->onDialog(function ($dialog) {
        expect($dialog->type())->toBe('confirm');
        expect($dialog->message())->toBe('Are you sure?');
        $dialog->dismiss();
    });

    $page->click('#confirm-btn');

    expect($page->text('#result'))->toBe('Cancelled');
});

it('can handle prompt dialog with custom input', function (): void {
    Route::get('/', fn (): string => '
        <button id="prompt-btn" onclick="
            var result = prompt(\'What is your name?\', \'Default Name\');
            document.getElementById(\'result\').textContent = result || \'No input\';
        ">Show Prompt</button>
        <div id="result"></div>
    ');

    $page = visit('/')->onDialog(function ($dialog) {
        expect($dialog->type())->toBe('prompt');
        expect($dialog->message())->toBe('What is your name?');
        expect($dialog->defaultValue())->toBe('Default Name');
        $dialog->accept('John Doe');
    });

    $page->click('#prompt-btn');

    expect($page->text('#result'))->toBe('John Doe');
});

it('can handle prompt dialog with dismissal', function (): void {
    Route::get('/', fn (): string => '
        <button id="prompt-btn" onclick="
            var result = prompt(\'What is your name?\');
            document.getElementById(\'result\').textContent = result || \'No input\';
        ">Show Prompt</button>
        <div id="result"></div>
    ');

    $page = visit('/')->onDialog(function ($dialog) {
        expect($dialog->type())->toBe('prompt');
        $dialog->dismiss();
    });

    $page->click('#prompt-btn');

    expect($page->text('#result'))->toBe('No input');
});


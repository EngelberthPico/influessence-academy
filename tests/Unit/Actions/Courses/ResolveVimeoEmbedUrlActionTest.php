<?php

use App\Actions\Courses\ResolveVimeoEmbedUrlAction;

beforeEach(function () {
    $this->action = new ResolveVimeoEmbedUrlAction;
});

test('resolves a plain numeric id', function () {
    expect($this->action->handle('1228428160'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1');
});

test('resolves a standard vimeo.com url', function () {
    expect($this->action->handle('https://vimeo.com/1228428160'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1');
});

test('resolves a manage/videos url', function () {
    expect($this->action->handle('https://vimeo.com/manage/videos/1228428160'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1');
});

test('resolves a player.vimeo.com url with a hash query param', function () {
    expect($this->action->handle('https://player.vimeo.com/video/1228428160?h=a1b2c3d4e5'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1&h=a1b2c3d4e5');
});

test('resolves an unlisted video link with id and hash in the path', function () {
    expect($this->action->handle('https://vimeo.com/1228428160/a1b2c3d4e5'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1&h=a1b2c3d4e5');
});

test('resolves a www.vimeo.com url the same as vimeo.com', function () {
    expect($this->action->handle('https://www.vimeo.com/1228428160'))
        ->toBe('https://player.vimeo.com/video/1228428160?dnt=1');
});

test('returns null for empty, blank or missing values', function (?string $value) {
    expect($this->action->handle($value))->toBeNull();
})->with([
    'empty string' => [''],
    'null' => [null],
    'only whitespace' => ['   '],
]);

test('returns null for text without a numeric id', function () {
    expect($this->action->handle('hello world'))->toBeNull();
});

test('returns null for a non-vimeo host', function () {
    expect($this->action->handle('https://evil.com/1228428160'))->toBeNull();
});

test('returns null for a javascript pseudo-url', function () {
    expect($this->action->handle('javascript:alert(1)'))->toBeNull();
});

test('returns null for a value with quotes or script tags mixed in', function () {
    expect($this->action->handle('1228428160"><script>alert(1)</script>'))->toBeNull();
});

test('never returns anything that does not start with the trusted player url', function () {
    $values = [
        '1228428160',
        'https://vimeo.com/1228428160',
        'https://vimeo.com/manage/videos/1228428160',
        'https://player.vimeo.com/video/1228428160?h=a1b2c3d4e5',
        'https://vimeo.com/1228428160/a1b2c3d4e5',
    ];

    foreach ($values as $value) {
        $result = $this->action->handle($value);

        expect($result)->not->toBeNull();
        expect($result)->toStartWith('https://player.vimeo.com/video/');
    }
});

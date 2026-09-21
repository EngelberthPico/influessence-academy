<?php

use App\Rules\ValidVimeoVideo;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

function validateVimeoValue(?string $value): ValidatorContract
{
    return Validator::make(
        ['vimeo_id' => $value],
        ['vimeo_id' => [new ValidVimeoVideo]],
    );
}

test('passes for a blank value', function (?string $value) {
    expect(validateVimeoValue($value)->passes())->toBeTrue();
})->with([null, '']);

test('passes for values the resolve action already accepts', function (string $value) {
    expect(validateVimeoValue($value)->passes())->toBeTrue();
})->with([
    'plain numeric id' => '1228428160',
    'vimeo.com url' => 'https://vimeo.com/1228428160',
    'www.vimeo.com url' => 'https://www.vimeo.com/1228428160',
    'player.vimeo.com url with hash' => 'https://player.vimeo.com/video/1228428160?h=a1b2c3d4e5',
    'unlisted link with id and hash in path' => 'https://vimeo.com/1228428160/a1b2c3d4e5',
]);

test('fails for an unrecognized value with the exact expected message', function (string $value) {
    $validator = validateVimeoValue($value);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('vimeo_id'))
        ->toBe('No reconocemos ese valor. Pega el número del video o el link de Vimeo');
})->with([
    'other host' => 'https://www.youtube.com/watch?v=1228428160',
    'text without a number' => 'esto no es un link de vimeo',
    'script tag' => '<script>alert(1)</script>',
]);

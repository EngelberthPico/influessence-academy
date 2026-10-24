<?php

test('robots.txt lists the sitemap and keeps the existing private routes disallowed', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->toContain('Sitemap: https://influessenceacademy.com/sitemap.xml')
        ->and($robots)->toContain('Disallow: /admin')
        ->and($robots)->toContain('Disallow: /mi-cuenta')
        ->and($robots)->toContain('Disallow: /settings')
        ->and($robots)->toContain('Disallow: /reservar-clase');
});

test('the sitemap route is not blocked by any disallow rule in robots.txt', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    preg_match_all('/^Disallow:\s*(\S+)/m', $robots, $matches);

    foreach ($matches[1] as $disallowedPath) {
        expect(str_starts_with('/sitemap.xml', $disallowedPath))->toBeFalse();
    }
});

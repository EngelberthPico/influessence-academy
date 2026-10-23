<?php

function navTagHtml(string $html): string
{
    preg_match('/<nav\b[^>]*>/s', $html, $matches);

    return $matches[0] ?? '';
}

test('the navbar on a non-transparent page always carries the solid background class as a static class, not through Alpine', function () {
    $response = $this->get(route('courses.index'));

    $response->assertOk();

    $navTag = navTagHtml($response->getContent());

    expect($navTag)->toContain(':data-mobile-menu-open="mobileMenuOpen"')
        ->and($navTag)->toContain('data-[mobile-menu-open]:bg-crema')
        ->and($navTag)->not->toContain("'bg-crema': mobileMenuOpen")
        ->and(preg_match('/class="[^"]*\bbg-crema\b[^"]*"/', $navTag))->toBe(1);
});

test('the home navbar starts transparent, with no static bg-crema class, but can still turn solid when the mobile menu opens', function () {
    $response = $this->get(route('home'));

    $response->assertOk();

    $navTag = navTagHtml($response->getContent());

    expect($navTag)->toContain(':data-mobile-menu-open="mobileMenuOpen"')
        ->and($navTag)->toContain('data-[mobile-menu-open]:bg-crema');

    preg_match('/class="([^"]*)"/', $navTag, $classMatch);
    $classes = explode(' ', trim($classMatch[1] ?? ''));

    expect($classes)->not->toContain('bg-crema');
});

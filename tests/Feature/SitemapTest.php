<?php

use App\Models\Course;

test('the sitemap responds with a 200 and valid xml', function () {
    $response = $this->get(route('sitemap'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');

    $xml = simplexml_load_string($response->getContent());
    expect($xml)->not->toBeFalse();
});

test('the sitemap includes a published course and excludes an unpublished one', function () {
    $published = Course::factory()->create(['title' => 'Curso Publicado', 'is_published' => true]);
    $unpublished = Course::factory()->create(['title' => 'Curso Sin Publicar', 'is_published' => false]);

    $response = $this->get(route('sitemap'));

    $response->assertOk();
    $response->assertSee(route('courses.show', $published), false);
    $response->assertDontSee($unpublished->slug);
});

test('the sitemap does not contain any private route', function () {
    $response = $this->get(route('sitemap'));

    $response->assertOk();
    $response->assertDontSee('/admin', false);
    $response->assertDontSee('/mi-cuenta', false);
    $response->assertDontSee('/carrito', false);
    $response->assertDontSee('/settings', false);
    $response->assertDontSee('/reservar-clase', false);
});

test('every url in the sitemap is absolute and uses https', function () {
    Course::factory()->create(['is_published' => true]);

    $response = $this->get(route('sitemap'));

    $response->assertOk();

    $xml = simplexml_load_string($response->getContent());
    $locations = [];
    foreach ($xml->url as $url) {
        $locations[] = (string) $url->loc;
    }

    expect($locations)->not->toBeEmpty();

    foreach ($locations as $location) {
        expect($location)->toStartWith('https://');
    }
});

<?php

test('the home no longer claims recorded courses have weekly live sessions', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('Seguimiento a tu avance');
    $response->assertDontSee('Comunidad semanal');
    $response->assertDontSee('cada semana hay sesiones en vivo de acompañamiento');
});

test('the home shows the updated como funciona and faq texts', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Aprende a tu ritmo');
    $response->assertSee('Los cursos grabados los ves cuando quieras y las veces que necesites, organizados por módulos');
    $response->assertSee('Agenda tu clase en vivo');
    $response->assertSee('Si tu curso incluye asesoría, o es un programa en vivo, agendas tu clase con Fabiola desde tu cuenta');
    $response->assertSee('Depende del curso. Los cursos grabados los ves a tu ritmo y no incluyen sesiones en vivo. Los programas en vivo son con Fabiola, y algunos cursos grabados cierran con una asesoría con ella');
});

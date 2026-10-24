<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(): Response
    {
        URL::forceScheme('https');

        $courses = Course::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->get();

        // La declaración XML se antepone aquí, en PHP puro, en vez de en la vista:
        // escribirla literal en un .blade.php hace que PHP la confunda con una
        // etiqueta de apertura corta ("<?") al compilar la vista.
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".view('sitemap', ['courses' => $courses])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

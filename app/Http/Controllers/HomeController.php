<?php

namespace App\Http\Controllers;

use App\Actions\Courses\GetBestSellerCoursesAction;

class HomeController extends Controller
{
    public function index(GetBestSellerCoursesAction $action)
    {
        return view('welcome', [
            'bestSellerCourses' => $action->handle(),
        ]);
    }
}

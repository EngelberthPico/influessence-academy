<?php

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Support\Collection;

class GetBestSellerCoursesAction
{
    /**
     * @return Collection<int, Course>
     */
    public function handle(): Collection
    {
        return Course::query()
            ->where('is_published', true)
            ->where('is_best_seller', true)
            ->orderBy('price_cents')
            ->orderBy('id')
            ->get();
    }
}

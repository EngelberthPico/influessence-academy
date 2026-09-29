<?php

namespace App\Actions\Cart;

use App\Models\Course;
use Illuminate\Support\Collection;

class GetCartCoursesAction
{
    /**
     * @return Collection<int, Course>
     */
    public function handle(): Collection
    {
        $courseIds = session('cart', []);

        if ($courseIds === []) {
            return collect();
        }

        return Course::query()->whereIn('id', $courseIds)->get();
    }
}

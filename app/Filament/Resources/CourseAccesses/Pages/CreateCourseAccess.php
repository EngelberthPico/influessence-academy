<?php

namespace App\Filament\Resources\CourseAccesses\Pages;

use App\Filament\Resources\CourseAccesses\CourseAccessResource;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseAccess extends CreateRecord
{
    protected static string $resource = CourseAccessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['granted_at'] = now();

        $redemptionWindowDays = Course::find($data['course_id'])?->redemption_window_days;

        $data['advisory_redeemable_until'] = $redemptionWindowDays !== null
            ? now()->addDays($redemptionWindowDays)
            : null;

        return $data;
    }
}

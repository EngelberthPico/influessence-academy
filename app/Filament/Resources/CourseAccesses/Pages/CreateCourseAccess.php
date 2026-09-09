<?php

namespace App\Filament\Resources\CourseAccesses\Pages;

use App\Filament\Resources\CourseAccesses\CourseAccessResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseAccess extends CreateRecord
{
    protected static string $resource = CourseAccessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['granted_at'] = now();

        return $data;
    }
}

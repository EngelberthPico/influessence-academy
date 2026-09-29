<?php

namespace App\Filament\Resources\CourseAccesses\Pages;

use App\Actions\Courses\GrantCourseAccessAction;
use App\Filament\Resources\CourseAccesses\CourseAccessResource;
use App\Models\Course;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCourseAccess extends CreateRecord
{
    protected static string $resource = CourseAccessResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(GrantCourseAccessAction::class)->handle(
            user: User::findOrFail($data['user_id']),
            course: Course::findOrFail($data['course_id']),
        );
    }
}

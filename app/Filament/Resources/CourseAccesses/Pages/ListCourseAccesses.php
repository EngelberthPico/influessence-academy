<?php

namespace App\Filament\Resources\CourseAccesses\Pages;

use App\Filament\Resources\CourseAccesses\CourseAccessResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseAccesses extends ListRecords
{
    protected static string $resource = CourseAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\CourseAccesses;

use App\Filament\Resources\CourseAccesses\Pages\CreateCourseAccess;
use App\Filament\Resources\CourseAccesses\Pages\ListCourseAccesses;
use App\Filament\Resources\CourseAccesses\Schemas\CourseAccessForm;
use App\Filament\Resources\CourseAccesses\Tables\CourseAccessesTable;
use App\Models\CourseAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseAccessResource extends Resource
{
    protected static ?string $model = CourseAccess::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'acceso';

    protected static ?string $pluralModelLabel = 'accesos';

    public static function form(Schema $schema): Schema
    {
        return CourseAccessForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseAccessesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'course']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseAccesses::route('/'),
            'create' => CreateCourseAccess::route('/create'),
        ];
    }
}

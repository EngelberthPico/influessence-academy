<?php

namespace App\Filament\Resources\CourseAccesses\Tables;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseAccessesTable
{
    public static function configure(Table $table): Table
    {
        $byPerson = Group::make('user_id')
            ->label('Persona')
            ->collapsible()
            ->getTitleFromRecordUsing(fn (CourseAccess $record): string => "{$record->user->name} · {$record->user->email}")
            ->orderQueryUsing(fn (Builder $query, string $direction): Builder => $query->orderBy(
                User::select('name')->whereColumn('users.id', 'course_accesses.user_id'),
                $direction,
            ));

        $byCourse = Group::make('course_id')
            ->label('Curso')
            ->collapsible()
            ->getTitleFromRecordUsing(fn (CourseAccess $record): string => $record->course->title)
            ->orderQueryUsing(fn (Builder $query, string $direction): Builder => $query->orderBy(
                Course::select('title')->whereColumn('courses.id', 'course_accesses.course_id'),
                $direction,
            ));

        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Persona')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label('Curso')
                    ->searchable(),

                TextColumn::make('granted_at')
                    ->label('Otorgado el')
                    ->dateTime(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->getStateUsing(fn (CourseAccess $record) => $record->revoked_at === null),

                TextColumn::make('advisory_status')
                    ->label('Estado de asesoría')
                    ->badge()
                    ->placeholder('No aplica')
                    ->getStateUsing(function (CourseAccess $record) {
                        if ($record->course?->redemption_window_days === null) {
                            return null;
                        }

                        if ($record->advisory_expired_at !== null) {
                            return 'Vencida';
                        }

                        return 'Vigente hasta '.$record->advisory_redeemable_until?->format('d/m/Y');
                    })
                    ->color(fn (CourseAccess $record) => $record->advisory_expired_at !== null ? 'danger' : 'success'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('course'))
            ->groups([$byPerson, $byCourse])
            ->defaultGroup($byPerson)
            ->defaultSort('granted_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Persona')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label('Revocar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CourseAccess $record) => $record->revoked_at === null)
                    ->action(fn (CourseAccess $record) => $record->update(['revoked_at' => now()])),
            ]);
    }
}

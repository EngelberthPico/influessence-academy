<?php

namespace App\Filament\Resources\CourseAccesses\Tables;

use App\Models\CourseAccess;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseAccessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
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
            ->filters([
                //
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

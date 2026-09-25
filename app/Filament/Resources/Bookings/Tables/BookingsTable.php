<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label('Curso')
                    ->placeholder('Clase general'),

                TextColumn::make('scheduled_at')
                    ->label('Fecha de la clase')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Agendada el')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('scheduled_at')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(BookingStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('complete')
                    ->label('Marcar completada')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record) => $record->status === BookingStatus::Scheduled)
                    ->action(fn (Booking $record) => $record->update(['status' => BookingStatus::Completed])),

                Action::make('cancel')
                    ->label('Cancelar reserva')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record) => $record->status === BookingStatus::Scheduled)
                    ->action(fn (Booking $record) => $record->update(['status' => BookingStatus::Canceled])),
            ]);
    }
}

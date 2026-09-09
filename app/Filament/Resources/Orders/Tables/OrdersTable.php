<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('total_cents')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$'.number_format($state / 100, 2)),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                TextColumn::make('paid_at')
                    ->label('Pagada el')
                    ->date(),

                TextColumn::make('created_at')
                    ->label('Creada el')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}

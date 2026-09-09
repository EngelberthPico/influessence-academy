<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required(),

                RichEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),

                TextInput::make('price_cents')
                    ->label('Precio (USD)')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => (int) round(((float) $state) * 100))
                    ->afterStateHydrated(fn ($component, $state) => $component->state($state !== null ? $state / 100 : null)),

                TextInput::make('vimeo_id')
                    ->label('ID o link del video en Vimeo'),

                Toggle::make('is_published')
                    ->label('Publicado'),
            ]);
    }
}

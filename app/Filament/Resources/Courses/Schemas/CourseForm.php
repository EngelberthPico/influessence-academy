<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Actions\Courses\ResolveVimeoEmbedUrlAction;
use App\Enums\CourseType;
use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->label('ID o link del video en Vimeo')
                    ->helperText('Pega el número del video o el link de Vimeo')
                    ->rule(function () {
                        return function (string $attribute, $value, Closure $fail) {
                            if (blank($value)) {
                                return;
                            }

                            if (app(ResolveVimeoEmbedUrlAction::class)->handle($value) === null) {
                                $fail('No reconocemos ese valor. Pega el número del video o el link de Vimeo');
                            }
                        };
                    }),

                Select::make('type')
                    ->label('Tipo')
                    ->options(CourseType::class)
                    ->default(CourseType::Recorded->value)
                    ->required()
                    ->live(),

                TextInput::make('session_count')
                    ->label('Número de sesiones')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => in_array($get->enum('type', CourseType::class), [CourseType::LiveProgram, CourseType::Hybrid])),

                TextInput::make('duration_months')
                    ->label('Duración (meses)')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => in_array($get->enum('type', CourseType::class), [CourseType::LiveProgram, CourseType::Hybrid])),

                TextInput::make('redemption_window_days')
                    ->label('Días para redimir la asesoría')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => $get->enum('type', CourseType::class) === CourseType::Hybrid),

                Toggle::make('is_published')
                    ->label('Publicado'),
            ]);
    }
}

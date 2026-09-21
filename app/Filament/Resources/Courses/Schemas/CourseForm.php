<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\CourseType;
use App\Rules\ValidVimeoVideo;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                    ->label('Video de bienvenida (opcional)')
                    ->helperText('Se muestra arriba del curso, antes de los módulos. Pega el número del video o el link de Vimeo')
                    ->rule(new ValidVimeoVideo),

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

                Toggle::make('is_best_seller')
                    ->label('Más vendido')
                    ->helperText('Los cursos marcados aparecen en la página de inicio, siempre que estén publicados. Te recomendamos marcar entre 3 y 6'),

                Section::make('Contenido del curso')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get->enum('type', CourseType::class), [CourseType::Recorded, CourseType::Hybrid]))
                    ->schema([
                        Repeater::make('modules')
                            ->label('Módulos')
                            ->relationship('modules')
                            ->orderColumn('position')
                            ->collapsible()
                            ->cloneable(false)
                            ->addActionLabel('Agregar módulo')
                            ->itemLabel(fn (array $state): string => filled($state['title'] ?? null) ? $state['title'] : 'Módulo nuevo')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título del módulo')
                                    ->required(),

                                Textarea::make('description')
                                    ->label('Descripción (opcional)')
                                    ->rows(2),

                                Repeater::make('lessons')
                                    ->label('Videos')
                                    ->relationship('lessons')
                                    ->orderColumn('position')
                                    ->collapsible()
                                    ->cloneable(false)
                                    ->minItems(0)
                                    ->addActionLabel('Agregar video')
                                    ->itemLabel(fn (array $state): string => filled($state['title'] ?? null) ? $state['title'] : 'Video nuevo')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título del video')
                                            ->required(),

                                        TextInput::make('vimeo_id')
                                            ->label('ID o link del video en Vimeo')
                                            ->helperText('Pega el número del video o el link de Vimeo')
                                            ->required()
                                            ->rule(new ValidVimeoVideo),

                                        Textarea::make('description')
                                            ->label('Descripción (opcional)')
                                            ->rows(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

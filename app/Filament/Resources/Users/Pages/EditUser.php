<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    /**
     * 'role' no es asignable en masa en el modelo User (para que ningún otro
     * punto del código lo pueda cambiar sin querer), así que aquí se asigna
     * explícitamente en vez de dejar que fill() lo descarte en silencio.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill(Arr::except($data, ['role']));
        $record->role = $data['role'];
        $record->save();

        return $record;
    }
}

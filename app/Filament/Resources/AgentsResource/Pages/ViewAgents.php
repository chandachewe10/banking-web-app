<?php

namespace App\Filament\Resources\AgentsResource\Pages;

use App\Filament\Resources\AgentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgents extends ViewRecord
{
    protected static string $resource = AgentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

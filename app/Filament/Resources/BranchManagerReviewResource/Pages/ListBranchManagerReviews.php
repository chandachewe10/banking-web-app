<?php

namespace App\Filament\Resources\BranchManagerReviewResource\Pages;

use App\Filament\Resources\BranchManagerReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchManagerReviews extends ListRecords
{
    protected static string $resource = BranchManagerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\HeadCreditReviewResource\Pages;

use App\Filament\Resources\HeadCreditReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeadCreditReviews extends ListRecords
{
    protected static string $resource = HeadCreditReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

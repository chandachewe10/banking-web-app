<?php

namespace App\Filament\Resources\HeadCreditReviewResource\Pages;

use App\Filament\Resources\HeadCreditReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeadCreditReview extends EditRecord
{
    protected static string $resource = HeadCreditReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

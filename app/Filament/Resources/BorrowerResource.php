<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowerResource\Pages;
use App\Filament\Resources\BorrowerResource\RelationManagers;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\ActionSize;
use App\Models\Borrower;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class BorrowerResource extends Resource
{
    protected static ?string $model = Borrower::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Borrower Evaluation';
    protected static ?string $modelLabel = 'Borrower Evaluation';
    protected static ?string $recordTitleAttribute = 'Borrower Evaluation';
    protected static ?string $title = 'Borrower Evaluation';
    protected static ?string $navigationGroup = 'Credit Module';
    protected static ?int $sort = 1;






    public static function infolist(Infolist $infolist): Infolist
    {


        $borrower = $infolist->getRecord();

        return $infolist
            ->schema([
                Section::make('Personal Details')
                    ->description('Borrower Personal Details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('first_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('last_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('gender')
                                    ->icon('heroicon-o-sparkles'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date('j F Y')
                                    ->icon('heroicon-o-cake'),
                                TextEntry::make('occupation')
                                    ->icon('heroicon-o-briefcase'),
                                TextEntry::make('identification')
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('mobile')
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('address')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),
                                TextEntry::make('city')
                                   ->icon('heroicon-o-credit-card'),
                                TextEntry::make('province')
                                    ->icon('heroicon-o-map'),
                                TextEntry::make('zipcode')
                                    ->icon('heroicon-o-tag'),
                            ]),
                    ]),

                // Section::make('Next of Kin Details')
                //     ->description('Borrower Next Of Kin Details')
                //     ->icon('heroicon-o-user-group')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextEntry::make('next_of_kin_first_name')
                //                     ->icon('heroicon-o-user'),
                //                 TextEntry::make('next_of_kin_last_name')
                //                     ->icon('heroicon-o-user'),
                //                 TextEntry::make('phone_next_of_kin')
                //                     ->icon('heroicon-o-phone'),
                //                 TextEntry::make('address_next_of_kin')
                //                     ->icon('heroicon-o-map-pin'),
                //                 TextEntry::make('relationship_next_of_kin')
                //                     ->icon('heroicon-o-heart')
                //                     ->columnSpanFull(),
                //             ]),
                //     ]),

                Section::make('Bank Details')
                    ->description('Borrower Bank Details')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('bank_name')
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('bank_branch')
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('bank_sort_code')
                                    ->icon('heroicon-o-banknotes'),
                                TextEntry::make('bank_account_number')
                                    ->icon('heroicon-o-credit-card'),
                                TextEntry::make('bank_account_name')
                                    ->icon('heroicon-o-user'),

                            ]),
                    ]),



                Section::make('Attached Files')
                    ->schema([
                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('id_front')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download NRC Front')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View NRC Front')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('id_back')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download NRC Back')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View NRC Back')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('selfie')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Selfie')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Selfie')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('bank_statement')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Bank Statement')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Bank Statement')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('payslip')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Payslip')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Payslips')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('signatureUri')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Signature')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Signature')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                    ]),
            ]);
    }


    protected static function getFileDisplaySchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    IconEntry::make('file_icon')
                        ->icon(fn($state, $record) => self::getFileIcon($record['extension']))
                        ->size(IconEntry\IconEntrySize::Large)
                        ->color('primary'),

                    TextEntry::make('file_name')
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('file_size')
                        ->color('gray')
                        ->formatStateUsing(fn($state) => self::formatFileSize($state)),
                ]),

            Actions::make([
                Action::make('view_file')
                    ->label('View File')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => $record['url'])
                    ->openUrlInNewTab()
                    ->size(ActionSize::Small),

                Action::make('download_file')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record['url'] . '?download=1')
                    ->size(ActionSize::Small),
            ])->verticalAlignment('center'),
        ];
    }
    // Helper method to get appropriate file icons
    protected static function getFileIcon(string $extension): string
    {
        $iconMap = [
            'pdf' => 'heroicon-o-document-text',
            'doc' => 'heroicon-o-document',
            'docx' => 'heroicon-o-document',
            'xls' => 'heroicon-o-table-cells',
            'xlsx' => 'heroicon-o-table-cells',
            'jpg' => 'heroicon-o-photo',
            'jpeg' => 'heroicon-o-photo',
            'png' => 'heroicon-o-photo',
            'gif' => 'heroicon-o-photo',
            'txt' => 'heroicon-o-document',
            'zip' => 'heroicon-o-archive-box',
            'rar' => 'heroicon-o-archive-box',
        ];

        return $iconMap[strtolower($extension)] ?? 'heroicon-o-document';
    }

    // Helper method to format file size
    protected static function formatFileSize(int $size): string
    {
        if ($size == 0) return '0 Bytes';

        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($size, 1024));

        return round($size / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zipcode')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('next_of_kin_first_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('next_of_kin_last_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('phone_next_of_kin')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('address_next_of_kin')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('relationship_next_of_kin')
                //    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_branch')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_sort_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_account_name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('mobile_money_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('mobile_money_number')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrowers::route('/'),
            'create' => Pages\CreateBorrower::route('/create'),
            'view' => Pages\ViewBorrower::route('/{record}'),
            'edit' => Pages\EditBorrower::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowerResource\Pages;
use App\Filament\Resources\BorrowerResource\RelationManagers;
use App\Models\Borrower;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowerResource extends Resource
{
    protected static ?string $model = Borrower::class;

     protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Credit Evaluation';
    protected static ?string $modelLabel = 'Credit Evaluation';
    protected static ?string $recordTitleAttribute = 'Credit Evaluation';
    protected static ?string $title = 'Credit Evaluation';
    protected static ?string $navigationGroup = 'Credit Module';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('gender')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('dob')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('occupation')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('identification')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mobile')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('province')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('zipcode')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('next_of_kin_first_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('next_of_kin_last_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('phone_next_of_kin')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('address_next_of_kin')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('relationship_next_of_kin')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bank_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bank_branch')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bank_sort_code')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bank_account_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bank_account_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mobile_money_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mobile_money_number')
                    ->maxLength(255)
                    ->default(null),
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
                Tables\Columns\TextColumn::make('next_of_kin_first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('next_of_kin_last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_next_of_kin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_next_of_kin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('relationship_next_of_kin')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('mobile_money_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_money_number')
                    ->searchable(),
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
                Tables\Actions\EditAction::make(),
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

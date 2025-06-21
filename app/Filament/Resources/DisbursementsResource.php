<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisbursementsResource\Pages;
use App\Filament\Resources\DisbursementsResource\RelationManagers;
use App\Models\Disbursements;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DisbursementsResource extends Resource
{
    protected static ?string $model = Disbursements::class;

   protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Disbursements';
    protected static ?string $modelLabel = 'Disbursements';
    protected static ?string $recordTitleAttribute = 'Disbursements';
    protected static ?string $title = 'Disbursements';
    protected static ?string $navigationGroup = 'Finance Module';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('method')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('disbursed_at')
                    ->required(),
                Forms\Components\Toggle::make('authorized')
                    ->required(),
                Forms\Components\TextInput::make('authorized_by')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('disbursed_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('authorized')
                    ->boolean(),
                Tables\Columns\TextColumn::make('authorized_by')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDisbursements::route('/'),
            'create' => Pages\CreateDisbursements::route('/create'),
            'view' => Pages\ViewDisbursements::route('/{record}'),
            'edit' => Pages\EditDisbursements::route('/{record}/edit'),
        ];
    }
}

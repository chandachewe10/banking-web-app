<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanAgreementFormsResource\Pages;
use App\Filament\Resources\LoanAgreementFormsResource\RelationManagers;
use App\Models\LoanAgreementForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanAgreementFormsResource extends Resource
{
    protected static ?string $model = LoanAgreementForms::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Agreement Forms';
    protected static ?string $modelLabel = 'Agreement Forms';
    protected static ?string $recordTitleAttribute = 'Agreement Forms';
    protected static ?string $title = 'Agreement Forms';
    protected static ?string $navigationGroup = 'Finance Module';    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_type_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('loan_agreement_text')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('activate_loan_agreement_form')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_type_id')
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
                Tables\Columns\TextColumn::make('activate_loan_agreement_form')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListLoanAgreementForms::route('/'),
            'create' => Pages\CreateLoanAgreementForms::route('/create'),
            'view' => Pages\ViewLoanAgreementForms::route('/{record}'),
            'edit' => Pages\EditLoanAgreementForms::route('/{record}/edit'),
        ];
    }
}

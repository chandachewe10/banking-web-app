<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhysicalSigningEvaluationResource\Pages;
use App\Filament\Resources\PhysicalSigningEvaluationResource\RelationManagers;
use App\Models\PhysicalSigningEvaluation as Agents;
use App\Policies\PhysicalSigningEvaluationPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Set;
use Filament\Forms\Components\Hidden;



class PhysicalSigningEvaluationResource extends Resource
{
    protected static ?string $model = Agents::class;
    protected static ?string $policy = PhysicalSigningEvaluationPolicy::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Physical signing verification';
    protected static ?string $modelLabel = 'Physical signing verification';
    protected static ?string $recordTitleAttribute = 'Physical signing verification';
    protected static ?string $title = 'Physical signing verification';
    protected static ?string $navigationGroup = 'Branch Operations';

    public static function form(Form $form): Form
    {
       return $form
    ->schema([
        Forms\Components\TextInput::make('loan_number')
            ->live(onBlur:true)
            ->helperText('Click outside the field to check whether the Loan has been approved or Not')
            ->required()
            ->afterStateUpdated(function ($state, Set $set) {
                if ($state) {
                    $approvalStatus = \App\Models\Loans::where('loan_number', $state)->first();
                    if ($approvalStatus) {
                        $set('physical_verification', $approvalStatus->loan_status == 'approved' ? 'Loan has been approved' : 'Loan Not-Found');
                    } else {
                        $set('physical_verification', 'Not-Found');
                    }
                }
            }),

        Forms\Components\TextInput::make('physical_verification')
            ->required()
             ->readOnly()
            ->maxLength(255),



        SpatieMediaLibraryFileUpload::make('contract')
            ->disk('borrowers')
            ->collection('contracts')
            ->visibility('public')
            ->required()
            ->multiple()
            ->minFiles(0)
            ->maxFiles(10)
            ->maxSize(5120)
            ->columnSpan(2)
            ->openable(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('physical_verification')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
               //Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPhysicalSigningEvaluations::route('/'),
            'create' => Pages\CreatePhysicalSigningEvaluation::route('/create'),
            'view' => Pages\ViewPhysicalSigningEvaluation::route('/{record}'),
            'edit' => Pages\EditPhysicalSigningEvaluation::route('/{record}/edit'),
        ];
    }
}

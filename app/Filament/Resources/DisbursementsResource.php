<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisbursementsResource\Pages;
use App\Filament\Resources\DisbursementsResource\RelationManagers;
use App\Models\Disbursements;
use App\Policies\DisbursementsPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Hidden;
use App\helpers\CreateLinks;

class DisbursementsResource extends Resource
{
    protected static ?string $model = Disbursements::class;
    protected static ?string $policy = DisbursementsPolicy::class;
    protected static ?int $sort = 2;
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
                Forms\Components\Select::make('borrower_id')
                    ->prefixIcon('heroicon-o-user')
                    ->relationship('borrower', 'first_name')
                    ->preload()
                    ->disabled()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('loan_status')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('principal_amount')
                    ->required()
                    ->disabled()
                    ->numeric(),
                Forms\Components\TextInput::make('loan_release_date')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('loan_duration')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('duration_period')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_reference')
                    ->maxLength(255)
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('loan_purpose')
                    ->maxLength(255)
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('interest_rate')
                    ->numeric()
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('interest_amount')
                    ->numeric()
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('processing_fee')
                    ->numeric()
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('arrangement_fee')
                    ->numeric()
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('insurance_fee')
                    ->numeric()
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('case_number')
                    ->maxLength(255)
                    ->disabled()
                    ->default(null),
                Forms\Components\TextInput::make('total_repayment')
                    ->numeric()
                    ->disabled()
                    ->columnSpan(2)
                    ->default(null),

                Forms\Components\Section::make("For the Official Use")
                    ->schema([

                        Forms\Components\Select::make('crb_scoring')
                            ->label('CRB Score')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->disabled()
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',

                            ]),
                        Forms\Components\Select::make('employer_verification')
                            ->label('Employer Verification')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->disabled()
                            ->options([
                                'Valid Employee' => 'Valid Employee',
                                'Former Employee' => 'Former Employee',
                                'Imposter' => 'Imposter',

                            ]),

                        Forms\Components\RichEditor::make('due_diligence')
                            ->label('Due Diligence Report')
                            ->required()
                            ->disabled()
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('comments')
                            ->helperText('Write in not more than 160 characters')
                            ->minLength(2)
                            ->maxLength(160)
                            ->rows(5)
                            ->disabled()
                            ->columnSpan(2),
                        Forms\Components\RichEditor::make('credit_appraisal_report')
                            ->label('Credit Appraisal Report')
                            ->required()
                            ->disabled()
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->columnSpan(2),

                        Forms\Components\Select::make('is_approved_on_step_one')
                            ->label('Decision by Credit Officer')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->disabled()
                            ->columnSpan(2)
                            ->options([

                                0 => 'Reject',
                                1 => 'Approve',
                            ]),
                        Forms\Components\Select::make('is_approved_on_step_two')
                            ->label('Decision by Head Credit Officer')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->disabled()
                            ->columnSpan(2)
                            ->options([

                                0 => 'Reject',
                                1 => 'Approve',
                            ]),

                        Forms\Components\Select::make('is_approved_on_step_three')
                            ->label('Decision by Branch Manager')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->disabled()
                            ->columnSpan(2)
                            ->options([

                                0 => 'Reject',
                                1 => 'Approve',
                            ]),
                        Forms\Components\Select::make('verified_by')
                            ->prefixIcon('heroicon-o-user')
                            ->relationship('verifiedBy', 'name')
                            ->preload()
                            ->disabled()
                            ->searchable()
                            ->columnSpan(2)
                            ->required(),

                        Forms\Components\Select::make('is_approved_on_step_four')
                            ->label('Disbursement Authorization')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->columnSpan(2)
                            ->options([

                                0 => 'Reject',
                                1 => 'Approved',
                            ]),

                        Hidden::make('loan_status')
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        $create_link = new CreateLinks();
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('borrower.first_name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('loan_status')
                    ->badge()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('loan_number')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loan_release_date')
                    ->date('j F Y')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_duration')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_approved_on_step_one')
                    ->label('Credit Officer Approval')
                    ->badge()
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
                    ->searchable(),


                Tables\Columns\TextColumn::make('is_approved_on_step_two')
                    ->badge()
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
                    ->label('Head Credit Officer Approval')
                    ->searchable(),


                Tables\Columns\TextColumn::make('is_approved_on_step_three')
                    ->label('Branch Manager Approval')
                    ->badge()
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
                    ->searchable(),


                Tables\Columns\TextColumn::make('is_approved_on_step_four')
                    ->label('Finance Approval')
                    ->badge()
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
                    ->searchable(),

                    Tables\Columns\TextColumn::make('loan_agreement_file_path')
                    ->label('Loan Agreement Form')
                    ->formatStateUsing(

                        fn(string $state) => $create_link::goTo(env('APP_URL') . '/' . $state, 'download', 'loan agreement form'),
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('loan_purpose')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('interest_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processing_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrangement_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('case_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_repayment')
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
            'index' => Pages\ListDisbursements::route('/'),
            'create' => Pages\CreateDisbursements::route('/create'),
            'view' => Pages\ViewDisbursements::route('/{record}'),
            'edit' => Pages\EditDisbursements::route('/{record}/edit'),
        ];
    }
}

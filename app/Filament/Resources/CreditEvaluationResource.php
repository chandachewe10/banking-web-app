<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditEvaluationResource\Pages;
use App\Models\CreditEvaluation as Loans;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CreditEvaluationResource extends Resource
{
    protected static ?string $model = Loans::class;

    protected static ?string $navigationIcon        = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel       = 'Credit Evaluation';
    protected static ?string $modelLabel            = 'Credit Evaluation';
    protected static ?string $recordTitleAttribute  = 'Credit Evaluation';
    protected static ?string $title                 = 'Credit Evaluation';
    protected static ?string $navigationGroup       = 'Credit Module';
    protected static ?int    $sort                  = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('case_number', auth()->user()->case_number)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ── Borrower & Status ─────────────────────────────────────
                Forms\Components\Section::make('Loan Overview')
                    ->columns(2)
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
                            ->disabled(),

                        Forms\Components\TextInput::make('loan_purpose')
                            ->disabled()
                            ->default(null),

                        Forms\Components\TextInput::make('loan_release_date')
                            ->required()
                            ->required(),

                        Forms\Components\TextInput::make('loan_duration')
                            ->required()
                            ->required()
                            ->suffix('Months'),

                        Forms\Components\TextInput::make('duration_period')
                            ->required()
                            ->disabled()
                            ->suffix('Months'),

                        Forms\Components\TextInput::make('transaction_reference')
                            ->disabled()
                            ->default(null),

                        Forms\Components\TextInput::make('case_number')
                            ->disabled()
                            ->default(null),
                    ]),

                // ── Loan Amounts ──────────────────────────────────────────
                Forms\Components\Section::make('Loan Amounts')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('principal_amount')
                            ->label('Total Loan Amount (K)')
                            ->required()
                            ->required()
                            ->numeric()
                            ->prefix('K'),

                        Forms\Components\TextInput::make('disbursed_amount')
                            ->label('Disbursed Amount (K)')
                            ->required()
                            ->numeric()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('interest_rate')
                            ->label('Interest Rate (% p.a.)')
                            ->numeric()
                            ->required()
                            ->suffix('%')
                            ->default(null),

                        Forms\Components\TextInput::make('interest_amount')
                            ->label('Total Interest Amount (K)')
                            ->numeric()
                            ->required()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('monthly_repayment')
                            ->label('Monthly Repayment (K)')
                            ->numeric()
                            ->required()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('total_repayment')
                            ->label('Total Repayable (K)')
                            ->numeric()
                            ->required()
                            ->prefix('K')
                            ->default(null),
                    ]),

                // ── Upfront Deduction Fees ────────────────────────────────
                Forms\Components\Section::make('Upfront Deduction Fees')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('arrangement_fee')
                            ->label('Arrangement Fee (K) — 4%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('processing_fee')
                            ->label('Processing Fee (K) — 2.5%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('credit_life_fee')
                            ->label('Credit Life Insurance (K) — 4.5%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('insurance_levy')
                            ->label('Insurance Levy (K) — Fixed K150')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('credit_reference_fee')
                            ->label('Credit Reference Bureau Fee (K) — Fixed K50')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('collateral_fee')
                            ->label('Collateral Appraisal Fee (K) — 1%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('documentation_fee')
                            ->label('Documentation Fee (K) — 0.5%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),

                        Forms\Components\TextInput::make('admin_fee_per_month')
                            ->label('Admin Fee / Month (K) — 0.5%')
                            ->numeric()
                            ->disabled()
                            ->prefix('K')
                            ->default(null),
                    ]),

                // ── Official Use ──────────────────────────────────────────
                Forms\Components\Section::make('For Official Use Only')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('crb_scoring')
                            ->label('CRB Score')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
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
                            ->options([
                                'Valid Employee'   => 'Valid Employee',
                                'Former Employee'  => 'Former Employee',
                                'Imposter'         => 'Imposter',
                            ]),

                        Forms\Components\RichEditor::make('due_diligence')
                            ->label('Due Diligence Report')
                            ->required()
                            ->disableToolbarButtons(['attachFiles', 'codeBlock'])
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('comments')
                            ->helperText('Write in not more than 160 characters')
                            ->minLength(2)
                            ->maxLength(160)
                            ->rows(5)
                            ->columnSpan(2),

                        Forms\Components\RichEditor::make('credit_appraisal_report')
                            ->label('Credit Appraisal Report')
                            ->required()
                            ->disableToolbarButtons(['attachFiles', 'codeBlock'])
                            ->columnSpan(2),

                        Forms\Components\Select::make('is_approved_on_step_one')
                            ->label('Your Decision')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->columnSpan(2)
                            ->options([
                                0 => 'Reject',
                                1 => 'Approve',
                            ]),
                    ]),





                    Forms\Components\Section::make('Documents & Attachments')
                    ->description('Upload all required documents for this loan application')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('crb_report')
                            ->label('CRB Report')
                            ->disk('borrowers')
                            ->collection('crb_report')
                            ->visibility('public')
                            ->minFiles(0)
                            ->maxFiles(1)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload CRB report (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('collateral_report')
                            ->label('Collateral Report')
                            ->disk('borrowers')
                            ->collection('collateral_report')
                            ->visibility('public')
                            ->multiple()
                            ->minFiles(0)
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload collateral report (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('employer_verification_report')
                            ->label('Employer Verification Report')
                            ->disk('borrowers')
                            ->collection('employer_verification_report')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload employer verification report (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('any_other_documents')
                            ->label('Any Other Documents')
                            ->disk('borrowers')
                            ->collection('any_other_documents')
                            ->visibility('public')
                            ->minFiles(0)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload any other documents if available (max 5MB)'),
                        
                        




                    ]), 
                ]);        
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return static::getModel()::query()
                    ->where('case_number', auth()->user()->case_number);
            })
            ->columns([
                Tables\Columns\TextColumn::make('borrower.first_name')
                    ->label('Borrower')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('loan_status')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('loan_purpose')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Loan Amount (K)')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('disbursed_amount')
                    ->label('Disbursed (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('monthly_repayment')
                    ->label('Monthly Repayment (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_repayment')
                    ->label('Total Repayable (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('interest_rate')
                    ->label('Rate (% p.a.)')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('interest_amount')
                    ->label('Total Interest (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('arrangement_fee')
                    ->label('Arrangement Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('processing_fee')
                    ->label('Processing Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('credit_life_fee')
                    ->label('Credit Life Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('insurance_levy')
                    ->label('Insurance Levy (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('credit_reference_fee')
                    ->label('CRB Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('collateral_fee')
                    ->label('Collateral Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('documentation_fee')
                    ->label('Documentation Fee (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('admin_fee_per_month')
                    ->label('Admin Fee/Month (K)')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('loan_duration')
                    ->label('Tenure (Months)')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('loan_release_date')
                    ->date('j F Y')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('case_number')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('is_approved_on_step_one')
                    ->label('Credit Officer')
                    ->badge()
                    ->colors(['success' => 1, 'danger' => 0])
                    ->formatStateUsing(fn ($state) => $state ? 'Approved' : 'Pending'),

                Tables\Columns\TextColumn::make('is_approved_on_step_two')
                    ->label('Head Credit Officer')
                    ->badge()
                    ->colors(['success' => 1, 'danger' => 0])
                    ->formatStateUsing(fn ($state) => $state ? 'Approved' : 'Pending'),

                Tables\Columns\TextColumn::make('is_approved_on_step_three')
                    ->label('Branch Manager')
                    ->badge()
                    ->colors(['success' => 1, 'danger' => 0])
                    ->formatStateUsing(fn ($state) => $state ? 'Approved' : 'Pending'),

                Tables\Columns\TextColumn::make('is_approved_on_step_four')
                    ->label('Finance')
                    ->badge()
                    ->colors(['success' => 1, 'danger' => 0])
                    ->formatStateUsing(fn ($state) => $state ? 'Approved' : 'Pending'),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCreditEvaluation::route('/'),
            'create' => Pages\CreateCreditEvaluation::route('/create'),
            'view'   => Pages\ViewCreditEvaluation::route('/{record}'),
            'edit'   => Pages\EditCreditEvaluation::route('/{record}/edit'),
        ];
    }
}
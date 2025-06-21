<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentsResource\Pages;
use App\Filament\Resources\AgentsResource\RelationManagers;
use App\Models\Agents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentsResource extends Resource
{
    protected static ?string $model = Agents::class;

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('national_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('branch_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cases_handled')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('documents_collected')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('performance_score')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('active')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_login_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cases_handled')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documents_collected')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performance_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
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
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgents::route('/create'),
            'view' => Pages\ViewAgents::route('/{record}'),
            'edit' => Pages\EditAgents::route('/{record}/edit'),
        ];
    }
}

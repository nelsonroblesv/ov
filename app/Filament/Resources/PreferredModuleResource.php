<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreferredModuleResource\Pages;
use App\Filament\Resources\PreferredModuleResource\RelationManagers;
use App\Models\PreferredModule;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreferredModuleResource extends Resource
{
    protected static ?string $model = PreferredModule::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Preferred';
    protected static ?string $navigationLabel = 'Modulos';
    protected static ?string $breadcrumb = "Modulos";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del modulo')
                    ->schema([
                        TextInput::make('module_name')
                            ->label('Nombre')
                            ->required()
                            ->placeholder('Nombre del modulo'),

                        TextInput::make('module_cost')
                            ->label('Costo')
                            ->disabled(),
                        //->required()
                        //->placeholder('Cos del modulo'),

                        MarkdownEditor::make('module_description')
                            ->label('Descripcion')
                            ->columnSpanFull()
                        //->required()
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module_description')
                    ->label('Descripcion')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module_cost')
                    ->label('Costo')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListPreferredModules::route('/'),
            'create' => Pages\CreatePreferredModule::route('/create'),
            'edit' => Pages\EditPreferredModule::route('/{record}/edit'),
        ];
    }
}

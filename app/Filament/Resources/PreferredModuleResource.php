<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\PreferredModuleResource\Pages;
use App\Filament\Resources\PreferredModuleResource\RelationManagers;
use App\Filament\Resources\PreferredModuleResource\RelationManagers\ItemsRelationManager as RelationManagersItemsRelationManager;
use App\Filament\Resources\PreferredModuleResource\RelationManagers\PreferredItemsRelationManager;
use App\Models\PreferredModule;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
            Section::make('Información del módulo')
                ->schema([
                TextInput::make('module_name')
                ->label('Nombre')
                ->unique()
                ->required()
                ->disabledOn('edit')
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
          PreferredItemsRelationManager::class
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

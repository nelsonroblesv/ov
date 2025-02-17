<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZonasResource\Pages;
use App\Filament\Resources\ZonasResource\RelationManagers;
use App\Models\Regiones;
use App\Models\Zonas;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZonasResource extends Resource
{
    protected static ?string $model = Zonas::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Ajustes';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $breadcrumb = "Zonas";
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Zonas')->schema([
                    Select::make('regiones_id')
                        ->label('Región')
                        ->placeholder('Seleccione una región')
                        ->options(
                            Regiones::all()->pluck('name', 'id')
                        )
                        ->required()
                        ->preload(),
                ]),

                Section::make('')->schema([
                    TextInput::make('nombre_zona')
                        ->label('Nombre')
                        ->placeholder('Nombre de la zona')
                        ->required(),

                    ColorPicker::make('color_zona')
                        ->label('Color')
                        ->placeholder('Color de la zona')
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_zona') ->label('Nombre')->searchable()->sortable(),
                ColorColumn::make('color_zona') ->label('Color')->searchable()->sortable(),
                TextColumn::make('regiones.name') ->label('Región')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Zona eliminada')
                            ->body('La Zona ha sido eliminada del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Zona')
                    ->modalDescription('Estas seguro que deseas eliminar esta Zona? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Registros eliminados')
                            ->body('Los registros seleccionados han sido eliminados.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Zonas')
                    ->modalDescription('Estas seguro que deseas eliminar las Zonas seleccionadas? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
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
            'index' => Pages\ListZonas::route('/'),
            'create' => Pages\CreateZonas::route('/create'),
            'edit' => Pages\EditZonas::route('/{record}/edit'),
        ];
    }
}

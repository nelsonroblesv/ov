<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteGuiasResource\Pages;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers\GuiasRelationManager;
use App\Models\Guias;
use App\Models\PaqueteGuias;
use App\Models\Regiones;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaqueteGuiasResource extends Resource
{
    protected static ?string $model = PaqueteGuias::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Logistica';
    protected static ?string $navigationLabel = 'Paquete de Guias';
    protected static ?string $breadcrumb = "Paquete de Guias";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del Paquete de Guias')->schema([
                    TextInput::make('periodo')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxLength(10),

                    Select::make('semana')
                        ->placeholder('Selecciona una semana')
                        ->required()
                        ->options([
                            '1' => 'Semana 1',
                            '2' => 'Semana 2',
                            '3' => 'Semana 3',
                            '4' => 'Semana 4',
                        ]),

                    TextInput::make('num_semana')
                        ->required()
                        ->numeric()
                        ->default(fn() => Carbon::now()->isoWeek())
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(53),

                    Select::make('regiones_id')
                        ->placeholder('Selecciona una región')
                        ->label('Región')
                        ->required()
                        ->options(
                            Regiones::query()->where('is_active', true)->pluck('name', 'id')
                        ),
                    DatePicker::make('created_at')
                        ->label('Fecha de registro')
                        ->default(now()),

                    Select::make('estado')
                        ->label('Estado del paquete')
                        ->required()
                        ->options([
                            'rev' => 'En Revisión',
                            'fal' => 'Con Faltantes',
                            'com' => 'Paquete Completo',
                        ])
                        ->default('rev'),

                    Hidden::make('user_id')->default(fn() => auth()->id()),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->withCount([
                        'guias as guias_entregadas_count' => fn(Builder $q) =>
                        $q->where('recibido', true),
                        'guias as guias_pendientes_count' => fn(Builder $q) =>
                        $q->where('recibido', false),
                    ]);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([

                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->date()
                    ->sortable(),
                TextColumn::make('periodo')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('semana')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('num_semana')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('region.name')
                    ->label('Región')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado del paquete')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'rev' => 'En Revisión',
                        'fal' => 'Con Faltantes',
                        'com' => 'Paquete Completo',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'warning' => 'rev',
                        'danger' => 'fal',
                        'success' => 'com'
                    ]),
                TextColumn::make('guias_entregadas_count')
                    ->label('Entregadas')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('guias_pendientes_count')
                    ->label('Pendientes')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Borrar Paquete de Guias')
                        ->modalDescription('Estás seguro que deseas eliminar este Paquete de Guias? Esta acción no se puede deshacer.'),
                ])
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
            GuiasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaqueteGuias::route('/'),
            'create' => Pages\CreatePaqueteGuias::route('/create'),
            'edit' => Pages\EditPaqueteGuias::route('/{record}/edit'),
        ];
    }
}

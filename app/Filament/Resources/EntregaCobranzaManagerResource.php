<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaCobranzaManagerResource\Pages;
use App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers;
use App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers\DetallesRelationManager as RelationManagersDetallesRelationManager;
use App\Filament\Resources\EntregaCobranzaResource\RelationManagers\DetallesRelationManager;
use App\Models\EntregaCobranza;
use App\Models\EntregaCobranzaManager;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntregaCobranzaManagerResource extends Resource
{
    protected static ?string $model = EntregaCobranza::class;

    protected static ?string $title = 'Administrar Agenda';
    protected static ?string $slug = 'administrar-agenda';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Agenda de Trabajo';
    protected static ?string $navigationLabel = 'Agendar Visitas';
    protected static ?string $breadcrumb = "Agendar Visitas";
    protected static ?int $navigationSort = 0;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos Generales')->schema([
                    /*
                    DatePicker::make('fecha_programada')
                        ->label('Fecha programada')
                        ->required()
                        ->unique(ignoreRecord:true)
                        ->validationMessages([
                            'unique' => 'Ya existe una entrega programada para esta fecha. Selecciona otra o edita la existente.'
                        ])
                        ->default(Carbon::now())
                        ->disabledOn('edit'),
*/
                    TextInput::make('periodo')->label('Periodo:')->required(),

                    Select::make('semana_mes')->label('Semana (mes):')->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ])->required(),

                    TextInput::make('semana_anio')->label('Semana (año):')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(52)
                        ->default(Carbon::now()->weekOfYear)
                        ->required(),

                    Select::make('tipo_semana')->label('Tipo de semana:')->options([
                        '0' => 'PAR',
                        '1' => 'NON'
                    ])
                    ->required(),

                    DatePicker::make('fecha_inicio')->label('Fecha de Inicio:')
                        ->date()
                        ->default(Carbon::now())
                         ->required()
                         ->disabledOn('edit'),

                    DatePicker::make('fecha_fin')->label('Fecha de Fin:')
                        ->date()
                        ->default(Carbon::now()->addDay(7))
                         ->required()
                         ->disabledOn('edit'),

                    Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'DESC')
            ->columns([
                TextColumn::make('periodo')->label('Periodo'),
                TextColumn::make('semana_mes')->label('Semana (mes)')->alignCenter(),
                TextColumn::make('semana_anio')->label('Semana (anual)')->alignCenter(),
                TextColumn::make('fecha_inicio')->label('Fecha inicio')->alignCenter(),
                TextColumn::make('fecha_fin')->label('Fecha_fin'),
                TextColumn::make('altaUser.name')->label('Registrado por'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagersDetallesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntregaCobranzaManagers::route('/'),
            'create' => Pages\CreateEntregaCobranzaManager::route('/create'),
            'edit' => Pages\EditEntregaCobranzaManager::route('/{record}/edit'),
        ];
    }
}

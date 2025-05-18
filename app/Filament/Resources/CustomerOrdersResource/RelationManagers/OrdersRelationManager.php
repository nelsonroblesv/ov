<?php

namespace App\Filament\Resources\CustomerOrdersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Str;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    TextInput::make('number')
                        ->label('Numero de Pedido')
                        ->required()
                        ->maxLength(255)
                        ->suffixIcon('heroicon-m-hashtag')
                        ->unique(ignoreRecord: true)
                        ->disabledOn('edit'),

                    ToggleButtons::make('tipo_nota')
                        ->label('Tipo de Nota')
                        ->required()
                        ->options([
                            'Sistema' => 'Sistema',
                            'Remisión' => 'Remisión',
                        ])
                        ->inline()
                        ->default('Sistema')
                        ->colors([
                            'Sistema' => 'success',
                            'Remisión' => 'warning',
                        ])
                        ->icons([
                            'Sistema' => 'heroicon-o-arrow-left-end-on-rectangle',
                            'Remisión' => 'heroicon-o-arrow-right-end-on-rectangle',
                        ])
                        ->default('Sistema'),

                    Select::make('tipo_semana_nota')
                        ->label('Semana de la Nota')
                        ->required()
                        ->options([
                            'PAR' => 'PAR',
                            'NON' => 'NON',
                        ]),

                    Select::make('dia_nota')
                        ->label('Día de la Nota')
                        ->required()
                        ->options([
                            'Lunes' => 'Lunes',
                            'Martes' => 'Martes',
                            'Miercoles' => 'Miercoles',
                            'Jueves' => 'Jueves',
                            'Viernes' => 'Viernes',
                        ]),

                    Select::make('status')
                        ->label('Estado del Pedido')
                        ->required()
                        ->options([
                            'PEN' => 'Pendiente',
                            'COM'  => 'Completo',
                            'REC'  => 'Rechazado',
                            'REU'  => 'Reubicar',
                            'DEV'  => 'Devuelta Parcial',
                            'SIG'  => 'Siguiente Visita'
                        ]),

                    DatePicker::make('created_at')
                        ->label('Fecha')
                        ->required()
                        ->native(),

                    DatePicker::make('fecha_liquidacion')
                        ->label('Fecha de liquidación')
                        ->required()
                        ->native(),

                    TextInput::make('notes')
                        ->label('Notas adicionales del Pedido')
                        ->nullable()
                        ->suffixIcon('heroicon-m-pencil-square'),

                    TextInput::make('grand_total')
                        ->label('Monto')
                        ->required()
                        ->numeric()
                        ->placeholder('0.00')
                        ->suffixIcon('heroicon-m-currency-dollar'),

                    FileUpload::make('notas_venta')
                        ->label('Notas de Venta')
                        ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                        ->multiple()
                        ->directory('notas_venta')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),

                    Hidden::make('registrado_por')->default(fn() => auth()->id()),
                    Hidden::make('solicitado_por')->default(fn() => auth()->id()),
                ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('number'),
                TextColumn::make('status'),
                TextColumn::make('grand_total')
                    ->summarize(Sum::make()->label('Total'))
                    ->prefix('$')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Pedido'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

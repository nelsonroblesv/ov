<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidosResource\Pages;
use App\Filament\Resources\PedidosResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidosResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Cliente')
                        ->schema([
                            Section::make()->schema([
                                Select::make('customer_id')
                                    ->label('Cliente')
                                    ->columnSpanFull(),

                                TextInput::make('zonas_id')
                                    ->label('Zona')
                                    ->disabled(),

                                TextInput::make('regiones_id')
                                    ->label('Región')
                                    ->disabled(),

                                Select::make('customer_type')
                                    ->label('Tipo de Cliente'),

                                Select::make('factura')
                                    ->label('Factura'),

                            ])->columns(2)

                        ]),
                    Step::make('Pedido')
                        ->schema([
                            Section::make()->schema([
                                TextInput::make('num_pedido')
                                    ->label('# Pedido'),

                                DatePicker::make('fecha_pedido')
                                    ->label('Fecha del Pedido'),

                                Select::make('tipo_nota')
                                    ->label('Tipo de Nota'),

                                Select::make('tipo_semana_nota')
                                    ->label('Tipo de Semana'),

                                Select::make('periodo')
                                    ->label('Periodo'),

                                Select::make('semana')
                                    ->label('Semana'),

                                Select::make('dia_nota')
                                    ->label('Día de Nota'),

                                Select::make('estado_pedido')
                                    ->label('Estado del Pedido'),

                            ])->columns(2)

                        ]),
                    Step::make('Pago y Entrega')
                        ->schema([
                            Section::make()->schema([
                                TextInput::make('monto')
                                    ->label('Monto del Pedido'),

                                Select::make('num_ruta')
                                    ->label('# Ruta'),

                                DatePicker::make('fecha_entrega')
                                    ->label('Fecha de Entrega'),

                                DatePicker::make('fecha_liquidacion')
                                    ->label('Fecha de Liquidación'),

                                Select::make('distribuidor')
                                    ->label('Distribuidor'),

                                Select::make('entrega')
                                    ->label('Entrega'),

                                Select::make('reparto')
                                    ->label('Reparto'),

                                Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->columnSpanFull(),

                                FileUpload::make('notas_venta')
                                    ->label('Notas de Venta')
                                    ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                                    ->multiple()
                                    ->directory('notas_venta')
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpanFull()
                            ])->columns(2)
                        ]),
                ])->columnSpanFull()
                    ->startOnStep(3)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedidos::route('/create'),
            'edit' => Pages\EditPedidos::route('/{record}/edit'),
        ];
    }
}

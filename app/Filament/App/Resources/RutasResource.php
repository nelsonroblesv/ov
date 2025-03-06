<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RutasResource\Pages;
use App\Filament\App\Resources\RutasResource\RelationManagers;
use App\Models\BitacoraCustomers;
use App\Models\Rutas;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RutasResource extends Resource
{
    protected static ?string $model = Rutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Mis Rutas';
    protected static ?string $breadcrumb = "Mis Rutas";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas programadas para hoy ' . Carbon::now()->setTimezone('America/Merida')->locale('es')->translatedFormat('l d \d\e F Y'). 
            '. No olvides agregar un registro en la Bitacora en cada visita.')

            ->reorderable('sort')
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->id();
                $query->where('user_id', $user);
            })
            ->defaultSort('sort', 'desc')

            ->columns([
                TextColumn::make('sort')->label('#')->sortable(),
                TextColumn::make('customer.name')->label('Cliente o Identificador'),
                TextColumn::make('tipo_cliente')->label('Tipo')->badge()->alignCenter()
                    ->colors([
                        'gray' => 'PO',
                        'warning' => 'PR',
                        'success' => 'PV',
                        'danger' => 'RD',
                        'info' => 'BK',
                        'warning' => 'SL'
                    ])
                    ->icons([
                        'heroicon-o-map' => 'PO',
                        'heroicon-o-magnifying-glass' => 'PR',
                        'heroicon-o-building-storefront' => 'PV',
                        'heroicon-o-user' => 'RD',
                        'heroicon-o-star' => 'BK',
                        'heroicon-o-sparkles' => 'SL'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PO' => 'Posible',
                        'PR' => 'Prospecto',
                        'PV' => 'Punto Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',
                        'SL' => 'Silver',
                    ][$state] ?? 'Otro'),
                TextColumn::make('regiones.name')->label('Region')
                    ->badge()
                    ->alignCenter()
                    ->colors(['info']),
                TextColumn::make('zonas.nombre_zona')->label('Zona')
                    ->badge()
                    ->alignCenter()
                    ->colors(['warning']),
                TextColumn::make('full_address')->label('Direccion'),
                IconColumn::make('visited')->boolean()->label('Visitado')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
                IconColumn::make('full_address')->label('Ubicación')->alignCenter()
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn($record) => "https://www.google.com/maps/search/?api=1&query=" . urlencode($record->full_address), true)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Registrar Visita')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('warning')
                    ->form([
                        Section::make('Registro en Bitácora')->schema([
                            Toggle::make('show_video')->label('Se presentó Video Testimonio')
                                ->onIcon('heroicon-m-play')
                                ->offIcon('heroicon-m-x-mark')
                                ->onColor('success')
                                ->offColor('danger'),

                            TextInput::make('notas')->label('Notas')->required()->columnSpanFull(),

                            Section::make('Testigos')->schema([
                                FileUpload::make('testigo_1')->label('Foto 1')->nullable()
                                    ->placeholder('Tomar o cargar Foto')
                                    ->directory('bitacora-testigos'),
                                FileUpload::make('testigo_2')->label('Foto 2')->nullable()
                                    ->placeholder('Tomar o cargar Foto')
                                    ->directory('bitacora-testigos')
                            ])->columns(2)
                        ])
                    ])
                    ->hidden(function ($record) {
                        return BitacoraCustomers::where('user_id', auth()->id())
                            ->where('customers_id', $record->customer_id)
                            ->whereDate('created_at', Carbon::now()->setTimezone('America/Merida')->toDateString()) // Mismo día
                            ->exists();
                    })
                    ->action(function ($record, array $data) {
                        BitacoraCustomers::create([
                            'customers_id' => $record->customer_id,
                            'user_id' => auth()->id(),
                            'show_video' => $data['show_video'],
                            'notas' => $data['notas'],
                            'testigo_1' => $data['testigo_1'],
                            'testigo_2' => $data['testigo_2'],
                            'created_at' => Carbon::now()->setTimezone('America/Merida')
                        ]);

                        $record->update(['visited' => 1]);

                        Notification::make()
                            ->title('Registro guardado en la Bitácora')
                            ->success()
                            ->send();
                    })
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
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRutas::route('/create'),
            'edit' => Pages\EditRutas::route('/{record}/edit'),
        ];
    }
}

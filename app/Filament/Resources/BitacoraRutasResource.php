<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraRutasResource\Pages;
use App\Filament\Resources\BitacoraRutasResource\RelationManagers;
use App\Models\BitacoraCustomers;
use App\Models\BitacoraRutas;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BitacoraRutasResource extends Resource
{
    protected static ?string $model = BitacoraCustomers::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Bitacora';
    protected static ?string $breadcrumb = 'Bitacora';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Visita')->schema([

                    // Select para elegir el tipo de visita
                    Select::make('tipo_visita')
                        ->placeholder('Seleccione una opción') // Placeholder no seleccionable
                        ->required()
                        ->label('Tipo de Visita')
                        ->options([
                            'EN' => 'Entrega de Pedido',
                            'CE' => 'Establecimiento Cerrado',
                            'RE' => 'Visita Regular',
                            'PR' => 'Prospectación',
                        ])
                        ->reactive()
                        ->default('EN')
                        ->columnSpanFull(),

                    // Sección de Entrega de Pedido
                    Section::make('Entrega de Pedido')
                        ->visible(fn($get) => $get('tipo_visita') === 'EN')
                        ->schema([
                            FileUpload::make('foto_entrega')
                                ->label('Foto de entrega')
                                ->placeholder('Foto de entrega de pedido')
                                ->multiple()
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),

                            FileUpload::make('foto_stock_antes')
                                ->label('Foto de stock antes')
                                ->placeholder('Foto de stock antes de entrega')
                                ->multiple()
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),

                            FileUpload::make('foto_stock_despues')
                                ->label('Foto de stock después')
                                ->placeholder('Foto de stock después de entrega')
                                ->multiple()
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),
                        ]),

                    Section::make('Establecimiento Cerrado')
                        ->visible(fn($get) => $get('tipo_visita') === 'CE')
                        ->schema([
                            FileUpload::make('foto_lugar_cerrado')
                                ->label('Foto de establecimiento cerrado')
                                ->placeholder('Tomar o cargar foto')
                                ->multiple()
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),
                        ]),

                    // Sección de Visita Regular
                    Section::make('Visita Regular')
                        ->visible(fn($get) => $get('tipo_visita') === 'RE')
                        ->schema([
                            FileUpload::make('foto_stock_regular')
                                ->label('Foto de stock actual')
                                ->placeholder('Tomar o cargar foto')
                                ->multiple()
                                ->default(function ($record) {
                                    if ($record && $record->foto_entrega && is_array($record->foto_entrega) && count($record->foto_entrega) > 0) {
                                        return asset($record->foto_entrega[0]); // Asumiendo que la ruta está en el primer elemento del array
                                    }
                                    return null;
                                })
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),
                        ]),

                    // Sección de Prospectación
                    Section::make('Prospectación')
                        ->visible(fn($get) => $get('tipo_visita') === 'PR')
                        ->schema([
                            Toggle::make('show_video')
                                ->label('Se presentó Video Testimonio')
                                ->onIcon('heroicon-m-play')
                                ->offIcon('heroicon-m-x-mark')
                                ->onColor('success')
                                ->offColor('danger'),

                            FileUpload::make('foto_evidencia_prospectacion')
                                ->label('Fotos de Evidencia')
                                ->placeholder('Tomar o cargar fotos')
                                ->multiple()
                                ->directory('fotos-bitacora')
                                ->downloadable()
                                ->required(),
                        ]),

                    // Notas generales
                    TextInput::make('notas')
                        ->label('Notas')
                        ->required()
                        ->columnSpanFull(),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('customer.user');
            })
            ->heading('Registros de Actividad')
            ->description('Listado de visitas de los usuarios. Puedes filtrar por tipo de visita y 
            vendedor, asi como ordenar por fecha de registro y mostrar u ocultar ciertas columnas.')
            ->defaultSort('created_at', 'desc')

            ->columns([
                //TextColumn::make('customers.user.name')->label('Vendedor')->searchable()->sortable(),
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas(
                            'customer',
                            fn($q) =>
                            $q->where('name', 'like', "%{$search}%")
                        );
                    }),
                TextColumn::make('customer.user.name')
                    ->label('Vendedor')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas(
                            'customer.user',
                            fn($q) =>
                            $q->where('name', 'like', "%{$search}%")
                        );
                    }),
                TextColumn::make('tipo_visita')->label('Visita')->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn(string $state): string => [
                        'EN' => 'Entrega',
                        'CE' => 'Cerrado',
                        'RE' => 'Regular',
                        'PR' => 'Prospeccion',
                    ][$state] ?? 'Otro'),

                TextColumn::make('notas')->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('customers.regiones.name')->label('Region')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customers.zonas.nombre_zona')->label('Zona')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('foto_entrega')->label('Entrega')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_antes')->label('Stock Antes')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_despues')->label('Stock Despues')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_lugar_cerrado')->label('Cerrado')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_regular')->label('Regular')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_evidencia_prospectacion')->label('Prospeccion')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')->label('Registro')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('show_video')->label('Video Testimonio')->boolean()->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                SelectFilter::make('user_id')->label('Usuario')
                    ->options(User::pluck('name', 'id')),

                SelectFilter::make('tipo_visita')->label('Tipo Visita')
                    ->options([
                        'EN' => 'Entrega',
                        'CE' => 'Cerrado',
                        'RE' => 'Regular',
                        'PR' => 'Prospección',
                    ])
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                //  Tables\Actions\BulkActionGroup::make([
                //  Tables\Actions\DeleteBulkAction::make(),
                //  ]),
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
            'index' => Pages\ListBitacoraRutas::route('/'),
            'create' => Pages\CreateBitacoraRutas::route('/create'),
            'edit' => Pages\EditBitacoraRutas::route('/{record}/edit'),
            'view' => Pages\ViewBitacoraRutas::route('/{record}'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return BitacoraCustomers::query()->with(['customer.user']);
    }
}

<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BitacoraCustomersResource\Pages;
use App\Filament\App\Resources\BitacoraCustomersResource\Pages\ViewBitacoraCustomers;
use App\Filament\App\Resources\BitacoraCustomersResource\RelationManagers;
use App\Models\AsignarTipoSemana;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BitacoraCustomersResource extends Resource
{
    protected static ?string $model = BitacoraCustomers::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Registros de Actividad';
    protected static ?string $breadcrumb = 'Bitacora';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

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
                            ->required(),

                        FileUpload::make('foto_stock_antes')
                            ->label('Foto de stock antes')
                            ->placeholder('Foto de stock antes de entrega')
                            ->multiple()
                            ->directory('fotos-bitacora')
                            ->required(),

                        FileUpload::make('foto_stock_despues')
                            ->label('Foto de stock después')
                            ->placeholder('Foto de stock después de entrega')
                            ->multiple()
                            ->directory('fotos-bitacora')
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
            $query->whereHas('customers', function (Builder $query) {
                $query->where('user_id', auth()->user()->id);
            });
        })
        ->defaultSort('created_at', 'desc')
        ->heading('Registros de Actividad')
        ->description('Listado de registros de visitas realizados a Clientes y Prospección')
            ->columns([
                //TextColumn::make('customers.user.name')->label('Registrado')->searchable()->sortable(),
                TextColumn::make('customers.name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('customers.regiones.name')->label('Regiones')->searchable()->sortable(),
                TextColumn::make('customers.zona.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('notas')->label('Notas')->searchable(),
                ImageColumn::make('testigo_1')->label('Testigo 1')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('testigo_2')->label('Testigo 2')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Registro')->dateTime()->sortable(),
               // IconColumn::make('show_video')->label('Video Testimonio')->boolean()->alignCenter()
            ])
            ->filters([
            ])
            ->actions([
             //   Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                */
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
            'index' => Pages\ListBitacoraCustomers::route('/'),
            'create' => Pages\CreateBitacoraCustomers::route('/create'),
            'edit' => Pages\EditBitacoraCustomers::route('/{record}/edit'),
            'view' => ViewBitacoraCustomers::route('/{record}/edit')
        ];
    }
}

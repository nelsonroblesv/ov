<?php

namespace App\Filament\Resources;

use App\Enums\CfdiTypeEnum;
use App\Enums\SociedadTypeEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use App\Models\Municipality;
use App\Models\State;
use Carbon\Callback;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Get;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Reactive;
use PhpParser\ErrorHandler\Collecting;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informacion Personal')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre completo')
                                ->required()
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-user'),

                            TextInput::make('alias')
                                ->label('Alias')
                                ->required()
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-user-circle'),

                           TextInput::make('email')
                                ->label('Correo electrónico')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord:true)
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-at-symbol'),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->required()
                                ->unique(ignoreRecord:true)
                                ->maxLength(50)
                                ->suffixIcon('heroicon-m-phone'),

                            DatePicker::make('birthday')
                                ->label('Fecha de nacimiento')
                                ->suffixIcon('heroicon-m-cake'),
                            /*
                            Forms\Components\TextInput::make('contact')
                            ->label('Contacto')
                            ->required()
                            ->tel()
                            ->suffixIcon('heroicon-m-device-phone-mobile'),
                        */

                           FileUpload::make('avatar')
                                ->label('Avatar')
                                ->image()
                                ->avatar()
                                ->directory('customer-avatar')
                        ])->columns(2),

                    Wizard\Step::make('Informacion del establecimiento')
                        ->schema([
                            TextInput::make('address')
                                ->label('Dirección')
                                ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, Intersecciones')
                                // ->required()
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-map')
                                ->columnSpanFull(),

                            Select::make('state_id')
                                ->options(State::all()->pluck('name', 'id')->toArray())
                                ->label('Estado')
                                ->searchable()
                                ->reactive()
                                ->preload()
                                ->live()
                                //->required()
                                ->afterStateUpdated(fn(callable $set) => $set('municipality_id', null)),

                            Select::make('municipality_id')
                                ->options(function (callable $get) {
                                    $estado = State::find($get('state_id'));
                                    if (!$estado) {
                                        return Municipality::all()->pluck('name', 'id');
                                    }
                                    return $estado->municipality->pluck('name', 'id');
                                })
                                ->reactive()
                                ->label('Municipio')
                                //  ->required()
                                ->searchable()
                                ->preload()
                                ->live(),

                            TextInput::make('locality')
                                //  ->required()
                                ->label('Localidad'),

                            TextInput::make('zip_code')
                                ->label('Código Postal')
                                //   ->required()
                                ->numeric()
                                ->maxLength(5)
                                ->suffixIcon('heroicon-m-hashtag'),

                            TextInput::make('coordinate')
                                ->label('Coordenadas Google Maps')
                                ->helperText('Formato: 20.1845751, -90.1334567')
                                //   ->required()
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-map-pin'),

                            Section::make('Fotos del establecimiento')
                                ->collapsible()
                                ->schema([
                                    FileUpload::make('front_image')
                                        ->label('Foto Exterior')
                                        ->helperText('Carga una foto del exterior del establecimiento')
                                        ->image()
                                        //  ->required()
                                        ->imageEditor()
                                        ->directory('customer-images'),

                                    FileUpload::make('inside_image')
                                        ->label('Foto Interior')
                                        ->helperText('Carga una foto del interior del establecimiento')
                                        ->image()
                                        //   ->required()
                                        ->imageEditor()
                                        ->directory('customer-images'),
                                ])->columns(2),

                            Section::make('Informacion adicional')
                                ->collapsible()
                                ->schema([
                                    MarkdownEditor::make('extra')
                                        //  ->required()
                                        ->label('Datos extra del cliente')
                                ])->icon('heroicon-o-information-circle')
                        ])->columns(2),

                    Wizard\Step::make('Datos de Facturacion')
                        ->schema([
                            TextInput::make('name_facturacion')
                                ->label('Nombre')
                                //   ->required()
                                ->suffixIcon('heroicon-m-user-circle'),

                                TextInput::make('razon_social')
                                ->label('Razon Social')
                                //   ->required()
                                ->suffixIcon('heroicon-m-building-library'),

                                TextInput::make('address_facturacion')
                                ->label('Direccion')
                                //   ->required()
                                ->suffixIcon('heroicon-m-map-pin'),

                               TextInput::make('postal_code_facturacion')
                                ->label('Codigo Postal')
                                ->numeric()
                                //   ->required()
                                ->suffixIcon('heroicon-m-hashtag'),

                                Select::make('tipo_cfdi')
                                ->label('Tipo de CFDI')
                                ->options([
                                   'Ingreso' => CfdiTypeEnum::INGRESO->value,
                                   'Egreso' => CfdiTypeEnum::EGRESO->value,
                                   'Traslado' => CfdiTypeEnum::TRASLADO->value,
                                   'Nomina' => CfdiTypeEnum::NOMINA->value
                                ])
                                ->suffixIcon('heroicon-m-document-text'),

                                Select::make('tipo_razon_social')
                                ->label('Tipo de Razon Social')
                                ->options([
                                   'Sociedad Anonima' => SociedadTypeEnum::S_ANONIMA->value,
                                   'Sociedad Civil' => SociedadTypeEnum::S_CIVIL->value,
                                ])
                                ->suffixIcon('heroicon-m-document-text'),

                                FileUpload::make('cfdi_document')
                                     ->columnSpanFull()
                                        ->label('CFDI')
                                        ->helperText('Carga un CFDI en formato PDF')
                                        //   ->required()
                                        ->directory('customer-cfdi'),

                        ])->columns(2),

                    Wizard\Step::make('Administracion del Cliente')
                        ->schema([
                            Section::make('Control')
                            ->collapsible()
                                ->schema([
                                    Forms\Components\Toggle::make('is_visible')
                                    ->label('Cliente Visible')
                                    ->default(true),
    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Cliente Activo')
                                    ->default(true)
                                ])->icon('heroicon-o-adjustments-vertical')->columns(2)
                        ])->columns(2),

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Clientes')
            ->description('Gestion de clientes.')
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Direccion')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('municipality.name')
                    ->label('Municipio')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('locality')
                    ->label('Localidad')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->label('Codigo postal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('front_image')
                    ->label('Exterior')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('inside_image')
                    ->label('Interior')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('coordinate')
                    ->label('Coordenadas')
                    ->url(fn(Customer $record): string => "http://maps.google.com/maps?q=loc: {$record->coordinate}")
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('danger')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('extra')
                    ->label('Notas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
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
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}

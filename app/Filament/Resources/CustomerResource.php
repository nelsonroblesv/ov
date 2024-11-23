<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Municipality;
use App\Models\State;
use Carbon\Callback;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información General')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-user'),

                        Forms\Components\TextInput::make('alias')
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-user-circle'),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-at-symbol'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-phone'),

                        Forms\Components\TextInput::make('contact')
                            ->label('Contacto')
                            ->required()
                            ->tel()
                            ->suffixIcon('heroicon-m-device-phone-mobile'),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->directory('profile-images')
                    ])->columns(3),

                Section::make('Domicilio')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, Intersecciones')
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-map'),

                        Forms\Components\Select::make('state_id')
                            ->options(State::all()->pluck('name', 'id')->toArray())
                            ->label('Estado')
                            ->searchable()
                            ->reactive()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn(callable $set) => $set('municipality_id', null)),

                        Forms\Components\Select::make('municipality_id')
                            ->options(function (callable $get) {
                                $estado = State::find($get('state_id'));
                                if (!$estado) {
                                    return Municipality::all()->pluck('name', 'id');
                                }
                                return $estado->municipality->pluck('name', 'id');
                            })
                            ->reactive()
                            ->label('Municipio')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                        Forms\Components\TextInput::make('locality')
                            ->label('Localidad')
                            ->required(),

                        Forms\Components\TextInput::make('zip_code')
                            ->label('Código Postal')
                            ->required()
                            ->numeric()
                            ->maxLength(5)
                            ->suffixIcon('heroicon-m-hashtag'),

                        Forms\Components\TextInput::make('coordinate')
                            ->label('Coordenadas Google Maps')
                            ->helperText('Formato: 20.1845751, -90.1334567')
                            ->required()
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-map-pin'),

                        Forms\Components\FileUpload::make('front_image')
                            ->label('Foto Exterior')
                            ->image()
                            ->required()
                            ->imageEditor()
                            ->directory('customer-images'),

                        Forms\Components\FileUpload::make('inside_image')
                            ->label('Foto Interior')
                            ->image()
                            ->required()
                            ->imageEditor()
                            ->directory('customer-images'),
                    ])->columns(2),

                Section::make('Extra')->schema([
                    Forms\Components\MarkdownEditor::make('extra')
                        ->label('Información adicional')
                ])->icon('heroicon-o-information-circle'),
                Group::make()
                    ->schema([
                        Section::make('Tipo de usuario')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Selecciona el tipo de Cliente')
                                    ->required()
                                    ->options([
                                        'par' => 'PAR',
                                        'non' => 'NON'
                                    ])
                            ])->icon('heroicon-o-users')
                    ]),
                Group::make()
                    ->schema([
                        Section::make('Control')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                ->label('Cliente Visible'),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Cliente Activo')
                            ])->icon('heroicon-o-adjustments-vertical')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('municipality.name')
                    ->label('Municipio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('locality')
                    ->label('Localidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->label('Codigo postal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('front_image')
                    ->label('Exterior'),
                Tables\Columns\ImageColumn::make('inside_image')
                    ->label('Interior'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

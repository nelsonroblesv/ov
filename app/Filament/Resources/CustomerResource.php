<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->numeric()
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

                        Forms\Components\Select::make('state')
                            ->label('Estado')
                            ->required(),

                        Forms\Components\Select::make('municipality')
                            ->label('Municipio')
                            ->required(),

                        Forms\Components\Select::make('locality')
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
                ]),

                Section::make('Control')
                    ->icon('heroicon-o-adjustments-vertical')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->required(),

                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('Usuario asignado')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->relationship('users', 'name')
                            ->suffixIcon('heroicon-m-users')
                    ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('municipality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('locality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('front_image'),
                Tables\Columns\ImageColumn::make('inside_image'),
                Tables\Columns\TextColumn::make('coordinate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('extra')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

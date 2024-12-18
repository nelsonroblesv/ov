<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use App\Models\Zone;
use Directory;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $breadcrumb = "Usuarios";
    //  protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Datos personales')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->helperText('Nombre completo')
                                ->required()
                                ->suffixIcon('heroicon-m-user')
                                ->columns(2),

                            TextInput::make('username')
                                ->label('Username')
                                ->helperText('Nombre de usuario (Unico)')
                                ->required()
                                ->suffixIcon('heroicon-m-user')
                                ->columns(2),

                            DatePicker::make('birthday')
                                ->label('Fecha de nacimiento')
                                ->helperText('Usa el formato dd/mm/aaaa')
                                ->required()
                                ->suffixIcon('heroicon-m-cake')
                                ->columns(2),

                            TextInput::make('email')
                                ->label('Correo electrónico')
                                ->helperText('Escribe tu correo')
                                // ->required()
                                ->email()
                                ->suffixIcon('heroicon-m-at-symbol')
                                ->columns(2),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->helperText('Ingresa un telefono')
                                ->dehydrated(fn($state) => filled($state))
                                ->required()
                                ->tel()
                                ->suffixIcon('heroicon-m-phone'),

                            TextInput::make('password')
                                ->label('Contraseña')
                                ->helperText('Ingresa una contraseña')
                                ->password()
                                ->revealable()
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $context): bool => $context === 'create')
                                ->disabledOn('edit'),

                            FileUpload::make('avatar')
                                ->label('Foto de perfil')
                                ->image()
                                ->avatar()
                                ->imageEditor()
                                ->circleCropper()
                                ->directory('user-avatar'),
                        ])->columns(2),

                    Wizard\Step::make('Empresa')
                        ->schema([
                            TextInput::make('email_empresa')
                                ->label('Correo electrónico de la empresa')
                                ->helperText('Escribe el correo asignado')
                                //    ->required()
                                ->email()
                                ->suffixIcon('heroicon-m-at-symbol'),

                            TextInput::make('phone_empresa')
                                ->label('Teléfono de la empresa')
                                ->helperText('Ingresa el telefono asignado.')
                                ->dehydrated(fn($state) => filled($state))
                                //   ->required()
                                ->tel()
                                ->suffixIcon('heroicon-m-phone'),

                            Select::make('role')
                                ->label('Rol asignado')
                                ->required()
                                ->dehydrated()
                                ->options([
                                    'Administrador' => UserRoleEnum::ADMINISTRADOR->value,
                                    'Gerente' => UserRoleEnum::GERENTE->value,
                                    'Vendedor' => UserRoleEnum::VENDEDOR->value,
                                    'Repartidor' => UserRoleEnum::REPARTIDOR->value,
                                ])
                                ->suffixIcon('heroicon-m-user-plus'),

                            ColorPicker::make('color')
                                ->label('Selecciona un color'),
                            //   ->required(),

                            DatePicker::make('fecha_inicio')
                                ->label('Inicio de contrato'),
                            //   ->required(),

                            DatePicker::make('fecha_fin')
                                ->label('Fin de contrato'),
                            //     ->required()

                            Toggle::make('is_active')
                                ->label('¿Usuario activo?')
                                ->onIcon('heroicon-m-user-plus')
                                ->offIcon('heroicon-m-user-minus')
                                ->onColor('success')
                                ->offColor('danger')
                                ->default(true),

                            Section::make('')->schema([
                                Repeater::make('zoneUser')
                                    ->label('Asignar Zona(s) a Usuario')
                                    ->relationship()
                                    ->schema([
                                        Select::make('zone_id')
                                            ->label('Zonas disponibles')
                                            ->placeholder('Selecciona una zona')
                                            ->options(Zone::query()->pluck('name', 'id'))
                                            ->reactive()
                                            ->searchable()
                                            ->preload()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ])
                                    ->createItemButtonLabel('Agregar Zona')
                                    ->columnSpanFull(),
                            ])
                        ])->columns(2),
                    Wizard\Step::make('Expediente')
                        ->schema([
                            TextInput::make('rfc')
                                ->label('Ingresa el RFC')
                                ->helperText('Maximo 13 caracteres. Mayusculas.')
                                //       ->required()
                                ->maxLength(13)
                                ->autocapitalize(true),

                            FileUpload::make('rfc_doc')
                                ->label('RFC')
                                ->helperText('En formato PDF')
                                ->directory('rfc-user'),

                            TextInput::make('curp')
                                ->label('Ingresa la CURP. Mayusculas.')
                                ->helperText('Maximo 18 caracteres')
                                //       ->required()
                                ->maxLength(18),

                            FileUpload::make('curp_doc')
                                ->label('CURP')
                                ->helperText('En formato PDF')
                                ->directory('curp-user'),

                            TextInput::make('imss')
                                ->label('Ingresa Numero de Seguridad Social')
                                ->helperText('Maximo 11 digitos')
                                //       ->required()
                                ->numeric()
                                ->maxLength(11),

                            FileUpload::make('imss_doc')
                                ->label('Documento IMSS')
                                ->helperText('En formato PDF')
                                ->directory('imss-user'),

                            FileUpload::make('comprobante_domicilio_doc')
                                ->label('Comprobante de domiclio')
                                ->helperText('En formato PDF')
                                ->directory('domicilio-user'),

                            FileUpload::make('licencia_image')
                                ->label('Licencia de conducir')
                                ->helperText('En formato de imagen')
                                ->image()
                                ->directory('licencia-user'),

                            FileUpload::make('ine_image')
                                ->label('INE')
                                ->helperText('En formato de imagen')
                                ->image()
                                ->directory('ine-user')
                        ])->columns(2),

                    Wizard\Step::make('Informacion financiera')
                        ->schema([
                            TextInput::make('banco')
                                ->label('Nombre del banco'),
                            //        ->required(),
                            TextInput::make('cuenta')
                                ->label('Numero de cuenta'),
                            //        ->required(),
                            TextInput::make('clabe')
                                ->label('CLABE')
                            //       ->required()
                        ])->columns(2)
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Usuarios')
            ->description('Gestion de usuarios del sistema.')
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Perfil'),
                ColorColumn::make('color')
                    ->label('Color'),
                TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->label('Username'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                TextColumn::make('role')
                    ->searchable()
                    ->sortable()
                    ->label('Rol'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Correo'),
                TextColumn::make('email_empresa')
                    ->searchable()
                    ->sortable()
                    ->label('Correo Empresa')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Teléfono'),
                TextColumn::make('phone_empresa')
                    ->searchable()
                    ->sortable()
                    ->label('Teléfono Empresa')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->sortable()
                    ->boolean(),
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
            // OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}')
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Datos personales')
                            ->collapsible()
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->helperText('Escribe tu nombre completo')
                                    ->required()
                                    ->suffixIcon('heroicon-m-user'),

                                DatePicker::make('birthday')
                                    ->label('Fecha de nacimiento')
                                    ->helperText('Usa el formato dd/mm/aaaa')
                                    ->required()
                                    ->suffixIcon('heroicon-m-cake'),

                                TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->helperText('Escribe tu correo')
                                    ->required()
                                    ->email()
                                    ->suffixIcon('heroicon-m-at-symbol'),

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
                                    ->disabledOn('edit')
                            ])->columns(1),

                        Section::make('Avatar')
                            ->collapsible()
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                FileUpload::make('avatar')
                                    ->label('Foto de perfil')
                                    ->image()
                                    //->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('user-avatar')
                            ])->columns(1),

                        Section::make('Empresa')
                            ->collapsible()
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                TextInput::make('email_empresa')
                                    ->label('Correo electrónico de la empresa')
                                    ->helperText('Escribe el correo asignado')
                                    ->required()
                                    ->email()
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                TextInput::make('phone_empresa')
                                    ->label('Teléfono de la empresa')
                                    ->helperText('Ingresa el telefono asignado.')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required()
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
                                    ])
                                    ->suffixIcon('heroicon-m-user-plus'),

                                ColorPicker::make('color')
                                    ->label('Selecciona un color')
                                    ->required(),

                                DatePicker::make('fecha_inicio')
                                    ->label('Inicio de contrato')
                                    ->required(),

                                DatePicker::make('fecha_fin')
                                    ->label('Fin de contrato')
                                    ->required()
                            ]),

                        Section::make('Control')
                            ->collapsible()
                            ->icon('heroicon-o-adjustments-vertical')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('¿Usuario activo?')
                                    ->onIcon('heroicon-m-user-plus')
                                    ->offIcon('heroicon-m-user-minus')
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ])
                    ]),

                Group::make()
                    ->schema([
                        Section::make('Expediente')
                            ->collapsible()
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                TextInput::make('rfc')
                                    ->label('Ingresa el RFC')
                                    ->helperText('Maximo 13 caracteres. Mayusculas.')
                                    ->required()
                                    ->maxLength(13)
                                    ->autocapitalize(true),

                                FileUpload::make('rfc_doc')
                                    ->label('RFC')
                                    ->helperText('En formato PDF')
                                    ->directory('rfc-user'),

                                TextInput::make('curp')
                                    ->label('Ingresa la CURP. Mayusculas.')
                                    ->helperText('Maximo 18 caracteres')
                                    ->required()
                                    ->maxLength(18),

                                FileUpload::make('curp_doc')
                                    ->label('CURP')
                                    ->helperText('En formato PDF')
                                    ->directory('curp-user'),

                                TextInput::make('imss')
                                    ->label('Ingresa Numero de Seguridad Social')
                                    ->helperText('Maximo 11 digitos')
                                    ->required()
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
                            ]),
                        Section::make('Datos financieros')
                            ->collapsible()
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                TextInput::make('banco')
                                    ->label('Nombre del banco')
                                    ->required(),
                                TextInput::make('cuenta')
                                    ->label('Numero de cuenta')
                                    ->required(),
                                TextInput::make('clabe')
                                    ->label('CLABE')
                                    ->required()
                            ])
                    ])
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
                    ->label('Correo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_empresa')
                    ->searchable()
                    ->sortable()
                    ->label('Correo Empresa'),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_empresa')
                    ->searchable()
                    ->sortable()
                    ->label('Teléfono Empresa'),
                IconColumn::make('is_active')
                    ->label('Activo')
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

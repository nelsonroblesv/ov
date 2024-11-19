<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Datos personales')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->helperText('Escribe tu nombre completo')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->helperText('Escribe tu correo')
                                    ->required()
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->helperText('Ingresa un telefono')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required()
                                    ->tel(),
                                Forms\Components\TextInput::make('password')
                                    ->label('Contraseña')
                                    ->helperText('Ingresa una contraseña')
                                    ->password()
                                    ->revealable()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->disabledOn('edit')
                            ])->columns(1)
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Acceso & Control')
                            ->schema([
                                Forms\Components\Select::make('role')
                                    ->label('Tipo de usuario')
                                    ->required()
                                    ->options([
                                        'administrator' => UserRoleEnum::ADMINISTATOR->value,
                                        'manager' => UserRoleEnum::MANAGER->value,
                                        'seller' => UserRoleEnum::SELLER->value,
                                    ]),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Usuario activo?')
                                    ->onIcon('heroicon-m-user-plus')
                                    ->offIcon('heroicon-m-user-minus')
                                    ->onColor('success')
                                    ->offColor('danger'),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Foto de perfil')
                                    ->avatar()
                                    ->image()
                                    ->imageEditor()
                                    ->circleCropper()
                            ])->columns(1)
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Perfil'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Correo'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Teléfono'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Tipo'),
                Tables\Columns\IconColumn::make('is_active')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

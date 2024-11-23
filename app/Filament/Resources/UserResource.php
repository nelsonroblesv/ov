<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Directory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;

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
                        ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->helperText('Escribe tu nombre completo')
                                    ->required()
                                    ->suffixIcon('heroicon-m-user'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->helperText('Escribe tu correo')
                                    ->required()
                                    ->email()
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->helperText('Ingresa un telefono')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required()
                                    ->tel()
                                    ->suffixIcon('heroicon-m-phone'),
                                    
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
                            Forms\Components\Section::make('Avatar')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Foto de perfil')
                                    ->image()
                                    //->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('user-images')
                            ])->columns(1),
                        
                            Forms\Components\Section::make('Control')
                            ->icon('heroicon-o-adjustments-vertical')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Usuario activo?')
                                    ->onIcon('heroicon-m-user-plus')
                                    ->offIcon('heroicon-m-user-minus')
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ])
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

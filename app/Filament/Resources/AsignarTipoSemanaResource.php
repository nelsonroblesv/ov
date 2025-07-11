<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignarTipoSemanaResource\Pages;
use App\Filament\Resources\AsignarTipoSemanaResource\RelationManagers;
use App\Models\AsignarTipoSemana;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsignarTipoSemanaResource extends Resource
{
    protected static ?string $model = AsignarTipoSemana::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Tipo Semana';
    protected static ?string $breadcrumb = 'Tipo de Semana';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Establecer datos')->schema([
                    Select::make('tipo_semana')
                        ->options([
                            '0' => 'PAR',
                            '1' => 'NON',
                        ])
                        ->required(),

                    Select::make('periodo')
                        ->label('Periodo')
                        ->suffixIcon('heroicon-m-calendar-date-range')
                        ->options([
                            '1' => 'P01',
                            '2' => 'P02',
                            '3' => 'P03',
                            '4' => 'P04',
                            '5' => 'P05',
                            '6' => 'P06',
                            '7' => 'P07',
                            '8' => 'P08',
                            '9' => 'P09',
                            '10' => 'P10',
                            '11' => 'P11',
                            '12' => 'P12',
                            '13' => 'P13'
                        ])
                        ->reactive()
                        ->required(),

                    Select::make('semana')
                        ->label('Semana')
                        ->suffixIcon('heroicon-m-calendar-days')
                        ->options([
                            '1' => 'S1',
                            '2' => 'S2',
                            '3' => 'S3',
                            '4' => 'S4'
                        ])
                        ->reactive()
                        ->required()
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_semana')
                    ->label('Semana Par/Non')
                    ->alignCenter()
                    ->formatStateUsing(fn(string $state): string => [
                        '1' => 'NON',
                        '0' => 'PAR',
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color('info'),


                TextColumn::make('periodo')
                    ->label('Periodo')
                    ->alignCenter()
                    ->badge()
                    ->color('danger'),

                TextColumn::make('semana')
                    ->label('Semana')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListAsignarTipoSemanas::route('/'),
            'create' => Pages\CreateAsignarTipoSemana::route('/create'),
            'edit' => Pages\EditAsignarTipoSemana::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'Administrador';
    }
}

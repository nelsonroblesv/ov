<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CobranzaResource\Pages;
use App\Filament\Resources\CobranzaResource\RelationManagers;
use App\Filament\Resources\CobranzaResource\RelationManagers\PagosRelationManager;
use App\Filament\Resources\PagoRelationManagerResource\RelationManagers\CobranzaResourceRelationManager;
use App\Models\Cobranza;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class CobranzaResource extends Resource
{
    protected static ?string $model = Cobranza::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Cobranza';
    protected static ?string $breadcrumb = "Cobranza";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    Select::make('customer_id')
                        ->label('Cliente')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('saldo_total')
                        ->label('Saldo Total')
                        ->numeric()
                        ->required()
                        ->prefix('$'),

                    Hidden::make('codigo')
                        ->default(fn(Get $get) => 'COB-' . strtoupper(Str::random(5)) . '-' . $get('customer_id')),

                    Hidden::make('created_by')
                        ->default(auth()->id()),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('Folio')->searchable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('saldo_total')
                    ->label('Saldo total')
                    ->money('MXN'),

                TextColumn::make('saldo_pendiente')
                    ->label('Saldo pendiente')
                    ->money('MXN')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('estado')->badge()
                    ->label('Estado')
                    ->colors([
                        'success' => 'Pagado',
                        'warning' => 'Pendiente',
                        'danger'  => 'Vencido',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Pagado',
                        'heroicon-o-exclamation-circle' => 'Pendiente',
                        'heroicon-o-x-circle' => 'Vencido',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')->date()->label('Fecha'),
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
            PagosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCobranzas::route('/'),
            'create' => Pages\CreateCobranza::route('/create'),
            'edit' => Pages\EditCobranza::route('/{record}/edit'),
        ];
    }
}

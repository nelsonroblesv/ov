<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraRutasResource\Pages;
use App\Filament\Resources\BitacoraRutasResource\RelationManagers;
use App\Filament\Resources\BitacoraRutasResource\RelationManagers\CobroRelationManager;
use App\Models\BitacoraCustomers;
use App\Models\BitacoraRutas;
use App\Models\User;
use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BitacoraRutasResource extends Resource
{
    protected static ?string $model = Visita::class;


    protected static ?string $title = 'Bitacora de Visitas';
    protected static ?string $slug = 'bitacora-visitas';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Bitacora de Visitas';
    protected static ?string $breadcrumb = "Bitacora de Visitas";
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de la Visita')
                    ->collapsible()
                    ->schema([
                        Placeholder::make('notas')
                            ->label('ID Nota')
                            ->content(fn($record) => $record?->pedido?->id_nota ?? 'Sin nota'),

                        Placeholder::make('cliente')
                            ->label('Cliente')
                            ->content(fn($record) => $record?->pedido?->customer?->name ?? 'Sin cliente'),

                        TextInput::make('fecha_visita')
                            ->label('Fecha de Visita'),

                        Select::make('tipo_visita')
                            ->label('Tipo de Visita')
                            ->options([
                                'EN' => 'Entrega',
                                'SE' => 'Seguimiento',
                                'SV' => 'Siguiente Visita',
                            ]),

                        Textarea::make('notas')
                            ->label('Notas de la Visita')
                            ->columnSpanFull(),

                        FileUpload::make('evidencias')
                            ->label('Evidencias de la Visita')
                            ->downloadable()
                            ->multiple()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Bitácora de Visitas a Clientes')
            ->description('Registro de visitas realizadas.')
            ->emptyStateHeading('No hay visitas registradas.')
            ->defaultSort('created_at', 'DESC')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha Visita')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pedido.id_nota')
                    ->label('ID Nota')
                    ->badge()
                    ->color('info'),

                TextColumn::make('pedido.customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo_visita')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'EN' => 'Entrega',
                        'SE' => 'Seguimiento',
                        'SV' => 'Siguiente Visita',
                        default => 'Entrega',
                    })
                    ->badge()
                    ->colors([
                        'success' => 'EN',
                        'warning' => 'SE',
                        'danger' => 'SV',
                    ])
                    ->sortable(),

                TextColumn::make('notas'),

            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detalles')
                    ->color('warning'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            CobroRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBitacoraRutas::route('/'),
            'create' => Pages\CreateBitacoraRutas::route('/create'),
            'edit' => Pages\EditBitacoraRutas::route('/{record}/edit'),
            'view' => Pages\ViewBitacoraRutas::route('/{record}'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return BitacoraCustomers::query()->with(['customer.user']);
    }
}

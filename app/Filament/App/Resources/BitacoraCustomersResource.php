<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BitacoraCustomersResource\Pages;
use App\Filament\App\Resources\BitacoraCustomersResource\Pages\ViewBitacoraCustomers;
use App\Models\Visita;
use DragonCode\Contracts\Http\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BitacoraCustomersResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static ?string $title = 'Bitacora de Usuario';
    protected static ?string $slug = 'bitacora-usuario';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Bitacora';
    protected static ?string $breadcrumb = "Bitacora";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de la Visita')
                    ->columns(2)
                    ->schema([
                         Placeholder::make('notas')
                            ->label('ID Nota')
                            ->content(fn($record) => $record?->pedido?->id_nota ?? 'Sin cliente'),

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
                            ])
                            ->required(),

                        Textarea::make('notas')
                            ->label('Notas de la Visita')
                            ->columnSpanFull(),

                        FileUpload::make('evidencias')
                            ->label('Evidencias de la Visita')
                            ->directory('evidencias-visitas')
                            ->downloadable()
                            ->multiple()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Visita::query()
                    ->where('user_id', Auth::id())
            )
            ->heading('Bitácora de Usuario')
            ->description('Registro de visitas realizadas.')
            ->emptyStateHeading('No hay visitas registradas.')
            ->defaultSort('fecha_visita', 'DESC')
            ->columns([
                TextColumn::make('fecha_visita'),

                TextColumn::make('pedido.id_nota')
                    ->label('ID Nota')
                    ->badge()
                    ->color('info'),

                TextColumn::make('pedido.customer.name')->label('Cliente'),

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
                    ]),

                TextColumn::make('notas'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListBitacoraCustomers::route('/'),
            'create' => Pages\CreateBitacoraCustomers::route('/create'),
            'edit' => Pages\EditBitacoraCustomers::route('/{record}/edit'),
            'view' => ViewBitacoraCustomers::route('/{record}/edit')
        ];
    }
}

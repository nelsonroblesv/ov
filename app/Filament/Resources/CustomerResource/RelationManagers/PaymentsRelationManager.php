<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Pagos';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    //protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Administrar Pagos';
    protected static ?string $breadcrumb = "Administrar Pagos";


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del Pago')->schema([

                    Select::make('user_id')
                        ->required()
                        ->label('Registrado por:')
                        ->options(
                            fn() =>
                            User::whereIn('id', function ($query) {
                                $query->select('id')
                                    ->from('users')
                                    ->where('is_active', true)
                                    ->where('role', 'Vendedor')
                                    ->orWhere('username', 'OArrocha')
                                    ->orderBy('name', 'DESC');
                            })->pluck('name', 'id')
                        ),

                    TextInput::make('importe')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('0.00'),

                    Select::make('tipo')
                        ->options([
                            'E' => 'Efectivo',
                            'T' => 'Transferencia',
                            'O' => 'Otro',
                        ])
                        ->default('E')
                        ->required(),

                    DatePicker::make('created_at')
                        ->label('Fecha de pago')
                        ->default(Carbon::now())
                        ->required(),

                    FileUpload::make('voucher')
                        ->label('Comprobante')
                        ->directory('recibos')
                        ->columnSpanFull(),

                    Textarea::make('notas')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('Pagos')
            ->columns([
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('created_at')->label('Fecha de Pago')->date(),
                TextColumn::make('importe')->label('Importe')->alignRight()
                     ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))
                    ->summarize(
                        Sum::make()->label('Total')
                            ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))),
                TextColumn::make('tipo')->label('Tipo de Pago')->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'E' => 'Efectivo',
                        'T' => 'Transferencia',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 'E',
                        'warning' => 'T',
                        'info' => 'O'
                    ]),
                ToggleColumn::make('is_verified')->label('Verificado'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                     ->label('Registrar Pago')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->modalHeading('Registrar Pago'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

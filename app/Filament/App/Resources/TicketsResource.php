<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TicketsResource\Pages;
use App\Filament\App\Resources\TicketsResource\RelationManagers;
use App\Models\Tickets;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketsResource extends Resource
{
    protected static ?string $model = Tickets::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Tickets';
    protected static ?string $navigationLabel = 'Administrar Tickets';
    protected static ?string $breadcrumb = 'Administrar Tickets';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Abrir Ticket')->schema([
                    Hidden::make('from_user_id')->default(fn() => auth()->id()),

                    Select::make('to_user_id')
                        ->label('Para:')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->getSearchResultsUsing(fn(string $searchQuery) => User::where('name', 'like', "%{$searchQuery}%")->limit(50)->get()->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn($value): ?string => User::find($value)?->name)
                        ->options(function () {
                            return User::where('id', '!=', auth()->id())->pluck('name', 'id');
                        })
                        ->native(false)
                        ->required(),
                    Select::make('asunto')
                        ->label('Asunto')
                        ->required()
                        ->options([
                            'Problema de acceso' => 'Problema de acceso',
                            'Cambios en Pedido' => 'Cambios en Pedido',
                            'Problema con el sistema' => 'Problema con el sistema',
                            'Problema con el servicio' => 'Problema con el servicio',
                            'Otros' => 'Otros',
                        ]),
                    Textarea::make('mensaje')
                        ->label('Mensaje')
                        ->rows(5)
                        ->columnSpan('full')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               // TextColumn::make('fromUser.name')->label('Remitente')->searchable()->sortable(),
                TextColumn::make('toUser.name')->label('Destinatario')->searchable()->sortable(),
                TextColumn::make('asunto')->label('Asunto')->searchable()->sortable()->limit(50),
                TextColumn::make('created_at')->label('Solicitado')->dateTime()->sortable(),
                IconColumn::make('estado')->label('Resuelto')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')->sortable(),
                TextColumn::make('updated_at')->label('Cerrado')->dateTime()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
               //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTickets::route('/create'),
            'edit' => Pages\EditTickets::route('/{record}/edit'),
        ];
    }
}

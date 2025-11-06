<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PedidosResource\Pages;
use App\Filament\App\Resources\PedidosResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class PedidosResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_nota'),
                TextColumn::make('customer_id'),
                TextColumn::make('monto'),
                IconColumn::make('is_signed')
                    ->label('Firmado')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('sign')
                    ->label('Entregar y Firmar')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn($record) => ! $record->is_signed)

                    // abre modal
                    ->form([
                        SignaturePad::make('signature')
                            ->label('Firma del Cliente')
                            ->required()
                            ->backgroundColor('#FFF')  // Background color on light mode
                            ->backgroundColorOnDark('#FFF')     // Background color on dark mode (defaults to backgroundColor)
                            ->penColor('#000')                  // Pen color on light mode
                            ->penColorOnDark('#000')            // Pen color on dark mode (defaults to penColor)
                    ])

                    ->action(function ($record, array $data) {
                        $record->update([
                            'signature' => $data['signature'],
                            'is_signed' => true,
                        ]);

                        Notification::make()
                            ->title('Firma registrada')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedidos::route('/create'),
            'edit' => Pages\EditPedidos::route('/{record}/edit'),
        ];
    }
}

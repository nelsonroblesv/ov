<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MisRutasResource\Pages;
use App\Filament\App\Resources\MisRutasResource\RelationManagers;
use App\Models\GestionRutas;
use App\Models\MisRutas;
use App\Models\Pedido;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MisRutasResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
     protected static ?string $slug = 'ruta-semanal';
    protected static ?string $navigationGroup = 'Rutas & Visitas';
    protected static ?string $navigationLabel = 'Ruta Semanal';
    protected static ?string $breadcrumb = "Ruta Semanal";
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
      return $table
            ->query(
                Pedido::query()
                    ->where('distribuidor', Auth::id())
                    ->where('estado_general', 'abierto')
                    ->whereBetween('fecha_entrega', [
                        Carbon::now()->startOfWeek(Carbon::MONDAY),
                        Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)
                    ])
            )
            ->recordUrl(null)
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas. Utiliza los controles disponibles para organizar tus rutas.
            Puedes arrastrar y soltar para cambiar el orden de las rutas. Seleccionar 
            el dia, filtrar por tipo de semana y buscar por nombre de cliente.')
            ->reorderable('orden')
            ->columns([
             
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
               
            ])
            ->filters([
               
            ])
            ->actions([])
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
            'index' => Pages\ListMisRutas::route('/'),
            'create' => Pages\CreateMisRutas::route('/create'),
            'edit' => Pages\EditMisRutas::route('/{record}/edit'),
        ];
    }
}

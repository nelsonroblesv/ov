<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraProspeccionResource\Pages;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use App\Models\Regiones;
use App\Models\User;
use DragonCode\Contracts\Http\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HtmlSanitizer\Visitor\Node\TextNode;

class BitacoraProspeccionResource extends Resource
{
    protected static ?string $model = BitacoraCustomers::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Registros de Actividad';
    protected static ?string $breadcrumb = 'Bitacora';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Registros de Actividad')
            ->description('Listado de los registros de actividad de los Clientes y Prospectos')
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('customers.user.name')->label('Vendedor')->searchable()->sortable(),
                TextColumn::make('customers.name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('tipo_visita')->label('Visita')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn(string $state): string => [
                        'EN' => 'Entrega',
                        'CE' => 'Cerrado',
                        'RE' => 'Regular',
                        'PR' => 'Prospeccion',
                    ][$state] ?? 'Otro'),
                TextColumn::make('notas')->label('Notas')->searchable(),

                TextColumn::make('customers.regiones.name')->label('Region')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customers.zonas.nombre_zona')->label('Zona')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('foto_entrega')->label('Entrega')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_antes')->label('Stock Antes')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_despues')->label('Stock Despues')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_lugar_cerrado')->label('Cerrado')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_stock_regular')->label('Regular')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_evidencia_prospectacion')->label('Prospeccion')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ImageColumn::make('testigo_1')->label('Testigo 1')->searchable()->toggleable(isToggledHiddenByDefault: true),
                //ImageColumn::make('testigo_2')->label('Testigo 2')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Registro')->date()->sortable(),
                IconColumn::make('show_video')->label('Video Testimonio')->boolean()->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                SelectFilter::make('user_id')->label('Usuario')
                    ->options(User::pluck('name', 'id')),

                SelectFilter::make('tipo_visita')->label('Tipo Visita')
                    ->options([
                        'EN' => 'Entrega',
                        'CE' => 'Cerrado',
                        'RE' => 'Regular',
                        'PR' => 'Prospección',
                    ])
            ])
            ->actions([
                //   Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                */]);
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
            'index' => Pages\ListBitacoraProspeccions::route('/'),
            'create' => Pages\CreateBitacoraProspeccion::route('/create'),
            'edit' => Pages\EditBitacoraProspeccion::route('/{record}/edit'),
        ];
    }
}

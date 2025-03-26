<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BitacoraCustomersResource\Pages;
use App\Filament\App\Resources\BitacoraCustomersResource\Pages\ViewBitacoraCustomers;
use App\Filament\App\Resources\BitacoraCustomersResource\RelationManagers;
use App\Models\AsignarTipoSemana;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BitacoraCustomersResource extends Resource
{
    protected static ?string $model = BitacoraCustomers::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Registros de Actividad';
    protected static ?string $breadcrumb = 'Bitacora';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Registro de actividad')
                ->schema([
                    Select::make('customers_id')->label('Nombre de Cliente o Identificador')
                        ->placeholder('Visitas para hoy:')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->options(function()
                        {
                            $hoy = strtoupper(Carbon::now()->setTimezone('America/Merida')->format('D'));
                            $dias = [
                                'MON' => 'Lun',
                                'TUE' => 'Mar',
                                'WED' => 'Mie',
                                'THU' => 'Jue',
                                'FRI' => 'Vie',
                                'SAT' => 'Sab',
                                'SUN' => 'Dom',
                            ];
                            $diaActual = $dias[$hoy];
                            $user = auth()->id();
                            $tipoSemanaSeleccionado = AsignarTipoSemana::value('tipo_semana');
                            $valores = [
                                '0' => 'PAR',
                                '1' => 'NON',
                            ];
                            $semana = $valores[$tipoSemanaSeleccionado];
                           
                            return Customer::whereIn('zonas_id', function ($query) use ($diaActual, $user, $semana) {
                                $query->select('id')
                                      ->from('zonas')
                                      ->where('dia_zona', $diaActual) 
                                      ->where('tipo_semana', $semana)
                                      ->where('user_id', $user);
                            })->pluck('name', 'id');
                        }),

                    Toggle::make('show_video')->label('Se presentó Video Testimonio')
                        ->onIcon('heroicon-m-play')
                        ->offIcon('heroicon-m-x-mark')
                        ->onColor('success')
                        ->offColor('danger'),

                    MarkdownEditor::make('notas')->label('Notas')->required()->columnSpanFull(),
                    Section::make('Testigos')->schema([
                        FileUpload::make('testigo_1')->label('Evidencias')->nullable()
                            ->placeholder('Tomar o cargar fotos')
                            ->multiple()
                            ->directory('bitacora-testigos'),
                       /* FileUpload::make('testigo_2')->label('Foto 2')->nullable()
                            ->placeholder('Tomar o cargar Foto')
                            ->directory('bitacora-testigos')*/
                    ])->columnSpanFull()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            $query->whereHas('customers', function (Builder $query) {
                $query->where('user_id', auth()->user()->id);
            });
        })
        ->defaultSort('created_at', 'desc')
        ->heading('Registros de Actividad')
        ->description('Listado de registros de visitas realizados a Clientes y Prospección')
            ->columns([
                TextColumn::make('customers.user.name')->label('Registrado')->searchable()->sortable(),
                TextColumn::make('customers.name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('customers.regiones.name')->label('Regiones')->searchable()->sortable(),
                TextColumn::make('customers.zonas.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('notas')->label('Notas')->searchable(),
                ImageColumn::make('testigo_1')->label('Testigo 1')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('testigo_2')->label('Testigo 2')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Registro')->dateTime()->sortable(),
               // IconColumn::make('show_video')->label('Video Testimonio')->boolean()->alignCenter()
            ])
            ->filters([
            ])
            ->actions([
             //   Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                */
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
            'index' => Pages\ListBitacoraCustomers::route('/'),
            'create' => Pages\CreateBitacoraCustomers::route('/create'),
            'edit' => Pages\EditBitacoraCustomers::route('/{record}/edit'),
            'view' => ViewBitacoraCustomers::route('/{record}/edit')
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormulariosResource\Pages;
use App\Filament\Resources\FormulariosResource\RelationManagers;
use App\Models\Eventos;
use App\Models\Formularios;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\HeaderActions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class FormulariosResource extends Resource
{
    protected static ?string $model = Formularios::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'OValle';
    protected static ?string $navigationLabel = 'Registros';
    protected static ?string $breadcrumb = "Registros";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos de Usuario')->schema([
                    Select::make('eventos_id')->label('Evento')
                        ->options(Eventos::pluck('nombre', 'id')),
                    DatePicker::make('fecha_registro')->label('Fecha de Registro'),
                    TextInput::make('nombre')->label('Nombre completo'),
                    TextInput::make('ciudad')->label('Ciudad'),
                    TextInput::make('email')->label('Correo electronico')->email(),
                    TextInput::make('telefono')->label('Telefono')->tel(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_registro')->date(),
                Tables\Columns\TextColumn::make('nombre')->searchable(),
                Tables\Columns\TextColumn::make('ciudad')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('telefono')->searchable(),
            ])
            ->headerActions([
                ActionsAction::make('Cargar CSV')
                    ->icon('heroicon-o-document')
                    ->form([
                        FileUpload::make('csv_file')
                            ->label('Selecciona un archivo CSV')
                            ->acceptedFileTypes(['text/csv'])
                            ->directory('uploads')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Obtener la ruta del archivo
                        $filePath = public_path("uploads/{$data['csv_file']}");
                        //dd($filePath);
                        //dd(Storage::disk('local')->exists($data['csv_file']), storage_path("app/public/{$data['csv_file']}"));

                        if (!file_exists($filePath)) {
                            Notification::make()
                                ->title('Error al procesar el archivo')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Leer el archivo CSV
                        $csv = Reader::createFromPath($filePath, 'r');
                        $csv->setHeaderOffset(0);

                        foreach ($csv as $record) {
                            $validated = Validator::make($record, [
                                'fecha_registro' => 'required|date',
                                'nombre'         => 'required|string|max:255',
                                'ciudad'         => 'nullable|string|max:255',
                                'email'          => 'nullable|email|max:255',
                                'telefono'       => 'nullable|string|max:20',
                            ])->validate();

                            Formularios::firstOrCreate(
                                [
                                    'email'    => $validated['email'],
                                    'telefono' => $validated['telefono'],
                                ],
                                [
                                    'fecha_registro' => $validated['fecha_registro'],
                                    'nombre'         => $validated['nombre'],
                                    'ciudad'         => $validated['ciudad'],
                                ]
                            );
                        }

                        // Eliminar archivo después de procesarlo
                        Storage::disk('public')->delete($data['csv_file']);

                        Notification::make()
                            ->title('Importación exitosa')
                            ->success()
                            ->send();
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormularios::route('/'),
            'create' => Pages\CreateFormularios::route('/create'),
            'edit' => Pages\EditFormularios::route('/{record}/edit'),
        ];
    }
}

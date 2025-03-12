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
use Filament\Tables\Columns\ColorColumn;
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
        ->defaultSort('created_at', 'desc')
            ->description('Lista de personas registradas por evento.')
            ->columns([
                ColorColumn::make('eventos.color')->label('Evento')->alignCenter(),
                TextColumn::make('fecha_registro')->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('ciudad')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('telefono')->searchable(),
            ])
            ->headerActions([
                ActionsAction::make('load_csv')
                    ->label('Cargar archivo CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([
                        FileUpload::make('csv_file')
                            ->label('Subir Archivo CSV')
                            ->directory('uploads')
                            ->acceptedFileTypes(['text/csv']) 
                            ->maxSize(2048)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        Storage::delete('public/uploads/01JP1972VDWKNH2CZ9QF6RJCQ9.csv');
                       
                        $filePath = Storage::disk('public')->path($data['csv_file']);
                        
                        if (!file_exists($filePath)) {
                            Notification::make()
                                ->title('Error')
                                ->body('El archivo no se encontró.')
                                ->danger()
                                ->send();
                            return;
                        }
                       
                        $csv = Reader::createFromPath($filePath, 'r');
                        $csv->setHeaderOffset(0); 

                        $registrosImportados = 0;
                        foreach ($csv as $record) {
                           
                            if (!Formularios::where('email', $record['email'])->orWhere('telefono', $record['telefono'])->exists()) {
                                Formularios::create([
                                    'fecha_registro' =>  str_replace('@', '', $record['fecha_registro']),
                                    'nombre' => $record['nombre'],
                                    'ciudad' => $record['ciudad'],
                                    'email' => $record['email'],
                                    'telefono' => $record['telefono'],
                                ]);
                                $registrosImportados++;
                            }
                        }

                        Notification::make()
                            ->title('Importación Completa')
                            ->body("Se importaron {$registrosImportados} registros.")
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

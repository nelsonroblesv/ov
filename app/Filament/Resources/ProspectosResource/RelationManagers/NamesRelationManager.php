<?php

namespace App\Filament\Resources\ProspectosResource\RelationManagers;

use App\Models\BitacoraProspeccion;
use App\Models\Prospectos;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class NamesRelationManager extends RelationManager
{
    protected static string $relationship = 'names';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Registro de actividad')
                    ->schema([
                        Toggle::make('show_video')->label('Se presentó Video Testimonio')
                            ->onIcon('heroicon-m-play')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),

                        MarkdownEditor::make('notas')->label('Notas')->required()->columnSpanFull(),
                        Section::make('Testigos')->schema([
                            FileUpload::make('testigo_1')->label('Foto 1')->nullable()
                                ->directory('bitacora-testigos'),
                            FileUpload::make('testigo_1')->label('Foto 2')->nullable()
                                ->directory('bitacora-testigos')
                        ])->columns(2)
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->heading('Registros')
            ->description('Informacion de visitas realizadas')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('prospectos.name')->label('Identificador'),
                TextColumn::make('created_at')->label('Registro'),
                TextColumn::make('notas')->label('Notas'),
                IconColumn::make('show_video')->label('Video Testimonio')
                ->boolean()
                ->trueIcon('heroicon-o-play-circle')
                ->falseIcon('heroicon-o-clock')
                ->alignCenter()
             ]) ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar en Bitacora')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading('Agregar registro a la bitacora')
                    ->modalSubmitActionLabel('Agregar')
                    // ->createAnother(false)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Registro agregado')
                            ->body('Se ha creado un nuevo registro en la Bitacora.')
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->color('success')
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

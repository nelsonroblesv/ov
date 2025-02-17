<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BitacoraProspeccionUserResource\Pages;
use App\Models\BitacoraProspeccion;
use App\Models\Prospectos;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BitacoraProspeccionUserResource extends Resource
{
    protected static ?string $model = BitacoraProspeccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Bitacora de Prospeccion';
    protected static ?string $breadcrumb = "Bitacora";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Registro de actividad')
                ->schema([
                    Select::make('prospectos_id')->label('Idenficador')
                        ->options(Prospectos::query()->where('user_id', auth()->user()->id)->pluck('name', 'id'))
                        ->required()
                        ->preload()
                        ->searchable(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('prospectos', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                });
            })
            ->defaultSort('created_at', 'desc')
            
            ->columns([
                TextColumn::make('prospectos.user.name')->label('Registrado')->searchable()->sortable(),
                TextColumn::make('prospectos.name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('notas')->label('Notas')->searchable(),
                ImageColumn::make('testigo_1')->label('Testigo 1')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('testigo_2')->label('Testigo 2')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Registro')->date()->sortable(),
                IconColumn::make('show_video')->label('Video Testimonio')->boolean()->alignCenter()
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
            'index' => Pages\ListBitacoraProspeccionUsers::route('/'),
            'create' => Pages\CreateBitacoraProspeccionUser::route('/create'),
            'edit' => Pages\EditBitacoraProspeccionUser::route('/{record}/edit'),
        ];
    }
}

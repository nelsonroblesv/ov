<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraProspeccionResource\Pages;
use App\Filament\Resources\BitacoraProspeccionResource\RelationManagers;
use App\Models\BitacoraCustomers;
use App\Models\BitacoraProspeccion;
use App\Models\Customer;
use App\Models\Prospectos;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->schema([
                Section::make('Registro de actividad')
                ->schema([
                    Select::make('customers_id')->label('Nombre de Cliente o Identificador')
                        ->options(Customer::query()->where('user_id', auth()->user()->id)->pluck('name', 'id'))
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

        ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('customers.user.name')->label('Registrado')->searchable()->sortable(),
                TextColumn::make('customers.name')->label('Identificador')->searchable()->sortable(),
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
             //   Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBitacoraProspeccions::route('/'),
            'create' => Pages\CreateBitacoraProspeccion::route('/create'),
            'edit' => Pages\EditBitacoraProspeccion::route('/{record}/edit'),
        ];
    }
}

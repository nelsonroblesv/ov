<?php

namespace App\Filament\Resources\ProspectosResource\Widgets;

use App\Filament\Resources\ProspectosResource;
use App\Filament\Resources\ProspectosResource\Pages\CreateProspectos;
use App\Filament\Resources\ProspectosResource\Pages\EditProspectos;
use App\Filament\Resources\ProspectosResource\Pages\ListProspectos;
use App\Filament\Resources\ProspectosResource\Pages\ViewProspectos;
use App\Models\Customer;
use App\Models\Prospectos;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Closure;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ProspectosMapWidget extends MapTableWidget
{
	protected static ?string $heading = 'Prospeccion';
	protected static ?int $sort = 1;
	protected static ?string $pollingInterval = null;
	protected static ?bool $clustering = true;
	protected static ?string $mapId = 'prospectos-map';
	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		return Prospectos::query()->latest();
	}
	
	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Listado de Prospectos';
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
			BadgeColumn::make('tipo_prospecto')->label('Tipo')
				->colors([
					'danger' => 'Posible',
					'warning' => 'Prospecto'
				])
				->icons([
					'heroicon-o-map' => 'Posible',
                    'heroicon-o-star' => 'Prospecto'
				]),
			TextColumn::make('name')->label('Identificador')->searchable()->sortable(),
			TextColumn::make('full_address')->label('Direccion')->searchable()->sortable(),
			TextColumn::make('email')->label('Correo')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('notes')->label('Notas')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('created_at')->label('Registro')->dateTime()->searchable()->sortable()
		];
	}

	protected function getTableFilters(): array
	{
		return [
			SelectFilter::make('tipo_prospecto')
                ->options([
                    'Posible' => 'Posible',
                    'Prospecto' => 'Prospecto'
                ]),	
		];
	}

	protected function getTableActions(): array
	{
		return [
			ActionGroup::make([
				ViewAction::make('view')
					->url(fn (Prospectos $record): string => ProspectosResource::getUrl('view', ['record' => $record])),

				EditAction::make('edit')
					->url(fn (Prospectos $record): string => ProspectosResource::getUrl('edit', ['record' => $record])),
				
				GoToAction::make()->zoom(14)->label('Ver en Mapa')->color('success'),

				Action::make('transfer')
					->label('Transferir')
					->requiresConfirmation()
					->icon('heroicon-o-arrows-up-down')
					->color('info')
					->modalHeading('Transferir Prospecto')
					->modalDescription('Estas seguro que deseas transferir este Prospecto como Cliente? Esta acción no se puede deshacer.')
					->action(function (Prospectos $record) {
					if (!$record->phone) {
							Notification::make()
								->title('Error')
								->body('Solo puedes transferir Prospectos que cuenten con informacion de contacto.')
								->danger()
								->color('danger')
								->send();

							return;
						}

						if (Customer::where('phone', $record->phone)->exists()) {
							Notification::make()
								->title('Error')
								->body('El numero de telefono indicado ya esta asociado con un Cliente existente.')
								->danger()
								->color('danger')
								->send();

							return;
						}

						$clienteData = $record->toArray();
						unset($clienteData['id'], $clienteData['created_at'], $clienteData['updated_at']);
						Customer::create($clienteData);
						$record->delete();

						Notification::make()
							->title('Prospecto transferido')
							->body('El prospecto ha sido transferido como Cliente.')
							->success()
							->send();
					}),

				ActionsDeleteAction::make('delete')
					->successNotification(
						Notification::make()
							->success()
							->title('Prospecto eliminado')
							->body('El Prospecto ha sido eliminado  del sistema.')
							->icon('heroicon-o-trash')
							->iconColor('danger')
							->color('danger')
					)
					->modalHeading('Borrar Prospecto')
					->modalDescription('Estas seguro que deseas eliminar este Prospecto? Esta acción no se puede deshacer.')
					->modalSubmitActionLabel('Si, eliminar'),
					]),		
		];
	}

	protected function getTableBulkActions(): array
	{
		return [
		
			DeleteBulkAction::make()
				->successNotification(
					Notification::make()
						->success()
						->title('Registros eliminados')
						->body('Los registros seleccionados han sido eliminados.')
						->icon('heroicon-o-trash')
						->iconColor('danger')
						->color('danger')
				)
				->modalHeading('Borrar Prospectos')
				->modalDescription('Estas seguro que deseas eliminar los Prospectos seleccionados? Esta acción no se puede deshacer.')
				->modalSubmitActionLabel('Si, eliminar'),
		];
	}

	protected function getData(): array
	{
		$locations = $this->getRecords();

		if ($locations->isEmpty() || $locations->count() == 0) {

			$data[] = [
				'location' => [
					'lat' => 19.8386943,
					'lng' => -90.4982317,
				]
			];
			return $data;
		}

		$data = [];

		foreach ($locations as $location) {
			$user = User::find($location->user_id)->name;
			$data[] = [
				'location' => [
					'lat' => $location->latitude ? round(floatval($location->latitude), static::$precision) : 0,
					'lng' => $location->longitude ? round(floatval($location->longitude), static::$precision) : 0,
				],
				'id'      => $location->id,
				'label'    => $user . ' -> ' . $location->name,
				'icon' => [
					'url' => url('images/location.png'),
					'type' => 'png',
					'scale' => [35, 35],
				],
			];
		}
		return $data;
	}


	public static function getPages(): array
    {
        return [
            'index' => ListProspectos::route('/'),
            'create' => CreateProspectos::route('/create'),
            'edit' => EditProspectos::route('/{record}/edit'),
            'view' => ViewProspectos::route('/{record}'),
        ];
    }
}

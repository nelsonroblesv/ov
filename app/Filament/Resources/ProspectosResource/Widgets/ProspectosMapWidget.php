<?php

namespace App\Filament\Resources\ProspectosResource\Widgets;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\ProspectosResource;
use App\Filament\Resources\ProspectosResource\Pages\CreateProspectos;
use App\Filament\Resources\ProspectosResource\Pages\EditProspectos;
use App\Filament\Resources\ProspectosResource\Pages\ListProspectos;
use App\Filament\Resources\ProspectosResource\Pages\ViewProspectos;
use App\Models\Customer;
use App\Models\Prospectos;
use App\Models\User;
use App\Models\Zonas;
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
	protected static ?string $mapId = 'user-prospectos-map';
	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		return Customer::query()
			->whereIn('tipo_cliente', ['PO', 'PR'])
			->orderBy('created_at', 'desc');
	}

	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Listado de Prospectos';
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
			TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
			TextColumn::make('zonas.nombre_zona')->label('Zona')->searchable()->sortable(),
			TextColumn::make('tipo_cliente')->label('Tipo')->badge()
				->colors([
					'danger' => 'PO',
					'warning' => 'PR'
				])
				->icons([
					'heroicon-o-map' => 'PO',
					'heroicon-o-star' => 'PR'
				])
				->formatStateUsing(fn(string $state): string => [
					'PO' => 'Posible',
					'PR' => 'Prospecto',
				][$state] ?? 'Otro'),
			TextColumn::make('simbolo')->label('Simbolo')->badge()
				->colors([
					'black',/*
					'custom' => 'SB',
					'success' => 'BB',
					'success' => 'UN',
					'success' => 'OS',
					'success' => 'CR',
					'success' => 'UB',
					'success' => 'NC'*/
				])
				->icons([
					'heroicon-o-scissors' => 'SB',
					'heroicon-o-building-storefront' => 'BB',
					'heroicon-o-hand-raised' => 'UN',
					'heroicon-o-rocket-launch' => 'OS',
					'heroicon-o-x-mark' => 'CR',
					'heroicon-o-map-pin' => 'UB',
					'heroicon-o-exclamation-triangle' => 'NC'
				])
				->formatStateUsing(fn(string $state): string => [
					'SB' => 'Salón de Belleza',
					'BB' => 'Barbería',
					'UN' => 'Salón de Uñas',
					'OS' => 'OSBERTH',
					'CR' => 'Cliente Pedido Rechazado',
					'UB' => 'Ubicación en Grupo',
					'NC' => 'Ya no compran'
				][$state] ?? 'Otro'),
			TextColumn::make('name')->label('Identificador')->searchable()->sortable(),
			TextColumn::make('full_address')->label('Direccion')->searchable()->sortable(),
			TextColumn::make('email')->label('Correo')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('extra')->label('Notas')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('created_at')->label('Registro')->dateTime()->searchable()->sortable()
		];
	}

	protected function getTableFilters(): array
	{
		return [
			SelectFilter::make('tipo_cliente')
				->label('Tipo de Cliente')
				->multiple()
				->options([
					'PR' => 'Prospecto',
					'PO' => 'Posible'
				]),
			SelectFilter::make('user_id')
				->label('Vendedor')
				->multiple()
				->options(User::pluck('name', 'id')->toArray()),

			SelectFilter::make('zonas_id')
				->label('Zonas')
				->multiple()
				->options(Zonas::pluck('nombre_zona', 'id')->toArray()),
			MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [
			ActionGroup::make([
				ViewAction::make('view')
					->url(fn(Customer $record): string => ProspectosResource::getUrl('view', ['record' => $record])),

				EditAction::make('edit')
					->url(fn(Customer $record): string => ProspectosResource::getUrl('edit', ['record' => $record])),

				Action::make('transfer')
					->label('Transferir')
					->requiresConfirmation()
					->icon('heroicon-o-arrows-up-down')
					->color('info')
					->modalHeading('Transferir a Cliente')
					->modalDescription('Estas seguro que deseas transferir como Cliente? Esta acción no se puede deshacer.')
					->action(function (Customer $record) {
						/*
						if (!$record->phone) {
							Notification::make()
								->title('Error')
								->body('Solo puedes transferir Prospectos que cuenten con informacion de contacto. Por lo menos el numero de telefono.')
								->danger()
								->color('danger')
								->send();

							return;
						}
						if (Customer::where('phone', $record->phone)
							->where('id', '!=', $record->id)
							->exists()
						) {
							Notification::make()
								->title('Error')
								->body('El número de teléfono indicado ya está asociado con un Cliente existente.')
								->danger()
								->color('danger')
								->send();
							return;
						}
						*/
						$record->update(['tipo_cliente' => 'PV']);

						$recipient = User::where('role', 'Administrador')->get();
						$username =  User::find($record['user_id'])->name;

						Notification::make()
							->title('Prospecto transferido')
							->body('Se ha transferido un nuevo cliente Punto de Venta.')
							->icon('heroicon-o-information-circle')
							->iconColor('info')
							->color('info')
							->send();

						Notification::make()
							->title('Prospecto transferido')
							->body('El vendedor ' . $username . ' ha transferido a ' . $record->name . ' como nuevo cliente Punto de Venta.')
							->icon('heroicon-o-information-circle')
							->iconColor('info')
							->color('info')
							->sendToDatabase($recipient);
					})

			])
		];
		/*
		return [
			ActionGroup::make([
				/*ViewAction::make('view')
					->url(fn (Prospectos $record): string => ProspectosResource::getUrl('view', ['record' => $record])),

				
				
				GoToAction::make()->zoom(14)->label('Ver en Mapa')->color('success'),
				/*
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
*/
	}

	protected function getTableBulkActions(): array
	{
		return [
			/*
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
				*/];
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

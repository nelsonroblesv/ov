<?php

// TO ADMIN

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\User;
use App\Models\Zonas;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class CustomersMap extends MapTableWidget
{
	protected static ?string $heading = 'Clientes';
	protected static ?int $sort = 1;
	protected static ?string $pollingInterval = null;
	protected static ?bool $clustering = true;
	protected static ?string $mapId = 'customers-map';
	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		return Customer::query()
			->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
			->orderBy('created_at', 'desc');
	}

	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Listado de Clientes';
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('altaUser.name')->label('Registrado por:')->searchable()->sortable(),
			TextColumn::make('user.name')->label('Asignado a:')->searchable()->sortable(),
			TextColumn::make('regiones.name')->label('Region')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
			TextColumn::make('zona.nombre_zona')->label('Zona')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
			TextColumn::make('tipo_cliente')->label('Tipo')->badge()->toggleable(isToggledHiddenByDefault: false)
				->colors([
					'success' => 'PV',
					'danger' => 'RD',
					'custom_black' => 'BK',
					'custom_gray' => 'SL'
				])
				->icons([
					'heroicon-o-building-storefront' => 'PV',
					'heroicon-o-user' => 'RD',
					'heroicon-o-star' => 'BK',
					'heroicon-o-sparkles' => 'SL'
				])
				->formatStateUsing(fn(string $state): string => [
					'PV' => 'Punto Venta',
					'RD' => 'Red',
					'BK' => 'Black',
					'SL' => 'Silver',
				][$state] ?? 'Otro'),
			TextColumn::make('name')->label('Cliente')->searchable()->sortable(),
			TextColumn::make('paquete_inicio.nombre')->label('Paquete Inicio')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
			TextColumn::make('simbolo')->label('Simbolo')->badge()->toggleable(isToggledHiddenByDefault: false)
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
					'heroicon-o-exclamation-triangle' => 'NC',
					'heroicon-o-sparkles' => 'EU',
					'heroicon-o-home-modern' => 'SYB'
				])
				->formatStateUsing(fn(string $state): string => [
					'SB' => 'Salón de Belleza',
					'SYB' => 'Salón y Barbería',
					'EU' => 'Estética Unisex',
					'BB' => 'Barbería',
					'UN' => 'Salón de Uñas',
					'OS' => 'OSBERTH',
					'CR' => 'Cliente Pedido Rechazado',
					'UB' => 'Ubicación en Grupo',
					'NC' => 'Ya no compran'
				][$state] ?? 'Otro'),
			TextColumn::make('full_address')->label('Direccion')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
			TextColumn::make('email')->label('Correo')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('extra')->label('Notas')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('created_at')->label('Registro')->dateTime()->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false)
		];
	}

	protected function getTableFilters(): array
	{
		return [
			SelectFilter::make('tipo_cliente')
				->label('Tipo de Cliente')
				->multiple()
				->options([
					'PV' => 'Punto de Venta',
					'RD' => 'Red',
					'BK' => 'Black',
					'SL' => 'Silver'
				]),
			SelectFilter::make('user_id')
				->label('Vendedor')
				->multiple()
				->options(User::pluck('name', 'id')->toArray()),

			SelectFilter::make('zonas_id')
				->label('Zonas')
				->multiple()
				->options(Zonas::pluck('nombre_zona', 'id')->toArray()),

			SelectFilter::make('paquete_inicio_id')
				->label('Paquete Inicio')
				->multiple()
				->options(PaquetesInicio::pluck('nombre', 'id')->toArray()),
			MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		//	return [];
		return [
			ActionGroup::make([
				ViewAction::make('view')
					->url(fn(Customer $record): string => CustomerResource::getUrl('view', ['record' => $record])),

				EditAction::make('edit')
					->url(fn(Customer $record): string => CustomerResource::getUrl('edit', ['record' => $record])),

				ActionsDeleteAction::make()
					->successNotification(
						Notification::make()
							->success()
							->title('Cliente borrado')
							->body('El Cliente ha sido eliminado del sistema.')
							->icon('heroicon-o-trash')
							->iconColor('danger')
							->color('danger')
					)
					->modalHeading('Borrar Cliente')
					->modalDescription('Estas seguro que deseas eliminar este Cliente? Esta acción no se puede deshacer.')
					->modalSubmitActionLabel('Si, eliminar'),

			])
		];
		//GoToAction::make()->zoom(14)->label('Ver en Mapa')->color('success'),
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
			'index' => ListCustomers::route('/'),
			'create' => CreateCustomer::route('/create'),
			'edit' => EditCustomer::route('/{record}/edit'),
			'view' => ViewCustomer::route('/{record}'),
		];
	}
}

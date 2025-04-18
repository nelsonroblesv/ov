<?php

// to USERS

namespace App\Filament\Resources\CustomerUserResource\Widgets;

use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class CustomersMap extends MapTableWidget
{
	protected static ?string $heading = 'Clientes';
	protected static ?int $sort = 1;
	protected static ?string $pollingInterval = null;
	protected static ?bool $clustering = true;
	protected static ?string $mapId = 'user-customers-map';
	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		return Customer::query()->where('user_id', auth()->id())
			->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
			->where('is_active', true)
			->orderBy('created_at', 'desc');
	}

	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Listado de Prospectos';
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('name')->label('Identificador')->searchable()->sortable(),
			TextColumn::make('tipo_cliente')->label('Tipo')->badge()
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
			//TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
			TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
			TextColumn::make('zona.nombre_zona')->label('Zona')->searchable()->sortable(),
			TextColumn::make('zona.tipo_semana')->label('Semana')->searchable()->sortable()
				->badge()
				->colors([
					'success' => 'PAR',
					'danger' => 'NON',
				])
				->icons([
					'heroicon-o-arrow-long-down' => 'PAR',
					'heroicon-o-arrow-long-up' => 'NON',
				]),
			TextColumn::make('zona.dia_zona')->label('Dia')->searchable()->sortable()
				->badge()
				->colors([
					'info' => 'Lun',
					'warning' => 'Mar',
					'danger' => 'Me',
					'success' => 'Jue',
					'custom_light_blue' => 'Vie',
				]),
			TextColumn::make('paquete_inicio.nombre')->label('Paquete Inicio')->searchable()->sortable(),
			TextColumn::make('simbolo')->label('Simbolo')->badge()
				->colors([
					'black',
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
			SelectFilter::make('tipo_cliente')
				->label('Tipo de Cliente')
				->multiple()
				->options([
					'PV' => 'Punto de Venta',
					'RD' => 'Red',
					'BK' => 'Black',
					'SL' => 'Silver'
				]),

			SelectFilter::make('paquete_inicio_id')
				->label('Paquete Inicio')
				->multiple()
				->options(PaquetesInicio::pluck('nombre', 'id')->toArray()),

			MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [];

	}

	protected function getTableBulkActions(): array
	{
		return [];
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

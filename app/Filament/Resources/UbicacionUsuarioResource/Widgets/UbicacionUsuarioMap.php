<?php

namespace App\Filament\Resources\UbicacionUsuarioResource\Widgets;

use App\Models\UbicacionUsuario;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UbicacionUsuarioMap extends MapTableWidget
{
	protected static ?string $heading = 'Ubicaciones de Usuarios';
	protected static ?int $sort = 1;
	protected static ?string $pollingInterval = null;
	protected static ?bool $clustering = true;
	protected static ?string $mapId = 'user-location-map';
	protected int|string|array $columnSpan = 'full';


	protected function getTableQuery(): Builder
	{
		return UbicacionUsuario::query()->latest();
        
	}

	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Ubicaciones';
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('user.name')->label('Usuario'),
			ImageColumn::make('user.icon_url')->label('Usuario'),
			TextColumn::make('created_at')->label('Registro'),
			IconColumn::make('user_id')->label('Ubicación')->alignCenter()
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitud},{$record->longitud}", true)
                    ->openUrlInNewTab(),
		];
	}

	protected function getTableFilters(): array
	{
		return [
			RadiusFilter::make('location')
				->section('Radius Filter')
				->selectUnit(),
            MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\ViewAction::make(),
			Tables\Actions\EditAction::make(),
			Tables\Actions\DeleteAction::make(),
			GoToAction::make()
				->zoom(14),
			RadiusAction::make(),
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
			$user = $location->user;
			$data[] = [
				'location' => [
					'lat' => $location->latitud ? round(floatval($location->latitud), static::$precision) : 0,
					'lng' => $location->longitud ? round(floatval($location->longitud), static::$precision) : 0,
				],
				'id'      => $location->id,
				'label'    => $user ? $user->name : 'Usuario no encontrado',
				'icon' => [
					'url' => url($user ? $user->icon_url : 'images/location.png'),
					'type' => 'png',
					'scale' => [35, 35],
				],
			];
		}
		return $data;
	}
}

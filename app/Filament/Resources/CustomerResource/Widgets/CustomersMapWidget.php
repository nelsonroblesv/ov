<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Filament\App\Resources\CustomerResource;
use App\Filament\App\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\App\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\App\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\App\Resources\CustomerResource\Pages\ViewCustomer;
use App\Filament\Resources\ProspectosResource;
use App\Filament\Resources\CustomerResource\Pages\CreateProspectos;
use App\Filament\Resources\CustomerResource\Pages\EditProspectos;
use App\Filament\Resources\CustomerResource\Pages\ListProspectos;
use App\Filament\Resources\CustomerResource\Pages\ViewProspectos;
use App\Models\Customer;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CustomersMapWidget extends MapTableWidget
{
	protected static ?string $heading = 'Clientes';
	protected static ?int $sort = 1;
	protected static ?string $pollingInterval = null;
	protected static ?bool $clustering = true;
	protected static ?string $mapId = 'clientes-map';
	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		return Customer::query()->latest();
	}
	
	protected function getTableDescription(): string|Htmlable|null
	{
		return 'Listado de Clientes';
	}

	protected function getTableColumns(): array
	{
		return [
			ImageColumn::make('avatar')->label('Avatar'),
			TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
			TextColumn::make('tipo_cliente')->label('Tipo')->searchable()->sortable()->badge()
				->colors([
					'primary' => 'PV',
					'danger' => 'RD',
					'info' => 'BK',
					'warning' => 'SL'
				])
				->icons([
					'heroicon-o-building-storefront' => 'PV',
					'heroicon-o-user'=> 'RD',
					'heroicon-o-star' => 'BK',
					'heroicon-o-sparkles' => 'SL'
				]),
			ToggleColumn::make('reventa')->label('Reventa')->alignCenter()->toggleable(isToggledHiddenByDefault: true),
			ToggleColumn::make('is_active')->label('Activo')->alignCenter()->toggleable(isToggledHiddenByDefault: true),
			TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
			TextColumn::make('email')->label('Correo')->searchable()->sortable()->badge()->color('warning'),
			TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->badge()->color('success'),
			TextColumn::make('full_address')->label('Direccion')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)	
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
			ActionGroup::make([
				ViewAction::make('view')
					->url(fn (Customer $record): string => CustomerResource::getUrl('view', ['record' => $record])),

				EditAction::make('edit')
					->url(fn (Customer $record): string => CustomerResource::getUrl('edit', ['record' => $record])),
				
				GoToAction::make()->zoom(14)->label('Ver en Mapa')->color('success'),

				ActionsDeleteAction::make('delete')
					->successNotification(
						Notification::make()
							->success()
							->title('Cliente eliminado')
							->body('El Cliente ha sido eliminado  del sistema.')
							->icon('heroicon-o-trash')
							->iconColor('danger')
							->color('danger')
					)
					->modalHeading('Borrar Cliente')
					->modalDescription('Estas seguro que deseas eliminar este Cliente? Esta acción no se puede deshacer.')
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
				->modalHeading('Borrar Clientes')
				->modalDescription('Estas seguro que deseas eliminar los Clientes seleccionados? Esta acción no se puede deshacer.')
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
            'view' => ViewCustomer::route('/{record}'),
        ];
    }
}

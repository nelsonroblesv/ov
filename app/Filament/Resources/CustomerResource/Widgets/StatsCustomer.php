<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class StatsCustomer extends Widget
{
    // Define la vista Blade personalizada para este widget
    protected static string $view = 'filament.widgets.stats-customer'; 
    
    // Opcional: Establece el ancho del widget si lo necesitas (por defecto es 'full')
    protected int | string | array $columnSpan = 'full'; 

    public ?string $heading = 'Analitica del Cliente';
    public ?string $description = 'Vision general de la analitica del cliente.';

    // Define los datos que se pasarán a la vista Blade
    protected function getViewData(): array
    {
        // Puedes mantener la lógica de cálculo aquí
        return [
            'stats' => [
                'heading' => $this->heading,
                'description' => $this->description,
                'cuentas_por_cobrar' => Customer::query()->where('is_active', true)->count(),
                'anticipos_recibidos' => Customer::query()->where('is_active', true)->count(),
                'anticipos_entregados' => Customer::query()->where('is_active', true)->count(),
                'pedidos_por_entregar' => Customer::query()->where('is_active', true)->count(),
                'pedidos_entregados' => Customer::query()->where('is_active', true)->count(),
                'total_venta' => Customer::query()->where('is_active', true)->count(),
            ],
        ];
    }
}

/*
class StatsCustomer extends BaseWidget
{

    protected ?string $heading = 'Analitica del Cliente';
    protected ?string $description = 'Vision general de la analitica del cliente.';

    protected function getStats(): array
    {
        return [
            Stat::make('Cuentas por cobrar', Customer::query()
                ->where('is_active', true)
                ->count())
                ->description('$250.00')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                 ->chart([7, 7, 7, 7, 7, 7, 7]),

            Stat::make('Anticipos recibidos', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Anticipos entregados', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Pedidos por entregar', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Pedidos entregados', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Total de venta', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),
        ];
    }
        
}
*/
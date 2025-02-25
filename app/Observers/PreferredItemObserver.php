<?php

namespace App\Observers;

use App\Models\PreferredModule;
use App\Models\PreferredModuleItem;

class PreferredItemObserver
{

    public function creating(PreferredModuleItem $item)
    {
        // Buscar si ya existe el producto en el mismo módulo
        $existingItem = PreferredModuleItem::where('preferred_module_id', $item->preferred_module_id)
            ->where('product_id', $item->product_id)
            ->first();

        if ($existingItem) {
            // Si ya existe, sumar la cantidad y recalcular los precios
            $existingItem->quantity += $item->quantity;
            $existingItem->total_price_publico = $existingItem->quantity * $existingItem->price_publico;
            $existingItem->total_price_salon = $existingItem->quantity * $existingItem->price_salon;
            $existingItem->save();

            // No permitir la creación del nuevo registro
            return false;
        }
    }
    /**
     * Handle the PreferredModuleItem "created" event.
     */
    public function created(PreferredModuleItem $item): void
    {
        $this->updateGrandTotal($item->preferred_module_id);
    }

    /**
     * Handle the PreferredModuleItem "updated" event.
     */
    public function updated(PreferredModuleItem $item): void
    {
        $this->updateGrandTotal($item->preferred_module_id);
    }

    /**
     * Handle the PreferredModuleItem "deleted" event.
     */
    public function deleted(PreferredModuleItem $item): void
    {
        $this->updateGrandTotal($item->preferred_module_id);
    }

    /**
     * Handle the PreferredModuleItem "restored" event.
     */
    public function restored(PreferredModuleItem $item): void
    {
        $this->updateGrandTotal($item->preferred_module_id);
    }

    /**
     * Handle the PreferredModuleItem "force deleted" event.
     */
    public function forceDeleted(PreferredModuleItem $item): void
    {
        $this->updateGrandTotal($item->preferred_module_id);
    }

    private function updateGrandTotal($moduleId)
    {
        $module = PreferredModule::find($moduleId);
        if ($module) 
        {
            // Sumar el total de todos los productos en ese módulo
            $grandTotal = PreferredModuleItem::where('preferred_module_id', $moduleId)
                ->sum('total_price_publico'); // Puedes cambiarlo si quieres usar price_salon

            $module->grand_total = $grandTotal;
            $module->save();
        }
    }
}

<?php

namespace App\Observers;

use App\Models\PreferredModule;
use App\Models\PreferredModuleItem;

class PreferredItemObserver
{
    /**
     * Handle the PreferredModuleItem "created" event.
     */
    public function created(PreferredModuleItem $preferredModuleItem): void
    {
        $this->updateModuleTotal($preferredModuleItem->preferred_module_id);
    }

    /**
     * Handle the PreferredModuleItem "updated" event.
     */
    public function updated(PreferredModuleItem $preferredModuleItem): void
    {
        //
    }

    /**
     * Handle the PreferredModuleItem "deleted" event.
     */
    public function deleted(PreferredModuleItem $preferredModuleItem): void
    {
        //
    }

    /**
     * Handle the PreferredModuleItem "restored" event.
     */
    public function restored(PreferredModuleItem $preferredModuleItem): void
    {
        //
    }

    /**
     * Handle the PreferredModuleItem "force deleted" event.
     */
    public function forceDeleted(PreferredModuleItem $preferredModuleItem): void
    {
        //
    }

    private function updateModuleTotal($preferredModuleItem)
    {
        // Sumar todos los valores de total_price PUBLICO para el modulo proporcionado
        $total = PreferredModuleItem::where('preferred_module_id', $preferredModuleItem)->sum('total_price_publico');
       
       // Actualizar el campo grand_total en la tabla PreferredModule
       PreferredModule::where('id', $preferredModuleItem)->update(['grand_total' => $total]);
    }
}

<?php


namespace App\Providers;
use App\Models\SR; // Ensure the SR model is imported

use Illuminate\Support\Facades\View;
use App\Models\ProductGroup; // Make sure this is capitalized
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
 

   public function boot(): void
{
    // This logic runs every time the sidebar component is rendered
    View::composer('components.sidebar', function ($view) {
        
        // 1. Get the current user's manager_id
        $managerId = auth()->user()->manager_id ?? 0;
                
        // 2. Fetch only the groups that belong to this manager
        $groups = ProductGroup::where('manager_id', $managerId)
                    ->orderBy('product_group', 'asc') // Changed from product_group to match your naming if needed
                    ->get();

        // 3. Pass the filtered data to the sidebar
        $view->with('groupsdata', $groups);
    });
View::composer('*', function ($view) {
    // This provides the full list of sizes to every page for the dropdowns
    $view->with('allSizes', \App\Models\Size::all());
    
    // This keeps your existing logic for the "active" one
    $sizeId = request()->get('id'); 
    $view->with('activeSize', $sizeId ? \App\Models\Size::find($sizeId) : null);
});

}
}
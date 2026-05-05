<?php

namespace App\Observers;

use App\Models\Package;
use App\Models\RadGroupReply;

class PackageObserver
{
    /**
     * Handle the Package "created" event.
     */
    public function created(Package $package): void
    {
        // Gunakan nama paket sebagai groupname di Radius
        $groupName = $package->name;

        // Set speed limit di RadGroupReply
        RadGroupReply::create([
            'groupname' => $groupName,
            'attribute' => 'Mikrotik-Rate-Limit',
            'op' => ':=',
            'value' => $package->speed_limit . 'M/' . $package->speed_limit . 'M',
        ]);
    }

    /**
     * Handle the Package "updated" event.
     */
    public function updated(Package $package): void
    {
        $groupName = $package->getOriginal('name');
        $newGroupName = $package->name;

        if ($package->isDirty('name')) {
            RadGroupReply::where('groupname', $groupName)
                ->update(['groupname' => $newGroupName]);
        }

        if ($package->isDirty('speed_limit')) {
            RadGroupReply::where('groupname', $newGroupName)
                ->where('attribute', 'Mikrotik-Rate-Limit')
                ->update(['value' => $package->speed_limit . 'M/' . $package->speed_limit . 'M']);
        }
    }

    /**
     * Handle the Package "deleted" event.
     */
    public function deleted(Package $package): void
    {
        RadGroupReply::where('groupname', $package->name)->delete();
    }
}

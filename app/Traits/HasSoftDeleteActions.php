<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSoftDeleteActions
{
    // Soft delete — controller calls $this->softDelete($model)
    protected function softDelete(Model $model, string $redirectRoute): \Illuminate\Http\RedirectResponse
    {
        $model->delete();

        activity()
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->log('Soft deleted ' . class_basename($model) . ' #' . $model->id);

        return redirect()->route($redirectRoute)
            ->with('success', class_basename($model) . ' deleted successfully.');
    }

    // Restore
    protected function restoreModel(Model $model, string $redirectRoute): \Illuminate\Http\RedirectResponse
    {
        $model->restore();

        activity()
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->log('Restored ' . class_basename($model) . ' #' . $model->id);

        return redirect()->route($redirectRoute)
            ->with('success', class_basename($model) . ' restored successfully.');
    }

    // Permanent delete
    protected function forceDeleteModel(Model $model, string $redirectRoute): \Illuminate\Http\RedirectResponse
    {
        $model->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->log('Permanently deleted ' . class_basename($model) . ' #' . $model->id);

        return redirect()->route($redirectRoute)
            ->with('success', class_basename($model) . ' permanently deleted.');
    }
}

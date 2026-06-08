<?php

namespace App\Traits;

trait HasBranchAccess
{
    /**
     * Check if the current user can see all branches
     */
    protected function canViewAllBranches(): bool
    {
        $user = auth()->user();
        return $user->hasRole('super_admin')
            || $user->hasRole('general_manager');
    }

    /**
     * Get branch filter for queries
     * Returns null if user can see all branches
     */
    protected function getBranchId(?int $requestedBranchId = null): ?int
    {
        if ($this->canViewAllBranches()) {
            return $requestedBranchId; // use filter if provided, else all
        }
        return auth()->user()->branch_id; // locked to own branch
    }

    /**
     * Apply branch scope to a query
     */
    protected function applyBranchScope($query, string $column = 'branch_id', ?int $requestedBranchId = null)
    {
        if ($this->canViewAllBranches()) {
            if ($requestedBranchId) {
                $query->where($column, $requestedBranchId);
            }
        } else {
            $query->where($column, auth()->user()->branch_id);
        }
        return $query;
    }
}

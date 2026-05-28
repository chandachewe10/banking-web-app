<?php

namespace App\Policies;

use App\Models\BranchManagerEvaluation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchManagerReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_branch::manager::review');
    }

    public function view(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('view_branch::manager::review');
    }

    public function create(User $user): bool
    {
        return $user->can('create_branch::manager::review');
    }

    public function update(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('update_branch::manager::review');
    }

    public function delete(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('delete_branch::manager::review');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_branch::manager::review');
    }

    public function forceDelete(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('force_delete_branch::manager::review');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_branch::manager::review');
    }

    public function restore(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('restore_branch::manager::review');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_branch::manager::review');
    }

    public function replicate(User $user, BranchManagerEvaluation $branchManagerEvaluation): bool
    {
        return $user->can('replicate_branch::manager::review');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_branch::manager::review');
    }
}

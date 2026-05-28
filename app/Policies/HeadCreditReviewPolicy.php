<?php

namespace App\Policies;

use App\Models\HeadCreditEvaluation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HeadCreditReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_head::credit::review');
    }

    public function view(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('view_head::credit::review');
    }

    public function create(User $user): bool
    {
        return $user->can('create_head::credit::review');
    }

    public function update(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('update_head::credit::review');
    }

    public function delete(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('delete_head::credit::review');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_head::credit::review');
    }

    public function forceDelete(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('force_delete_head::credit::review');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_head::credit::review');
    }

    public function restore(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('restore_head::credit::review');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_head::credit::review');
    }

    public function replicate(User $user, HeadCreditEvaluation $headCreditEvaluation): bool
    {
        return $user->can('replicate_head::credit::review');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_head::credit::review');
    }
}

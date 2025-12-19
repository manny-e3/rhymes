<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['author', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Book $book): bool
    {
        return $user->hasRole('admin') || $user->id === $book->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can submit books
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        // Only book owner can edit, and only if pending or rejected
        return $user->id === $book->user_id && 
               in_array($book->status, ['pending', 'rejected']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $book): bool
    {
        // Only book owner can delete, and only if pending or rejected
        return $user->id === $book->user_id && 
               in_array($book->status, ['pending', 'rejected']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Book $book): bool
    {
        // Authors can restore their own books, admins can restore any book
        return $user->hasRole('admin') || $user->id === $book->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        // Only admins can permanently delete books
        return $user->hasRole('admin');
    }
}

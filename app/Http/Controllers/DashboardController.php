<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on user role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        }
        
        // Get user's books
        $books = Book::where('user_id', $user->id)->get();
        
        // Default dashboard for regular users
        return view('dashboard', compact('books'));
    }
}
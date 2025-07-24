<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ClassModel; // Change to your actual class model name
use App\Models\Material; // Add this line
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $invoiceCount = Invoice::count();
        $studentCount = User::where('role', 'student')->count();
        $classCount = ClassModel::count();
        $pendingCount = Invoice::where('status', 'unused')->count();

        $recentEnrollments = Enrollment::with(['user', 'class'])
            ->latest('enrollment_date')
            ->take(3)
            ->get();

        $users = User::where('role', 'student')->get(); // Add this line

        $materials = Material::with('class')->paginate(10); // Add this line
        $classes = ClassModel::all(); // Add this line

        return view('admin.dashboard', compact(
            'invoiceCount', 'studentCount', 'classCount', 'pendingCount', 'recentEnrollments', 'users', 'materials', 'classes'
        )); // Update this line
    }
}
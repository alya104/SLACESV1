<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invoice;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'invoiceCount' => Invoice::count(),
            'studentCount' => User::where('role', 'student')->count(),
            'classCount' => ClassModel::where('status', 'active')->count(),
            'pendingCount' => Enrollment::where('status', 'pending')->count(),
            'recentEnrollments' => Enrollment::with(['user', 'class'])
                            ->latest()
                            ->take(5)
                            ->get()
        ]);
    }
    
    public function classes()
    {
        return view('admin.classes', [
            'classes' => ClassModel::withCount('enrollments')->get(),
            'users' => User::where('role', 'student')->get(), // <-- add this line!
        ]);
    }
    
    public function invoices()
    {
        return view('admin.invoices', [
            'invoices' => Invoice::with(['user', 'class'])->get()
        ]);
    }
    
    public function students()
    {
        return view('admin.enrolled-students', [
            'students' => User::where('role', 'student')
                ->withCount('enrollments')
                ->with('enrollments.class')
                ->get()
        ]);
    }
    
    public function materials()
    {
        return view('admin.materials', [
            'classes' => ClassModel::all()
        ]);
    }
    
    public function logs()
    {
        return view('admin.logs', [
            'logs' => [] // For now, just an empty array
        ]);
    }
    
    public function showProfile()
    {
        return view('admin.profile', [
            'user' => Auth::user(),
        ]);
    }
    
    public function editProfile()
    {
        return view('admin.edit-profile', [
            'user' => Auth::user(),
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = '/storage/' . $path;
        }

        $user->save();

        // Log the profile edit
        \App\Models\Log::logAction(
            Auth::id(),
            'edit',
            'Edited profile: ' . $user->name . ' (' . $user->email . ')'
        );

        return redirect()->route('admin.profile')->with('success', 'Profile updated!');
    }
    
    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|exists:users,id'
        ]);
        
        $class = new ClassModel();
        $class->title = $validated['title'];
        $class->description = $validated['description'];
        $class->instructor_id = $validated['instructor'];
        $class->status = 'active';
        $class->save();
        
        return redirect()->route('admin.classes')->with('success', 'Class created successfully!');
    }
    
    public function storeInvoice(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'student_email' => 'required|email|exists:users,email',
            'class_id' => 'required|exists:class_models,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid,overdue',
            'invoice_date' => 'required|date',
        ]);

        // Find the student by email
        $user = User::where('email', $validated['student_email'])->first();

        // Create the invoice
        $invoice = new Invoice();
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->user_id = $user->id;
        $invoice->class_id = $validated['class_id'];
        $invoice->amount = $validated['amount'];
        $invoice->status = $validated['status'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->save();

        return redirect()->route('admin.invoices')->with('success', 'Invoice added successfully.');
    }
    
    public function storeMaterial(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_models,id',
            'type' => 'required|in:video,pdf,link',
            'description' => 'nullable|string',
        ]);
        
        // Create the material
        $material = new Material();
        $material->title = $validated['title'];
        $material->class_id = $validated['class_id'];
        $material->type = $validated['type'];
        $material->description = $validated['description'] ?? null;
        
        // Handle different types of materials
        if ($validated['type'] === 'video' && $request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('materials/videos', 'public');
            $material->file_path = $path;
            $material->duration = $request->input('duration');
        } else if ($validated['type'] === 'pdf' && $request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('materials/pdfs', 'public');
            $material->file_path = $path;
        } else if ($validated['type'] === 'link') {
            $material->url = $request->input('url');
        }
        
        // Store thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('materials/thumbnails', 'public');
            $material->thumbnail = $thumbnailPath;
        }
        
        $material->save();
        
        return redirect()->route('admin.materials')->with('success', 'Material added successfully.');
    }
}
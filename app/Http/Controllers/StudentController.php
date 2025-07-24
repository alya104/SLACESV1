<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassModel;
use App\Models\CompletedMaterial;
use App\Models\Progress;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get all enrollments with class relationship
        $enrollments = $user->enrollments()->with('class')->get();

        // Enrolled classes count
        $enrolledClassesCount = $enrollments->count();

        // Get all class IDs
        $classIds = $enrollments->pluck('class_id');

        // Get all materials from those classes
        $materials = \App\Models\Material::whereIn('class_id', $classIds)->get();
        $availableMaterialsCount = $materials->count();

        // Calculate completed modules/materials for this user
        $completedModuleIds = \App\Models\Progress::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('module_id')
            ->toArray();

        // Calculate overall completion percentage
        $totalModules = $materials->count();
        $completedModules = count(array_intersect($materials->pluck('id')->toArray(), $completedModuleIds));
        $overallCompletionPercentage = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;

        // Prepare enrolledClasses for "My Progress" cards
        $enrolledClasses = [];
        foreach ($enrollments as $enrollment) {
            $class = $enrollment->class;
            if (!$class) continue;
            $classMaterials = $class->materials ?? collect();
            $total = $classMaterials->count();
            $completed = $classMaterials->whereIn('id', $completedModuleIds)->count();
            $enrolledClasses[] = (object)[
                'class_id' => $class->id,
                'class' => $class,
                'status' => $enrollment->status,
                'completion_percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'completed_modules_count' => $completed,
                'total_modules_count' => $total,
                'updated_at' => $enrollment->updated_at,
            ];
        }

        return view('student.dashboard', compact(
            'enrolledClassesCount',
            'overallCompletionPercentage',
            'availableMaterialsCount',
            'enrolledClasses'
        ));
    }

    public function resources()
    {
        // Your code to show resources page
        return view('student.resources');
    }

    public function myClasses()
    {
        $user = auth()->user();

        // Get enrollments with class relationship
        $enrollments = $user->enrollments()
            ->with('class.materials')
            ->whereIn('status', ['active', 'pending'])
            ->orderByDesc('created_at')
            ->paginate(9); // <-- use paginate instead of get()

        // Get completed module IDs for this user
        $completedModuleIds = \App\Models\Progress::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('module_id')
            ->toArray();

        $enrollments->getCollection()->transform(function($enrollment) use ($completedModuleIds) {
            $class = $enrollment->class;
            $materials = $class ? $class->materials : collect();
            $total = $materials->count();
            $completed = $materials->whereIn('id', $completedModuleIds)->count();

            $enrollment->completion_percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            $enrollment->enrollment_date = $enrollment->created_at;
            return $enrollment;
        });

        // If you want pagination, use ->paginate() and adjust the mapping accordingly

        return view('student.classes', compact('enrollments'));
    }

    public function redeemInvoice(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|exists:invoices,invoice_number',
        ]);

        $user = Auth::user();
        $invoice = \App\Models\Invoice::where('invoice_number', $request->invoice_number)
            ->where('email', $user->email)
            ->where('status', 'unused')
            ->first();

        if (!$invoice) {
            return redirect()->back()->withErrors(['invoice_number' => 'Invalid or already used invoice.']);
        }

        // Upgrade role
        $user->role = 'student';
        $user->save();

        // Enroll student
        \App\Models\Enrollment::updateOrCreate([
            'user_id' => $user->id,
            'class_id' => $invoice->class_id,
        ], [
            'status' => 'pending', // set to pending on join
            'enrollment_date' => now(),
            'completion_percentage' => 0,
        ]);

        // Mark invoice as used
        $invoice->status = 'used';
        $invoice->user_id = $user->id;
        $invoice->save();

        return redirect()->route('student.dashboard')->with('success', 'You have joined the class!');
    }
    
    public function modules($class_id)
    {
        // Fetch the class and its modules
        $class = \App\Models\ClassModel::with('modules')->findOrFail($class_id);

        // Optionally, check if the user is enrolled in this class
        $user = auth()->user();
        $enrolled = $user->enrollments()->where('class_id', $class_id)->exists();

        if (!$enrolled) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Get completed materials for the class
        $completedMaterials = \App\Models\Progress::where('class_id', $class_id)
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('module_id')
            ->toArray();

        // Pass class and modules to the view
        return view('student.modules', [
            'class' => $class,
            'modules' => $class->modules,
            'completedMaterials' => $completedMaterials,
        ]);
    }

    public function allModules()
    {
        $user = auth()->user();

        // Get all class IDs the student is enrolled in
        $classIds = $user->enrollments()->pluck('class_id');

        // Get all materials from those classes
        $materials = \App\Models\Material::whereIn('class_id', $classIds)->with('class')->paginate(9);

        // Get completed materials for all classes
        $completedMaterials = \App\Models\Progress::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('module_id')
            ->toArray();

        return view('student.modules', [
            'materials' => $materials,
            'classes' => \App\Models\ClassModel::whereIn('id', $classIds)->get(),
            'class' => null,
            'completedMaterials' => $completedMaterials,
        ]);
    }

    public function markProgress(Request $request, $class_id, $module_id)
    {
        $user = auth()->user();

        // Mark the module as completed for this user
        \App\Models\Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'class_id' => $class_id,
                'module_id' => $module_id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        // Recalculate progress for this class
        $class = \App\Models\ClassModel::with('materials')->find($class_id);
        $totalModules = $class->materials->count();
        $completedModules = \App\Models\Progress::where('user_id', $user->id)
            ->where('class_id', $class_id)
            ->where('completed', true)
            ->count();

        $progress = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;

        // Update enrollment status based on progress
        $enrollment = $user->enrollments()->where('class_id', $class_id)->first();
        if ($enrollment) {
            $enrollment->completion_percentage = $progress;
            // Set status to 'active' if progress > 0, otherwise keep as is or set to 'pending'
            $enrollment->status = $progress > 0 ? 'active' : 'pending';
            // Optionally, set to 'completed' if 100%
            // $enrollment->status = $progress == 100 ? 'completed' : ($progress > 0 ? 'active' : 'pending');
            $enrollment->save();
        }

        return redirect()->back()->with('success', 'Material marked as done!');
    }

    public function unmarkProgress(Request $request, $class_id, $module_id)
    {
        $user = auth()->user();
        \App\Models\Progress::where([
            'user_id' => $user->id,
            'class_id' => $class_id,
            'module_id' => $module_id,
        ])->delete();

        // Recalculate progress for this class
        $class = \App\Models\ClassModel::with('materials')->find($class_id);
        $totalModules = $class->materials->count();
        $completedModules = \App\Models\Progress::where('user_id', $user->id)
            ->where('class_id', $class_id)
            ->where('completed', true)
            ->count();

        $progress = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;

        // Update enrollment status based on progress
        $enrollment = $user->enrollments()->where('class_id', $class_id)->first();
        if ($enrollment) {
            $enrollment->completion_percentage = $progress;
            // Set status to 'active' if progress > 0, otherwise 'pending'
            $enrollment->status = $progress > 0 ? 'active' : 'pending';
            // Optionally, set to 'completed' if 100%
            // $enrollment->status = $progress == 100 ? 'completed' : ($progress > 0 ? 'active' : 'pending');
            $enrollment->save();
        }

        return redirect()->back()->with('success', 'Material marked as undone!');
    }

    public function viewMaterial($class_id, $material_id)
    {
        $user = auth()->user();

        // Check if the user is enrolled in the class
        $enrolled = $user->enrollments()->where('class_id', $class_id)->exists();
        if (!$enrolled) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Fetch the material
        $material = \App\Models\Material::where('class_id', $class_id)
            ->findOrFail($material_id);

        // Optionally, fetch the class for display
        $class = \App\Models\ClassModel::findOrFail($class_id);

        return view('student.material-view', compact('material', 'class'));
    }

    public function materialPreview($materialId)
    {
        $user = auth()->user();

        // Only allow preview if the user is enrolled in the class
        $material = \App\Models\Material::with('class')->find($materialId);
        if (!$material) {
            return response()->json(['success' => false, 'message' => 'Material not found']);
        }

        $enrolled = $user->enrollments()->where('class_id', $material->class_id)->exists();
        if (!$enrolled) {
            return response()->json(['success' => false, 'message' => 'Not enrolled in this class']);
        }

        return response()->json([
            'success' => true,
            'material' => [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'type' => $material->type,
                'file_path' => $material->file_path,
                'thumbnail' => $material->thumbnail,
                'url' => $material->url,
                'class' => [
                    'id' => $material->class->id,
                    'title' => $material->class->title,
                ],
            ],
        ]);
    }

    public function showJoinClassForm()
    {
        return view('student.join-class');
    }

    public function submitJoinClass(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|exists:invoices,invoice_number',
            'email' => 'required|email',
        ]);

        $user = auth()->user();

        // Find the invoice
        $invoice = \App\Models\Invoice::where('invoice_number', $request->invoice_number)->first();

        if (!$invoice) {
            return back()->withErrors(['invoice_number' => 'Invalid invoice number or email.']);
        }

        // Check if invoice already used
        if ($invoice->user_id && $invoice->user_id != $user->id) {
            return back()->withErrors(['invoice_number' => 'This invoice has already been used.']);
        }

        // Check email match
        if (
            ($invoice->email && strtolower($invoice->email) !== strtolower($request->email)) &&
            ($invoice->user_id == null || $user->id != $invoice->user_id)
        ) {
            return back()->withErrors(['email' => 'Email does not match our records.']);
        }

        // Link invoice to user
        $invoice->user_id = $user->id;
        $invoice->save();

        // Enroll student in the class
        $class = \App\Models\ClassModel::find($invoice->class_id);
        \App\Models\Enrollment::updateOrCreate([
            'user_id' => $user->id,
            'class_id' => $invoice->class_id,
        ], [
            'status' => 'pending',
            'enrollment_date' => now(),
            'completion_percentage' => 0,
        ]);

        $className = $class ? $class->title : 'the class';

        return redirect()->route('student.dashboard')->with('success', "You have successfully joined '{$className}'!");
    }

    // Show profile form
    public function profile()
    {
        $user = auth()->user();
        return view('student.profile', compact('user'));
    }

    // Handle profile update
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            // Remove email from validation since we don't allow editing
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;

        // Do NOT update email

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images');
            $file->move($destinationPath, $filename);
            $user->avatar_url = '/images/' . $filename;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
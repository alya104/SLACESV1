<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentController extends Controller
{
    /**
     * Show the list of enrolled students, optionally filtered by class.
     */
    public function index(Request $request)
    {
        // Get all classes for the filter dropdown
        $classes = ClassModel::all();
        
        // Get all students for the enrollment modal
        $students = User::where('role', 'student')->get();

        // Optional: Filter by class if class_id is present in the query string
        $classId = $request->query('class_id');
        
        // Base query with consistent eager loading
        $query = Enrollment::with(['student', 'class', 'progress' /* custom relation */]);
        
        // Apply filter if class_id is provided
        if ($classId) {
            $query->where('class_id', $classId);
        }
        
        // Get paginated results
        $enrollments = $query->paginate(10);
        foreach ($enrollments as $enrollment) {
            $class = $enrollment->class;
            $student = $enrollment->student;
            if ($class && $student) {
                $total = $class->materials()->count();
                $completed = \App\Models\Progress::where('user_id', $student->id)
                    ->where('class_id', $class->id)
                    ->where('completed', true)
                    ->count();
                $enrollment->completion_percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            } else {
                $enrollment->completion_percentage = 0;
            }
        }

        // Pass the data to the view
        return view('admin.enrolled-students', compact('classes', 'enrollments', 'classId', 'students'));
    }

    /**
     * Update the specified enrollment in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,pending',
            'enrollment_date' => 'required|date',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->class_id = $request->input('class_id');
        $enrollment->user_id = $request->input('user_id');
        $enrollment->status = $request->input('status');
        $enrollment->enrollment_date = $request->input('enrollment_date');
        $enrollment->save();

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,pending',
            'enrollment_date' => 'required|date',
        ]);

        Enrollment::updateOrCreate([
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
        ], [
            'status' => $request->status,
            'enrollment_date' => $request->enrollment_date,
            'completion_percentage' => 0,
        ]);

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment created successfully!');
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment deleted successfully!');
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit($id)
    {
        $enrollment = Enrollment::with('student', 'class')->findOrFail($id);
        $classes = ClassModel::all();
        $students = User::where('role', 'student')->get();

        return view('admin.edit-enrollment', compact('enrollment', 'classes', 'students'));
    }

    /**
     * Display the specified enrollment.
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'class'])->findOrFail($id);

        // Calculate progress
        $class = $enrollment->class;
        $student = $enrollment->student;
        $total = $class ? $class->materials()->count() : 0;
        $completed = 0;
        $lastAccessed = null;

        if ($class && $student) {
            $completed = \App\Models\Progress::where('user_id', $student->id)
                ->where('class_id', $class->id)
                ->where('completed', true)
                ->count();

            // Optional: get last accessed material title
            $lastProgress = \App\Models\Progress::where('user_id', $student->id)
                ->where('class_id', $class->id)
                ->where('completed', true)
                ->orderByDesc('updated_at')
                ->first();
            $lastAccessed = $lastProgress && $lastProgress->material ? $lastProgress->material->title : null;
        }

        // Other enrolled classes (titles)
        $otherClasses = Enrollment::with('class')
            ->where('user_id', $student->id)
            ->where('id', '!=', $enrollment->id)
            ->get()
            ->pluck('class.title')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'enrollment' => [
                'student' => [
                    'name' => $student->name,
                    'email' => $student->email,
                ],
                'class' => [
                    'title' => $class ? $class->title : '',
                ],
                'enrollment_date' => $enrollment->enrollment_date,
                'status' => $enrollment->status,
                'completed_materials' => $completed,
                'total_materials' => $total,
                'completion_percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'last_accessed_material' => $lastAccessed,
                'other_classes' => $otherClasses,
            ]
        ]);
    }
}
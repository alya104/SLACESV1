<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * Display a listing of classes
     */
    public function index()
    {
        $classes = ClassModel::with('instructor')
                    ->latest()
                    ->paginate(10);
        
        $instructors = User::where('role', 'instructor')->get();
        $users = User::where('role', 'student')->get(); // <-- add this line!

        return view('admin.classes', [
            'classes' => $classes,
            'instructors' => $instructors,
            'users' => $users, // <-- add this!
        ]);
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,upcoming',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048', // required!
            'created_at' => 'nullable|date',
            // add other fields as needed
        ]);

        $class = new ClassModel();
        $class->title = $request->title;
        $class->description = $request->description;
        $class->status = $request->status;
        if ($request->filled('created_at')) {
            $class->created_at = $request->created_at;
        }
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = 'class_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('class_thumbnails', $filename, 'public');
            $class->thumbnail = $path;
        }
        $class->save();

        return redirect()->route('admin.classes')->with('success', 'Class added successfully!');
    }

    /**
     * Update an existing class
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,upcoming',
            'created_at' => 'nullable|date',
            // add other fields as needed
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.classes')
                ->withErrors($validator)
                ->withInput();
        }
        
        $class = ClassModel::findOrFail($id);
        $class->title = $request->title;
        $class->description = $request->description;
        $class->status = $request->status;
        if ($request->filled('created_at')) {
            $class->created_at = $request->created_at;
        }
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = 'class_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('class_thumbnails', $filename, 'public');
            $class->thumbnail = $path;
        }
        $class->save();
        
        return redirect()->route('admin.classes')
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Delete a class
     */
    public function destroy($id)
    {
        // Check if class has enrolled students before deletion
        $class = ClassModel::findOrFail($id);

        // Optional: Check for enrollments
        if ($class->enrollments()->count() > 0) {
            return redirect()->route('admin.classes')
                ->with('error', 'Cannot delete class with enrolled students.');
         }
        
        $class->delete();
        
        return redirect()->route('admin.classes')
            ->with('success', 'Class deleted successfully!');
    }
    
    /**
     * Change class status
     */
    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,upcoming'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        
        $class = ClassModel::findOrFail($id);
        $class->status = $request->status;
        $class->save();
        
        return response()->json(['success' => 'Status updated successfully']);
    }

    /**
     * Display the specified class.
     */
    public function show($id)
    {
        $class = ClassModel::findOrFail($id);
        return view('admin.class-show', compact('class'));
    }
}
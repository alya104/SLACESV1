<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\CompletedMaterial;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get enrolled classes
        $enrolledClasses = Enrollment::where('user_id', $userId)
            ->where('status', 'active')
            ->with(['class' => function ($query) {
                $query->select('id', 'title', 'description', 'status', 'created_at');
            }])
            ->get();
            
        // Count total active enrollments
        $enrolledClassesCount = $enrolledClasses->count();
        
        // Calculate overall completion percentage
        $enrollmentIds = $enrolledClasses->pluck('id')->toArray();
        $classIds = $enrolledClasses->pluck('class_id')->toArray();
        
        // Get all materials for enrolled classes
        $totalMaterials = Material::whereIn('class_id', $classIds)->count();
        
        // Get completed materials count
        $completedMaterialsCount = CompletedMaterial::where('user_id', $userId)
            ->whereHas('material', function($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->count();
        
        // Calculate completion percentage
        $overallCompletionPercentage = $totalMaterials > 0 
            ? ($completedMaterialsCount / $totalMaterials) * 100 
            : 0;

        // Get total learning hours (dummy calculation - replace with real tracking)
        // This could be based on completed video durations
        $totalLearningHours = $completedMaterialsCount > 0 
            ? round($completedMaterialsCount * 0.75) 
            : 0;
            
        // Count available materials
        $availableMaterialsCount = $totalMaterials;
        
        // Enhance enrollment data with completion details
        foreach ($enrolledClasses as $enrollment) {
            // Count total modules for this class
            $totalModulesCount = Material::where('class_id', $enrollment->class_id)->count();
            $enrollment->total_modules_count = $totalModulesCount;
            
            // Count completed modules for this class
            $completedModulesCount = CompletedMaterial::where('user_id', $userId)
                ->whereHas('material', function($query) use ($enrollment) {
                    $query->where('class_id', $enrollment->class_id);
                })
                ->count();
            $enrollment->completed_modules_count = $completedModulesCount;
            
            // Calculate completion percentage
            $enrollment->completion_percentage = $totalModulesCount > 0 
                ? ($completedModulesCount / $totalModulesCount) * 100 
                : 0;
        }
        
        return view('student.dashboard', [
            'enrolledClasses' => $enrolledClasses,
            'enrolledClassesCount' => $enrolledClassesCount,
            'overallCompletionPercentage' => $overallCompletionPercentage,
            'totalLearningHours' => $totalLearningHours,
            'availableMaterialsCount' => $availableMaterialsCount,
        ]);
    }
}
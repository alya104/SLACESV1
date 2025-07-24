<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\ClassModel;
use App\Models\User; // Add this import
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Log as ActivityLog; // <-- add this line


class MaterialsController extends Controller
{
    /**
     * Display a listing of the materials
     */
    public function index(Request $request)
    {
        $query = Material::query();
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('class_id') && $request->class_id !== 'all') {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        $materials = $query->paginate(9)->appends($request->all());

        $classes = ClassModel::all();
        $users = User::where('role', 'student')->get(); // <-- add this line

        return view('admin.materials', compact('materials', 'classes', 'users'));
    }
    
    /**
     * Store a newly created material
     */
    public function store(Request $request)
    {
        Log::info('Store method called!', ['method' => $request->method(), 'url' => $request->url()]);
        
        // Add debug logging
        Log::info('File upload debug:', [
            'has_file' => $request->hasFile('file'),
            'has_video_file' => $request->hasFile('video_file'),
            'has_pdf_file' => $request->hasFile('pdf_file'),
            'has_image_file' => $request->hasFile('image_file'),
            'type' => $request->type,
            'all_files' => array_keys($request->allFiles()),
            'all_input' => $request->except(['_token'])
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|in:video,pdf,link,document,image,audio',
            'file' => 'nullable|file|max:10240',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:51200', // 50MB
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB
            'image_file' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:5120', // 5MB
            'url' => 'nullable|url|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->route('admin.materials')
                ->withErrors($validator)
                ->withInput();
        }
        
        $material = new Material();
        $material->title = $request->title;
        $material->description = $request->description;
        $material->class_id = $request->class_id;
        $material->type = $request->type;
        $material->is_active = $request->has('is_active');
        $material->sort_order = Material::where('class_id', $request->class_id)->max('sort_order') + 1;
        
        // Simplified file handling with better debugging
        $fileInput = null;
        $file = null;
        
        // Check for type-specific file inputs first
        if ($request->hasFile('video_file')) {
            $fileInput = 'video_file';
            $file = $request->file('video_file');
        } elseif ($request->hasFile('pdf_file')) {
            $fileInput = 'pdf_file';
            $file = $request->file('pdf_file');
        } elseif ($request->hasFile('image_file')) {
            $fileInput = 'image_file';
            $file = $request->file('image_file');
        } elseif ($request->hasFile('file')) {
            $fileInput = 'file';
            $file = $request->file('file');
        }

        if ($file && $file->isValid()) {
            Log::info('Processing file upload:', [
                'input_name' => $fileInput,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            try {
                $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $folder = $request->type . 's';
                
                // Ensure directory exists
                Storage::disk('public')->makeDirectory($folder);
                
                $path = $file->storeAs($folder, $filename, 'public');
                $material->file_path = $path;

                Log::info('File stored successfully:', [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path)
                ]);

                // Generate thumbnail
                if ($request->type === 'image') {
                    $material->thumbnail = $path;
                } elseif ($request->type === 'pdf') {
                    $material->thumbnail = 'materials/default-pdf.png';
                } elseif ($request->type === 'video') {
                    $material->thumbnail = 'materials/default-video.png';
                }
            } catch (\Exception $e) {
                Log::error('File upload failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('admin.materials')
                    ->withErrors(['file' => 'File upload failed: ' . $e->getMessage()])
                    ->withInput();
            }
        } elseif ($request->filled('url')) {
            $material->url = $request->url;
            
            // For video links, try to generate a thumbnail
            if ($material->type === 'video' && Str::contains($request->url, ['youtube', 'youtu.be', 'vimeo'])) {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $request->url, $matches)) {
                    $material->thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
                }
            }
        }
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $material->thumbnail = $thumbnailPath;
        }
        
        $material->save();
        
        ActivityLog::logAction(Auth::id(), 'create', 'Created material: ' . $material->title);
        
        return redirect()->route('admin.materials')
            ->with('success', 'Material created successfully');
    }
    
    /**
     * Update an existing material
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|in:video,pdf,link,document,image,audio',
            'file' => 'nullable|file|max:10240',
            'url' => 'nullable|url|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.materials')
                ->withErrors($validator)
                ->withInput();
        }

        $material = Material::findOrFail($id);
        $material->title = $request->title;
        $material->description = $request->description;
        $material->class_id = $request->class_id;
        $material->type = $request->type;
        $material->is_active = $request->has('is_active');

        // Determine the correct file input name based on type
        $fileInput = null;
        if ($request->type === 'video' && $request->hasFile('video_file')) {
            $fileInput = 'video_file';
        } elseif ($request->type === 'pdf' && $request->hasFile('pdf_file')) {
            $fileInput = 'pdf_file';
        } elseif ($request->type === 'image' && $request->hasFile('image_file')) {
            $fileInput = 'image_file';
        } elseif ($request->hasFile('file')) {
            $fileInput = 'file';
        }

        if ($fileInput) {
            // Delete old file if exists
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file($fileInput);
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $folder = $request->type . 's'; // Store in 'videos', 'pdfs', 'images', etc.
            $path = $file->storeAs($folder, $filename, 'public');
            $material->file_path = $path;
            $material->url = null;

            // Generate thumbnail for images, or default for pdf/video
            if ($request->type === 'image') {
                $material->thumbnail = $path;
            } elseif ($request->type === 'pdf') {
                $material->thumbnail = 'materials/default-pdf.png';
            } elseif ($request->type === 'video') {
                $material->thumbnail = 'materials/default-video.png';
            }
        } elseif ($request->filled('url')) {
            $material->url = $request->url;
            $material->file_path = null;
            
            // For video links, try to generate a thumbnail
            if ($material->type === 'video' && Str::contains($request->url, ['youtube', 'youtu.be', 'vimeo'])) {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $request->url, $matches)) {
                    $material->thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
                }
            }
        }
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Optionally delete old thumbnail
            if ($material->thumbnail && Storage::disk('public')->exists($material->thumbnail)) {
                Storage::disk('public')->delete($material->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $material->thumbnail = $thumbnailPath;
        }
        
        $material->save();
        ActivityLog::logAction(Auth::id(), 'edit', 'Edited material: ' . $material->title);

        return redirect()->route('admin.materials')
            ->with('success', 'Material updated successfully');
    }
    
    /**
     * Remove the specified material
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        
        // Delete file if exists
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();
         ActivityLog::logAction(Auth::id(), 'delete', 'Deleted material: ' . $material->title);
        return redirect()->route('admin.materials')
            ->with('success', 'Material deleted successfully');
    }
    
    /**
     * Toggle the active status of a material
     */
    public function toggleActive($id)
    {
        $material = Material::findOrFail($id);
        $material->is_active = !$material->is_active;
        $material->save();
        
        return redirect()->route('admin.materials')
            ->with('success', 'Material status updated successfully');
    }
    
    /**
     * Update the sort order of materials
     */
    public function updateOrder(Request $request)
    {
        $items = $request->items;
        
        foreach ($items as $item) {
            Material::where('id', $item['id'])->update(['sort_order' => $item['position']]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Preview a material
     */
    public function preview($id)
    {
        $material = Material::with('class')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'material' => $material
        ]);
    }
    
    /**
     * Show the form for editing the specified material
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'material' => $material
        ]);
    }
}
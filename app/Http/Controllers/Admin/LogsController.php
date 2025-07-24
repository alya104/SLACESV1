<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $logs = $query->orderBy('created_at', $sort)->paginate(20);

        return view('admin.logs', compact('logs'));
    }

    public function storeEditLog($invoiceId)
    {
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'edit',
            'description' => 'Edited invoice #' . $invoiceId,
        ]);
    }
}
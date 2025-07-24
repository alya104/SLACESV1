<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Log as ActivityLog; // <-- Import the Log model

class InvoicesController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $classes = ClassModel::all();
        $users = User::where('role', 'student')->get();

        $query = Invoice::with('user', 'class');

        // Optional filters
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(10);

        return view('admin.invoices', compact('invoices', 'classes', 'users'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:unused,used',
            'invoice_date' => 'required|date',
            'student_email' => 'required|email',
        ]);

        $user = User::where('email', $request->student_email)->first();

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'user_id' => $user ? $user->id : null, // <-- Fix: set to null if user not found
            'class_id' => $request->class_id,
            'amount' => $request->amount,
            'status' => $request->status,
            'invoice_date' => $request->invoice_date,
            'email' => $request->student_email, // <-- Always use the email from request
        ]);

        // Do NOT create enrollment here if user is not found
        if ($user) {
            $status = $request->status == 'used' ? 'active' : 'pending';

            \App\Models\Enrollment::updateOrCreate([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
            ], [
                'status' => $status,
                'enrollment_date' => now(),
                'completion_percentage' => 0,
            ]);
        }

        ActivityLog::logAction(Auth::id(), 'create', 'Created invoice #' . $invoice->id); // <-- Log the action

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully!');
    }

    /**
     * Show invoice data for editing (AJAX).
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        return response()->json([
            'success' => true,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $id,
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:unused,used',
            'invoice_date' => 'required|date',
            'student_email' => 'required|email',
        ]);

        $user = User::where('email', $request->student_email)->first();

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'user_id' => $user ? $user->id : null, // <-- allow null
            'class_id' => $request->class_id,
            'amount' => $request->amount,
            'status' => $request->status,
            'invoice_date' => $request->invoice_date,
            'email' => $request->student_email, // <-- always use request email
        ]);

        $status = $request->status == 'used' ? 'active' : 'pending';

        // Only create/update enrollment if user exists
        if ($user) {
            \App\Models\Enrollment::updateOrCreate([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
            ], [
                'status' => $status,
                'enrollment_date' => now(),
                'completion_percentage' => 0,
            ]);
        }

        ActivityLog::logAction(Auth::id(), 'edit', 'Edited invoice #' . $invoice->invoice_number); // <-- Log the action

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        ActivityLog::logAction(Auth::id(), 'delete', 'Deleted invoice #' . $id); // <-- Log the action

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully!');
    }
}
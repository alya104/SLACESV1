<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassModel;
use App\Models\Invoice;
use App\Models\Enrollment;

class GuestController extends Controller
{
    // Show guest dashboard with class browsing
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'guest') {
            return redirect()->route('guest.dashboard');
        }
        $classes = ClassModel::where('status', 'active')->get();
        return view('guest.dashboard', compact('user', 'classes'));
    }

    // Show join class form
    public function showJoinClassForm()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'guest') {
            return redirect()->route('student.dashboard');
        }
        return view('guest.join-class', compact('user'));
    }

    // Handle join class
    public function submitJoinClass(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'guest') {
            return redirect()->route('student.dashboard');
        }

        $request->validate([
            'invoice_number' => 'required|exists:invoices,invoice_number',
        ]);

        $invoice = Invoice::where('invoice_number', $request->invoice_number)
            ->where('email', $user->email)
            ->where('status', 'unused')
            ->first();

        if (!$invoice) {
            return redirect()->back()->withErrors(['invoice_number' => 'Invalid or already used invoice.']);
        }

        // Upgrade role
        $user->role = 'student';
        $user->save();
        Auth::login($user); // Add this line

        // Enroll student
        Enrollment::updateOrCreate([
            'user_id' => $user->id,
            'class_id' => $invoice->class_id,
        ], [
            'status' => 'pending',
            'enrollment_date' => now(),
            'completion_percentage' => 0,
        ]);

        // Mark invoice as used
        $invoice->status = 'used';
        $invoice->user_id = $user->id;
        $invoice->save();

        return redirect()->route('student.dashboard')->with('success', 'You have joined the class!');
    }
}
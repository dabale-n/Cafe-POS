<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\StudentTransaction;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:255|unique:students',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['balance'] = 0;

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $transactions = $student->transactions()->with('user')->latest()->paginate(10);
        return view('students.show', compact('student', 'transactions'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:255|unique:students,student_id,' . $student->id,
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function addTransaction(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:deposit,deduction',
            'payment_method' => 'nullable|required_if:type,deposit|in:cash,mobile',
            'description' => 'nullable|string'
        ]);

        $amount = $request->amount;
        if ($request->type === 'deposit') {
            $student->balance += $amount;
        } else {
            if ($student->balance < $amount) {
                return back()->with('error', 'Insufficient balance.');
            }
            $student->balance -= $amount;
        }

        $student->save();

        StudentTransaction::create([
            'student_id' => $student->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $amount,
            'payment_method' => $request->type === 'deposit' ? $request->payment_method : null,
            'reference' => 'Manual Entry',
            'description' => $request->description,
        ]);

        return back()->with('success', 'Transaction recorded successfully.');
    }
}

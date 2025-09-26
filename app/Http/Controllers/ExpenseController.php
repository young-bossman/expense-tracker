<?php


namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = $request->user()->expenses()->latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string|max:100',
        ]);

        $request->user()->expenses()->create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
{
    if ($expense->user_id !== auth()->id()) {
        abort(403); // Forbidden
    }
    return view('expenses.edit', compact('expense'));
}

public function update(Request $request, Expense $expense)
{
    if ($expense->user_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'category' => 'nullable|string|max:100',
    ]);

    $expense->update($request->all());

    return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
}

public function destroy(Expense $expense)
{
    if ($expense->user_id !== auth()->id()) {
        abort(403);
    }

    $expense->delete();

    return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
}

}

<?php


namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Expense;

use Illuminate\Http\Request;


class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        // eager load category and tags
        $expenses = $request->user()
            ->expenses()
            ->with(['categoryRelation', 'tags'])
            ->latest()
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    public function create()
{
    $categories = Category::whereNull('user_id')
        ->orWhere('user_id', auth()->id())
        ->orderBy('name')
        ->get();

    return view('expenses.create', compact('categories'));
}

   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'category_id' => 'nullable|exists:categories,id',
        'new_category' => 'nullable|string|max:100',
        'tags' => 'nullable|string'
    ]);

    $categoryId = $request->category_id;

    // create new category if user typed one
    if ($request->filled('new_category')) {
        $category = Category::firstOrCreate(
            ['user_id' => auth()->id(), 'name' => $request->new_category]
        );
        $categoryId = $category->id;
    }

    $expense = $request->user()->expenses()->create([
        'title' => $request->title,
        'amount' => $request->amount,
        'date' => $request->date,
        'category_id' => $categoryId,
        // keep the old string column too (optional), helpful for backward compatibility
        'category' => $request->filled('new_category') ? $request->new_category : ($categoryId ? Category::find($categoryId)->name : null),
    ]);

    // tags (comma separated)
    if ($request->filled('tags')) {
        $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['user_id' => auth()->id(), 'name' => $name]);
            $tagIds[] = $tag->id;
        }
        $expense->tags()->sync($tagIds);
    }

    return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
}


    public function edit(Expense $expense)
{
    if ($expense->user_id !== auth()->id()) abort(403);

    $categories = Category::whereNull('user_id')
        ->orWhere('user_id', auth()->id())
        ->orderBy('name')
        ->get();

    $tagList = $expense->tags->pluck('name')->join(', ');

    return view('expenses.edit', compact('expense', 'categories', 'tagList'));
}


public function update(Request $request, Expense $expense)
{
    if ($expense->user_id !== auth()->id()) abort(403);

    $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'category_id' => 'nullable|exists:categories,id',
        'new_category' => 'nullable|string|max:100',
        'tags' => 'nullable|string'
    ]);

    $categoryId = $request->category_id;
    if ($request->filled('new_category')) {
        $category = Category::firstOrCreate(['user_id' => auth()->id(), 'name' => $request->new_category]);
        $categoryId = $category->id;
    }

    $expense->update([
        'title' => $request->title,
        'amount' => $request->amount,
        'date' => $request->date,
        'category_id' => $categoryId,
        'category' => $request->filled('new_category') ? $request->new_category : ($categoryId ? Category::find($categoryId)->name : null),
    ]);

    // tags
    $tagIds = [];
    if ($request->filled('tags')) {
        $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['user_id' => auth()->id(), 'name' => $name]);
            $tagIds[] = $tag->id;
        }
    }
    $expense->tags()->sync($tagIds);

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

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Expenses') }}</h2>

            <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="p-3">Title</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Date</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Tags</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">{{ $expense->title }}</td>
                                    <td class="p-3 font-medium">₵{{ number_format($expense->amount, 2) }}</td>
                                   <td class="p-3">{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}</td>

                                    <td class="p-3">{{ $expense->categoryRelation?->name ?? $expense->category ?? '-' }}</td>
                                    <td class="p-3">
                                        @forelse($expense->tags as $tag)
                                            <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 text-xs rounded mr-1">{{ $tag->name }}</span>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td class="p-3">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="text-blue-600 hover:underline">Edit</a>
                                        |
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this expense?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-3 text-center">No expenses yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

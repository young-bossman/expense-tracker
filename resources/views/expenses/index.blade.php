<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Expenses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('expenses.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded">
                        + Add Expense
                    </a>

                    @if(session('success'))
                        <div class="bg-green-100 text-green-800 p-2 rounded mt-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="w-full mt-4 border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-2 text-left">Title</th>
                                <th class="p-2 text-left">Amount</th>
                                <th class="p-2 text-left">Date</th>
                                <th class="p-2 text-left">Category</th>
                                <th class="p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="border-b">
                                    <td class="p-2">{{ $expense->title }}</td>
                                    <td class="p-2">₵{{ number_format($expense->amount, 2) }}</td>
                                    <td class="p-2">{{ $expense->date }}</td>
                                    <td class="p-2">{{ $expense->category ?? '-' }}</td>
                                    <td class="p-2">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="text-blue-600">Edit</a>
                                        |
                                        <form action="{{ route('expenses.destroy', $expense) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600" 
                                                    onclick="return confirm('Delete this expense?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-2 text-center">No expenses yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

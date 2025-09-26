<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Title</label>
                            <input type="text" name="title" value="{{ $expense->title }}" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Amount</label>
                            <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Date</label>
                            <input type="date" name="date" value="{{ $expense->date }}" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Category</label>
                            <input type="text" name="category" value="{{ $expense->category }}" class="border rounded w-full p-2">
                        </div>

                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                            Update
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

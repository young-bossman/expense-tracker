<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Title</label>
                            <input type="text" name="title" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Amount</label>
                            <input type="number" step="0.01" name="amount" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Date</label>
                            <input type="date" name="date" class="border rounded w-full p-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Category</label>
                            <input type="text" name="category" class="border rounded w-full p-2">
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Save
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

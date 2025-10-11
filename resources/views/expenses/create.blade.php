<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Expense') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ addNew: false }">
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" name="date" type="date" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <x-input-label :value="__('Category')" />
                        <div class="flex gap-2 items-center">
                            <select name="category_id" class="border rounded p-2 flex-1">
                                <option value="">{{ __('— Select category —') }}</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>

                            <button type="button" @click="addNew = !addNew" class="px-3 py-2 bg-gray-200 rounded">
                                {{ __('New') }}
                            </button>
                        </div>

                        <div x-show="addNew" x-cloak class="mt-2">
                            <x-input-label for="new_category" :value="__('New category name')" />
                            <x-text-input id="new_category" name="new_category" class="block mt-1 w-full" />
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="mb-4">
                        <x-input-label for="tags" :value="__('Tags (comma separated)')" />
                        <x-text-input id="tags" name="tags" class="block mt-1 w-full" placeholder="e.g. food, groceries" />
                        <p class="text-xs text-gray-500 mt-1">Create tags by typing comma-separated values.</p>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

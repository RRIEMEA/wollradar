<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Brands</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded bg-green-50 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('brands.store') }}" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">New Brand</label>
                        <input name="name" value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. Lana Grossa" />
                        @error('name')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="px-4 py-2 rounded bg-indigo-600 text-gray-700 hover:bg-indigo-700">
                        Add
                    </button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-3">Your Brands</div>

                @if($brands->isEmpty())
                    <div class="text-gray-500">No brands yet.</div>
                @else
                    <ul class="divide-y">
                        @foreach($brands as $brand)
                            <li class="py-3 flex items-center justify-between">
                                <div class="font-medium text-gray-900">{{ $brand->name }}</div>
                                <form method="POST" action="{{ route('brands.destroy', $brand) }}"
                                      onsubmit="return confirm('Delete {{ $brand->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

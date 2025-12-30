<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Yarn</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('yarns.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    @include('yarns._form')

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('yarns.index') }}" class="text-gray-700 hover:text-gray-900">Cancel</a>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-gray-700 hover:bg-indigo-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

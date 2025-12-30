<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Yarn #{{ $yarn->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded bg-green-50 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('yarns.update', $yarn) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('yarns._form', ['yarn' => $yarn])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('yarns.index') }}" class="text-gray-700 hover:text-gray-900">Back</a>

                        <x-primary-button>
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>

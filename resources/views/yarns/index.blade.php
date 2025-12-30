<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yarns</h2>

            <a href="{{ route('yarns.create') }}"
               class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                Add Yarn
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded bg-green-50 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($yarns->isEmpty())
                    <div class="text-gray-500">No yarns yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-fixed">
                            <thead class="text-xs uppercase text-gray-500 border-b">
                                <tr>
                                    <th class="py-2 px-4 text-left">ID</th>
                                    <th class="py-2 px-4 text-left">Name</th>
                                    <th class="py-2 px-4 text-left">Project</th>
                                    <th class="py-2 px-4 text-left">Details</th>
                                    <th class="py-2 px-4 text-right">Qty</th>
                                    <th class="py-2 px-4 text-right">Length (m)</th>
                                    <th class="py-2 px-4 text-right">Weight (g)</th>
                                    <th class="py-2 px-4 text-left">Notes</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-800 divide-y">
                                @foreach($yarns as $yarn)
                                    <tr class="align-top hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-500 whitespace-nowrap">
                                            #{{ $yarn->id }}
                                        </td>

                                        {{-- NAME + THUMBNAIL --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                @if($yarn->photo_path)
                                                    <a href="{{ Storage::url($yarn->photo_path) }}"
                                                       target="_blank"
                                                       class="shrink-0 inline-block">
                                                        <img
                                                            src="{{ Storage::url($yarn->photo_path) }}"
                                                            alt="Yarn photo"
                                                            class="h-14 w-14 rounded border object-cover"
                                                            loading="lazy"
                                                        />
                                                    </a>
                                                @else
                                                    <div class="h-14 w-14 rounded border bg-gray-50 text-gray-400 flex items-center justify-center shrink-0">
                                                        —
                                                    </div>
                                                @endif

                                                <div class="min-w-0">
                                                    <div class="font-medium text-gray-900 truncate">
                                                        {{ $yarn->name ?? ('Yarn #' . $yarn->id) }}
                                                    </div>
                                                    {{-- optional: kleine Zusatzinfo unter Name, falls du willst --}}
                                                    {{-- <div class="text-xs text-gray-500">#{{ $yarn->id }}</div> --}}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="font-medium text-gray-900 truncate">
                                                {{ $yarn->project?->name ?? '—' }}
                                            </div>
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="text-gray-700 space-y-0.5">
                                                <div><span class="text-gray-500">Color:</span> {{ $yarn->color?->name ?? '—' }}</div>
                                                <div><span class="text-gray-500">Material:</span> {{ $yarn->material?->name ?? '—' }}</div>
                                                <div><span class="text-gray-500">Brand:</span> {{ $yarn->brand?->name ?? '—' }}</div>
                                                <div><span class="text-gray-500">Location:</span> {{ $yarn->location?->name ?? '—' }}</div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-4 text-right tabular-nums whitespace-nowrap">
                                            {{ number_format((float) $yarn->quantity, 0, ',', '.') }}
                                        </td>

                                        <td class="py-3 px-4 text-right tabular-nums whitespace-nowrap">
                                            {{ $yarn->length_m === null ? '—' : number_format((float) $yarn->length_m, 0, ',', '.') }}
                                        </td>

                                        <td class="py-3 px-4 text-right tabular-nums whitespace-nowrap">
                                            {{ $yarn->weight_g === null ? '—' : number_format((float) $yarn->weight_g, 0, ',', '.') }}
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="text-gray-600 whitespace-normal break-words">
                                                {{ $yarn->notes ?? '' }}
                                            </div>
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="flex flex-col items-start gap-2">
                                                <a class="text-indigo-600 hover:text-indigo-800"
                                                   href="{{ route('yarns.edit', $yarn) }}">
                                                    Edit
                                                </a>

                                                <form method="POST"
                                                      action="{{ route('yarns.destroy', $yarn) }}"
                                                      onsubmit="return confirm('Delete Yarn #{{ $yarn->id }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:text-red-800">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $yarns->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

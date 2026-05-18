<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Centralized Research Document Repository') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search Bar -->
            <form method="GET" action="{{ route('repository.index') }}" class="mb-6 flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, abstract, or research field..." class="form-input rounded-md shadow-sm mt-1 block w-full" />
                <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Search
                </button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if($completedResearches->isEmpty())
                        <p class="text-gray-500">No completed research found in the repository.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($completedResearches as $research)
                                <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-md transition">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $research->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-4"><strong>Lead Researcher:</strong> {{ $research->user->name }}</p>
                                    
                                    @if($research->collaborators->count() > 0)
                                        <p class="text-xs text-gray-500 mb-4"><strong>Collaborators:</strong> 
                                            {{ $research->collaborators->pluck('name')->join(', ') }}
                                        </p>
                                    @endif

                                    <p class="text-sm text-gray-700 mb-4 line-clamp-3">
                                        {{ Str::limit($research->abstract, 150) }}
                                    </p>

                                    <div class="flex justify-between items-center mt-4 border-t pt-4">
                                        <span class="text-xs font-semibold px-2 py-1 bg-green-100 text-green-800 rounded">
                                            Completed: {{ $research->phase_updated_at ? \Carbon\Carbon::parse($research->phase_updated_at)->format('M d, Y') : 'N/A' }}
                                        </span>
                                        <a href="{{ route('proposal.show', $research->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View Details &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $completedResearches->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

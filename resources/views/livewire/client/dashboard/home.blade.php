<div class="p-4 space-y-10">
    <div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-7xl mx-auto">
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book" class="size-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path></svg>
                <h4 class="text-4xl font-medium">Syllabus Generator</h4>
            </div>
            <a href="{{ route('syllabus') }}" wire:navigate aria-label="Create Syllabus" class="flex items-center bg-red-700 text-white px-4 py-2 space-x-2 rounded hover:bg-red-800 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                </svg>
                <span>Create Syllabus</span>
            </a>
        </div>
        <div class="border bg-white rounded shadow-sm">
            <div class="border-b p-2 text-sm font-medium text-gray-700 flex space-x-2 items-center">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bell" class="size-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"></path></svg>
                <h4>Notifications</h4>
            </div>
        </div>
    </div>
    <div class="border-b-2 border-gray-300 pb-2 flex items-center justify-between text-lg max-w-7xl mx-auto">
        <h4>My Syllabi</h4>
        <div class="flex items-center space-x-2">
            <a href="{{ route('home') }}" wire:navigate class="hover:underline">Home</a>
            <span>></span>
            <p>Syllabi</p>
        </div>
    </div>

    {{-- Search and Filter Controls --}}
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search syllabi..." 
                        class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    >
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="under_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            @if($search || $statusFilter)
                <button wire:click="clearFilters" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
        @forelse($syllabi as $syllabus)
        <div class="border bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-red-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-600">Syllabus #{{ $syllabus->id }}</span>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ 
                        $syllabus->status === 'approved' ? 'bg-green-100 text-green-800' : 
                        ($syllabus->status === 'under_review' ? 'bg-yellow-100 text-yellow-800' : 
                        ($syllabus->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                        'bg-gray-100 text-gray-800')) 
                    }}">
                        {{ ucfirst(str_replace('_', ' ', $syllabus->status)) }}
                    </span>
                </div>

                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                        {{ $syllabus->name }}
                    </h3>
                    <p class="text-sm text-gray-600">Course: {{ $syllabus->course->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">Code: {{ $syllabus->course->code ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">College: {{ $syllabus->course->college->name ?? 'N/A' }}</p>
                </div>

                <div class="flex items-center justify-between pt-2 border-t">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-xs text-gray-500">{{ $syllabus->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('syllabus.edit', $syllabus) }}" wire:navigate class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                            Edit
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('syllabus.pdf.view', $syllabus) }}" target="_blank" rel="noopener" class="text-red-700 hover:text-red-800 text-sm font-medium">
                            Open PDF
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('syllabus.pdf.download', $syllabus) }}" class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No syllabi found</h3>
                <p class="text-gray-600 mb-6">
                    @if($search || $statusFilter)
                        No syllabi match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't created any syllabi yet. Get started by creating your first syllabus.
                    @endif
                </p>
                @if(!$search && !$statusFilter)
                    <a href="{{ route('syllabus') }}" wire:navigate class="inline-block bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                        Create Syllabus
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($syllabi->hasPages())
        <div class="max-w-7xl mx-auto mt-8">
            {{ $syllabi->links() }}
        </div>
    @endif
    </div>

</div>
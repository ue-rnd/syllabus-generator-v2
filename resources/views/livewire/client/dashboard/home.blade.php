@extends('livewire.client.dashboard.base')
@section('content')
<div class="p-4" x-data="{showAddModel: false}">
    <div class="space-y-10">
        <div class="grid grid-cols-2 gap-2 max-w-7xl mx-auto">
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book" class="size-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path></svg>
                    <h4 class="text-4xl font-medium">Syllabus Generator</h4>
                </div>
                <button class="flex items-center bg-red-700 text-white px-4 py-2 space-x-2 rounded" @click="showAddModel = !showAddModel">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                    </svg>
                    <span>Add</span>
                </button>
            </div>
            <div class="border bg-white rounded shadow-sm">
                <div class="border-b p-2 text-sm font-medium text-gray-700 flex space-x-2 items-center">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bell" class="size-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"></path></svg>
                    <h4>Notifications</h4>
                </div>
            </div>
        </div>
        <div class="border-b-2 border-gray-300 pb-2 flex items-center justify-between text-lg max-w-7xl mx-auto">
            <h4>Available</h4>
            <div class="flex items-center space-x-2">
                <p>Home</p>
                <span>></span>
                <p>Curricular</p>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4 max-w-7xl mx-auto">
            @foreach([1,2,3,4] as $count)
            <div class="border bg-white p-4 rounded shadow">
                <div class="space-y-2">
                    <img src="http://placehold.co/175x175" alt="College Logo" class="mx-auto">
                    <h4 class="text-center text-red-700 font-medium">CCSS</h4>
                    <h2 class="text-center">College of Computer Studies and Systems</h2>
                </div>
            </div>
            @endforeach
        </div>
        <div class="border-b-2 border-gray-300 pb-2 flex items-center justify-between text-xl max-w-7xl mx-auto">
            <h4>Unavailable</h4>
        </div>
        <div class="grid grid-cols-4 gap-4 max-w-7xl mx-auto">
            @foreach([1,2,3,4] as $count)
            <div class="border bg-white p-4 rounded shadow opacity-70">
                <div class="space-y-2">
                    <img src="http://placehold.co/175x175" alt="College Logo" class="mx-auto">
                    <h4 class="text-center text-red-700 font-medium">CCSS</h4>
                    <h2 class="text-center">College of Computer Studies and Systems</h2>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div x-show="showAddModel" x-transition class="fixed inset-0 top-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white max-w-4xl w-full rounded" @click.away="showAddModel = false">
            <div class="p-4 bg-red-700 text-white rounded-t flex items-center justify-between">
                <h2 class="text-xl font-medium">Add</h2>
                <button class="hover:text-gray-300 ease duration-200" @click="showAddModel = false">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>
            <div class="">
                <div x-data="{ activeTab: 'syllabus' }">
                    <div class="flex border-b">
                        <button @click="activeTab = 'syllabus'" :class="{'border-red-700 text-red-700': activeTab === 'syllabus'}" class="px-4 py-2 border-b-2">Add Syllabus</button>
                        <button @click="activeTab = 'subject'" :class="{'border-red-700 text-red-700': activeTab === 'subject'}" class="px-4 py-2 border-b-2">Add Subject</button>
                    </div>
                    <div x-show="activeTab === 'syllabus'">
                        <div class="space-y-4 p-6 border-b">
                            <div class="space-y-1">
                                <label for="college" class="text-sm font-medium text-gray-600">College</label>
                                <select name="college" id="college" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                    <option value="">Select College</option>
                                    <option value="ccss">CCSS</option>
                                    <option value="cba">CBA</option>
                                    <option value="coe">COE</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="program" class="text-sm font-medium text-gray-600">Program</label>
                                <select name="program" id="program" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                    <option value="">Select Program</option>
                                    <option value="bsit">BSIT</option>
                                    <option value="bshrm">BSHRM</option>
                                    <option value="bsa">BSA</option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="space-y-1">
                                    <label for="year" class="text-sm font-medium text-gray-600">Year</label>
                                    <select name="year" id="year" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                        <option value="">Select Year</option>
                                        <option value="1st">1st Year</option>
                                        <option value="2nd">2nd Year</option>
                                        <option value="3rd">3rd Year</option>
                                        <option value="4th">4th Year</option>
                                        <option value="summer">Summer</option>
                                        <option value="elective">Elective</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label for="semester" class="text-sm font-medium text-gray-600">Semester</label>
                                    <select name="semester" id="semester" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label for="subject" class="text-sm font-medium text-gray-600">Subject</label>
                                <select name="subject" id="subject" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                    <option value="">Select Subject</option>
                                    <option value="math">Mathematics</option>
                                    <option value="science">Science</option>
                                    <option value="history">History</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-4 flex items-center justify-end space-x-4 bg-gray-50 rounded-b">
                            <button class="bg-green-600 ease duration-200 hover:bg-green-700 text-white px-4 py-1 rounded">Save</button>
                            <button class="bg-gray-200 border border-gray-300 ease duration-200 hover:bg-gray-300 px-2 py-1 rounded">Cancel</button>
                        </div>
                    </div>
                    <div x-show="activeTab === 'subject'" class="">
                        <div class="space-y-4 p-6 border-b">
                            <div class="space-y-1">
                                <label for="college_name" class="text-sm font-medium text-gray-600">College</label>
                                <select name="college" id="college_name" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                    <option value="">Select College</option>
                                    <option value="college1">College 1</option>
                                    <option value="college2">College 2</option>
                                    <option value="college3">College 3</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="programs" class="text-sm font-medium text-gray-600">Programs</label>
                                <div class="flex flex-wrap gap-4">
                                    @foreach (['Program 1', 'Program 2', 'Program 3', 'Program 4', 'Program 5', 'Program 6', 'Program 7', 'Program 8', 'Program 9'] as $program)
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="programs[]" id="{{ $program }}" value="{{ $program }}">
                                        <label for="{{ $program }}" class="ml-2">{{ $program }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="space-y-1">
                                    <label for="year" class="text-sm font-medium text-gray-600">Year</label>
                                    <select name="year" id="year" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                        <option value="">Select Year</option>
                                        <option value="1st">1st Year</option>
                                        <option value="2nd">2nd Year</option>
                                        <option value="3rd">3rd Year</option>
                                        <option value="4th">4th Year</option>
                                        <option value="summer">Summer</option>
                                        <option value="elective">Elective</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label for="semester" class="text-sm font-medium text-gray-600">Semester</label>
                                    <select name="semester" id="semester" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 flex items-center justify-end space-x-4 bg-gray-50 rounded-b">
                            <button class="bg-green-600 ease duration-200 hover:bg-green-700 text-white px-4 py-1 rounded">Save</button>
                            <button class="bg-gray-200 border border-gray-300 ease duration-200 hover:bg-gray-300 px-2 py-1 rounded">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="p-4 space-y-10">
    <div class="grid grid-cols-2 gap-2 max-w-7xl mx-auto">
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book" class="size-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path></svg>
                <h4 class="text-4xl font-medium">Syllabus Generator</h4>
            </div>
            <button class="flex items-center bg-red-700 text-white px-4 py-2 space-x-2 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                </svg>
                <span>Add</span>
            </button>
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
            <p>Home</p>
            <span>></span>
            <p>Syllabi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
        {{-- TODO: Replace with dynamic syllabi data filtered by user's college --}}
        {{-- $syllabi = auth()->user()->college?->syllabi()->with(['course', 'user'])->published()->latest()->get() ?? collect() --}}
        @foreach([1,2,3,4,5,6] as $count)
        <div class="border bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-red-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-600">Syllabus #{{$count}}</span>
                    </div>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Published</span>
                </div>

                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                        Introduction to Computer Science
                    </h3>
                    <p class="text-sm text-gray-600">Course Code: CS101</p>
                    <p class="text-sm text-gray-600">College: College of Computer Studies and Systems</p>
                </div>

                <div class="flex items-center justify-between pt-2 border-t">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-xs text-gray-500">Updated 2 days ago</span>
                    </div>
                    <button class="text-red-700 hover:text-red-800 text-sm font-medium">
                        View Details →
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty state when no syllabi exist --}}
    {{-- TODO: Replace with actual syllabi count --}}
    {{-- @if($syllabi->isEmpty()) --}}
    @if(count([1,2,3,4,5,6]) == 0)
    <div class="text-center py-12 max-w-7xl mx-auto">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No syllabi found</h3>
        <p class="text-gray-600 mb-6">You haven't created any syllabi yet. Get started by creating your first syllabus.</p>
        <button class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800 transition-colors">
            Create Syllabus
        </button>
    </div>
    @endif
</div>
@endsection
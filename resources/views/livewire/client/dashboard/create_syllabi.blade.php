@extends('livewire.client.dashboard.base')
@section('content')
<div class="max-w-7xl mx-auto p-4 space-y-2">
    <div class="w-full mx-auto">
        <button class="bg-red-700 text-white px-4 py-1 rounded hover:bg-red-800 ease duration-200">Save</button>
    </div>
    <div class="w-full border mx-auto bg-white rounded shadow-sm" x-data="{ activeTab: 'learning_matrix' }">
        <div class="flex border-b">
            <button @click="activeTab = 'description'" :class="{'border-red-700 text-red-700': activeTab === 'description'}" class="px-4 py-2 border-b-2">Syllabus Description</button>
            <button @click="activeTab = 'learning_matrix'" :class="{'border-red-700 text-red-700': activeTab === 'learning_matrix'}" class="px-4 py-2 border-b-2">Learning Matrix</button>
            <button @click="activeTab = 'references'" :class="{'border-red-700 text-red-700': activeTab === 'references'}" class="px-4 py-2 border-b-2">References</button>
            <button @click="activeTab = 'other_elements'" :class="{'border-red-700 text-red-700': activeTab === 'other_elements'}" class="px-4 py-2 border-b-2">Other elements</button>
            <button @click="activeTab = 'prepared_by'" :class="{'border-red-700 text-red-700': activeTab === 'prepared_by'}" class="px-4 py-2 border-b-2">Prepared By</button>
        </div>
        <div x-show="activeTab === 'learning_matrix'">
            <div class="p-4 space-y-6">
                <h4 class="font-medium text-xl">Week 1</h4>
                <div class="md:flex md:max-w-64 block items-center md:space-x-4">
                    <div class="space-y-1 w-full max-w-64">
                        <label for="lecture_hour" class="text-sm font-medium text-gray-600">Lecture Hours</label>
                        <input type="number" name="lecture_hour" id="lecture_hour" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                    <div class="space-y-1 w-full max-w-64">
                        <label for="laboratory_hour" class="text-sm font-medium text-gray-600">Laboratory Hours</label>
                        <input type="number" name="laboratory_hour" id="laboratory_hour" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                </div>
                <div class="space-y-1 w-full">
                    <label for="learning_outcome" class="text-sm font-medium text-gray-600">Learning Outcome</label>
                    <textarea name="learning_outcome" id="learning_outcome" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" placeholder="Enter your statement here." rows="6"></textarea>
                </div>
                <div class="space-y-1 w-full">
                    <label for="content" class="text-sm font-medium text-gray-600">Content</label>
                    <textarea name="content" id="content" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" placeholder="Enter your statement here." rows="6"></textarea>
                </div>
                <h4 class="font-medium text-lg">Teaching Learning Activity 1</h4>
                <div class="md:flex md:max-w-xl border block items-center md:space-x-4">
                    <div class="space-y-1">
                        <label for="title" class="text-sm font-medium text-gray-600">Title</label>
                        <input type="text" name="title" id="title" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                    <div class="space-y-1">
                        <label for="" class="text-sm font-medium text-gray-600">Modality</label>
                        <select name="course_modality" id="course_modality" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                            <option value="onsite">Onsite</option>
                            <option value="offsite">Offsite</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="activeTab === 'description'">
            <div class="p-4 space-y-6">
                <div class="space-y-1 w-full max-w-64">
                    <label for="school_year" class="text-sm font-medium text-gray-600">School Year</label>
                    <select name="school_year" id="school_year" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        <option value="2025-2026">2025 - 2026</option>
                    </select>
                </div>
                <div class="md:flex block items-center md:space-x-4">
                    <div class="space-y-1">
                        <label for="course_code" class="text-sm font-medium text-gray-600">Course Code</label>
                        <input type="text" name="course_code" id="course_code" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                    <div class="space-y-1">
                        <label for="course_title" class="text-sm font-medium text-gray-600">Course Title</label>
                        <input type="text" name="course_title" id="course_title" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                </div>
                <h4 class="font-medium text-lg">Credit Units</h4>
                <div class="md:flex block items-center md:space-x-4">
                    <div class="space-y-1">
                        <label for="lecture_units" class="text-sm font-medium text-gray-600">Lecture Units</label>
                        <input type="text" name="lecture_units" id="lecture_units" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                    <div class="space-y-1">
                        <label for="laboratory_units" class="text-sm font-medium text-gray-600">Laboratory Units</label>
                        <input type="text" name="laboratory_units" id="laboratory_units" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                </div>
                <div class="space-y-1">
                    <label for="course_modality" class="text-sm font-medium text-gray-600">Course Modality</label><br/>
                    <select name="course_modality" id="course_modality" class="md:max-w-64 w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        <option value="onsite">Onsite</option>
                        <option value="offsite">Offsite</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <h4 class="font-medium text-lg">Pre-Requisite(s)</h4>
                <div class="space-y-1">
                    
                    <div class="grid grid-cols-2 max-w-xl w-full gap-4">
                        <div>
                            <label for="course_code_1" class="text-sm font-medium text-gray-600">Course Code 1</label>
                            <input type="text" name="course_code_1" id="course_code_1" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                        <div>
                            <label for="course_title_1" class="text-sm font-medium text-gray-600">Course Title 1</label>
                            <input type="text" name="course_title_1" id="course_title_1" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                        <div>
                            <label for="course_code_2" class="text-sm font-medium text-gray-600">Course Code 2</label>
                            <input type="text" name="course_code_2" id="course_code_2" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                        <div>
                            <label for="course_title_2" class="text-sm font-medium text-gray-600">Course Title 2</label>
                            <input type="text" name="course_title_2" id="course_title_2" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                        <div>
                            <label for="course_code_3" class="text-sm font-medium text-gray-600">Course Code 3</label>
                            <input type="text" name="course_code_3" id="course_code_3" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                        <div>
                            <label for="course_title_3" class="text-sm font-medium text-gray-600">Course Title 3</label>
                            <input type="text" name="course_title_3" id="course_title_3" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                        </div>
                    </div>
                </div>
                <div class="space-y-1">
                    <label for="course_description" class="text-sm font-medium text-gray-600">Course Description</label>
                    <textarea name="course_description" id="course_description" class="w-full border p-2 rounded focus:outline-none focus:ring-1 focus:ring-red-400" rows="10" placeholder="Enter your statement here."></textarea>
                </div>
                <div class="space-y-1">
                    <label for="course_outcome" class="text-sm font-medium text-gray-600">Course Outcome</label>
                    <textarea name="course_outcome" id="course_outcome" class="w-full border p-2 rounded focus:outline-none focus:ring-1 focus:ring-red-400" rows="10" placeholder="Enter your statement here."></textarea>
                </div>
            </div>
        </div>
    </div>
    
@endsection
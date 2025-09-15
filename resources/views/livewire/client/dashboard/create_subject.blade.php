@extends('livewire.client.dashboard.base')
@section('content')
<div class="p-4 space-y-2">
    <div class="max-w-7xl mx-auto">
        <button class="bg-red-700 text-white px-4 py-1 rounded hover:bg-red-800 ease duration-200">Save</button>
    </div>
    <div class="max-w-7xl border mx-auto bg-white rounded shadow-sm" x-data="{ activeTab: 'description' }">
        <div class="flex border-b">
            <button @click="activeTab = 'outcomes'" :class="{'border-red-700 text-red-700': activeTab === 'outcomes'}" class="px-4 py-2 border-b-2">Program Outcomes</button>
            <button @click="activeTab = 'description'" :class="{'border-red-700 text-red-700': activeTab === 'description'}" class="px-4 py-2 border-b-2">Syllabus Description</button>
        </div>
        <div x-show="activeTab === 'outcomes'">
            <div class="p-4 space-y-8">
                <h1>Program Outcomes</h1>
            </div>
        </div>
        <div x-show="activeTab === 'description'">
            <div class="p-4 space-y-8">
                <div class="flex items-center space-x-4">
                    <div class="space-y-1">
                        <label for="course_code" class="text-sm font-medium text-gray-600">Course Code</label>
                        <input type="text" name="course_code" id="course_code" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                    <div class="space-y-1">
                        <label for="course_title" class="text-sm font-medium text-gray-600">Course Title</label>
                        <input type="text" name="course_title" id="course_title" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    </div>
                </div>
                <div class="flex items-center space-x-4">
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
                <div class="space-y-1">
                    <label for="course_modality" class="text-sm font-medium text-gray-600">Subject</label>
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
            </div>
        </div>
    </div>
</div>
@endsection
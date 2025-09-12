@extends('components.layouts.app.main-layout')
@section('content')
    <div class="p-4 space-y-10">
        <div class="max-w-7xl border mx-auto bg-white rounded shadow">
            <div class="border-b px-4 py-2 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.517V17.25a.75.75 0 0 0 1.075.676L10 15.082l5.925 2.844A.75.75 0 0 0 17 17.25V4.517c0-1.103-.806-2.068-1.93-2.207A41.403 41.403 0 0 0 10 2Z" clip-rule="evenodd" />
                </svg>
                <h4 class="text-medium text-gray-700">Bookmarks</h4>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left px-4 py-2 font-medium text-sm">Course Code</th>
                        <th class="text-left px-4 py-2 font-medium text-sm">Course Title</th>
                        <th class="text-left px-4 py-2 font-medium text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</td>
                        <td class="px-4 py-2">COURSE TITLE PLACEHOLDER</td>
                        <td class="px-4 py-2">Delete</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center py-2 text-sm font-medium text-gray-600">No bookmarks found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
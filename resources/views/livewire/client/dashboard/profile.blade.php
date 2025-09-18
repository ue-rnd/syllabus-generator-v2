<div class="p-4 space-y-10">
    <div class="max-w-lg border mx-auto p-4 bg-white rounded">
        <form action="" method="post" class="space-y-4 my-4">
            @php($user = auth()->user())
            <div class="mx-auto rounded-full size-24 bg-red-600 text-white flex items-center justify-center text-3xl font-semibold">
                <span>{{ method_exists($user, 'initials') ? $user->initials() : substr($user?->name ?? 'U', 0, 2) }}</span>
            </div>
            <div class="space-y-1">
                <label for="fullname" class="text-sm font-medium text-gray-600">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" value="Thaddeus Toledo">
            </div>
            <div class="space-y-1">
                <label for="email" class="text-sm font-medium text-gray-600">Email</label>
                <input type="text" name="email" id="email" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" value="toledo.thaddeus@ue.edu.ph">
            </div>
            <div class="space-y-1">
                <label for="access" class="text-sm font-medium text-gray-600">Access</label>
                <input type="text" name="access" id="access" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" value="Publisher">
            </div>
            <div class="space-y-1">
                <label for="position" class="text-sm font-medium text-gray-600">Position</label>
                <input type="text" name="position" id="position" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" value="Dean">
            </div>
            <div class="space-y-1">
                <label for="college" class="text-sm font-medium text-gray-600">College</label>
                <input type="text" name="college" id="college" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400" value="CCSS">
            </div>
            <div class="space-y-1">
                <label for="available_college" class="text-sm font-medium text-gray-600">Available College</label>
                <div class="bg-red-400/10 p-2 rounded">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">Basic Education</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CAS</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CBA</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CCSS</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CDENT</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CEDUC</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CENG</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">CLAW</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="available_college" id="available_college">
                        <label for="available_college" class="text-sm font-medium text-gray-600">GS</label>
                    </div>
                </div>
            </div>
            <div class="space-y-1">
                <label for="current_password" class="text-sm font-medium text-gray-600">Current Password</label>
                <input type="password" name="current_password" id="current_password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
            </div>
            <div class="space-y-1">
                <label for="change_password" class="text-sm font-medium text-gray-600">Change Password</label>
                <input type="password" name="change_password" id="change_password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
            </div>
            <div class="space-y-1">
                <label for="retype_change_password" class="text-sm font-medium text-gray-600">Retype Change Password</label>
                <input type="password" name="retype_change_password" id="retype_change_password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <button class="w-full bg-red-800 text-white py-2 rounded hover:bg-red-900 ease duration-200">Change Password</button>
            </div>
        </form>
    </div>
</div>
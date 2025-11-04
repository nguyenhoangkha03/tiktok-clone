<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-gray-900 mb-2">
                Current Password
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Enter your current password"
            />
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-sm text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-bold text-gray-900 mb-2">
                New Password
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Enter new password"
            />
            <p class="text-gray-500 text-xs mt-1">At least 8 characters with letters and numbers</p>
            @if($errors->updatePassword->has('password'))
                <p class="mt-2 text-sm text-red-600">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-gray-900 mb-2">
                Confirm New Password
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Confirm new password"
            />
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-sm text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                Update Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-semibold"
                >✓ Password updated successfully!</p>
            @endif
        </div>
    </form>
</section>

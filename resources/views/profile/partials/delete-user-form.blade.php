<section class="space-y-4" x-data="{ showDeleteModal: false }">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm text-red-800">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </div>

    <button
        @click="showDeleteModal = true"
        class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
    >
        Delete Account
    </button>

    <!-- Delete Modal -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showDeleteModal = false"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full"
        >
            <form method="post" action="{{ route('settings.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <!-- Warning Icon -->
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-black text-gray-900 text-center mb-2">
                    Delete Account?
                </h2>

                <p class="text-gray-600 text-center text-sm mb-6">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
                </p>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-gray-900 mb-2">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full px-4 py-3 bg-[#F1F1F2] border border-gray-200 rounded-lg text-[15px] text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                        placeholder="Enter your password"
                    />
                    @if($errors->userDeletion->has('password'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="flex-1 px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-bold"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 hover:scale-[1.02] font-bold focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

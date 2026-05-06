<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    Edit Faculty Member
                </h2>
                <p class="text-sm text-neutral-body">Update credentials for {{ $user->name }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-brand-navy hover:text-slate-700 transition-colors uppercase tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        {{-- Name Field --}}
                        <div>
                            <label for="name" class="block text-xs font-black text-brand-navy uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-orange focus:ring-brand-orange transition-all text-sm" required>
                            @error('name') <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="block text-xs font-black text-brand-navy uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-orange focus:ring-brand-orange transition-all text-sm" required>
                            @error('email') <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                class="bg-brand-orange hover:bg-orange-600 text-white font-black px-8 py-3 rounded shadow-md transition-all text-xs tracking-widest uppercase">
                                Update Faculty Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Optional Info Card --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <p class="text-xs text-blue-700 leading-relaxed">
                    <span class="font-bold uppercase">Note:</span> Changing a faculty member's email will require them to use the new email for future logins. Password resets should be handled via the "Forgot Password" flow for security.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
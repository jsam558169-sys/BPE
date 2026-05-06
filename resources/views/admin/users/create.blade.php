<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
            {{ __('Create New Faculty Account') }}
        </h2>
        <p class="text-sm text-neutral-body">Register a new faculty member to allow them to borrow equipment.</p>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Full Name*</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="E.G. JOHN DOE" required autofocus
                            class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 uppercase text-sm placeholder:text-gray-300">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Email Address*</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="faculty@university.edu.ph" required
                            class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 text-sm placeholder:text-gray-300">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Password*</label>
                            <input type="password" name="password" required
                                class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 text-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Confirm Password*</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                        <button type="submit" class="bg-brand-navy text-white px-8 py-3 rounded shadow-md text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition">
                            Create Faculty Account
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-gray-400 uppercase hover:text-black tracking-widest transition">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
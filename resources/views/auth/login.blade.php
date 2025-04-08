<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 bg-blue-50 text-blue-700 p-3 rounded-md flex items-center" :status="session('status')">
        @if(session('status'))
            <i class="fas fa-info-circle mr-2"></i> {{ session('status') }}
        @endif
    </x-auth-session-status>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" class="text-gray-700 font-medium" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <x-text-input id="username" 
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                    type="text" 
                    name="username" 
                    :value="old('username')" 
                    placeholder="username"
                    required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
            </div>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password" 
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit" class="w-full flex justify-center items-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i>
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
    <div class="mt-6 text-center text-sm text-gray-600">
        {{ __("Don't have an account?") }} 
        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-800 transition">
            {{ __('Sign up') }}
        </a>
    </div>
    @endif
</x-guest-layout>
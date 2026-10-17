<x-guest-layout>
    <div x-data="{ role: 'cashier', prefill() { 
        if(this.role === 'admin') { 
            // Optional: Prefill for easier testing
            document.getElementById('email').value = 'admin@example.com'; 
            document.getElementById('password').value = 'password'; 
        } else { 
            document.getElementById('email').value = 'test@example.com'; 
            document.getElementById('password').value = 'password'; 
        } 
    }}" x-init="prefill()">
    
        <div class="flex mb-6 space-x-2">
            <button type="button" @click="role = 'cashier'; prefill()" :class="role === 'cashier' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="w-1/2 py-3 rounded-lg font-bold transition duration-200">
                🛒 Cashier Login
            </button>
            <button type="button" @click="role = 'admin'; prefill()" :class="role === 'admin' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="w-1/2 py-3 rounded-lg font-bold transition duration-200">
                🛡️ Admin Login
            </button>
        </div>

        <h2 class="text-xl font-bold mb-4 text-center text-gray-800" x-text="role === 'admin' ? 'Admin Access Portal' : 'Cashier Terminal Access'"></h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3" x-text="role === 'admin' ? 'Log in as Admin' : 'Log in as Cashier'">
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

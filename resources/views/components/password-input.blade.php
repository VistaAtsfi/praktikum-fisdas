@props([
    'id' => 'password',
    'name' => 'password',
    'label' => 'Password',
    'autocomplete' => 'current-password',
])

<div>
    <x-input-label :for="$id" :value="$label" />

    <div class="relative mt-1">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="password"
            autocomplete="{{ $autocomplete }}"
            required
            class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-11"
        />

        <button
            type="button"
            onclick="togglePasswordVisibility('{{ $id }}', this)"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-blue-700 focus:outline-none"
            tabindex="-1"
        >
            {{-- Icon eye --}}
            <svg class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>

            {{-- Icon eye-off --}}
            <svg class="h-5 w-5 icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.362-2.304A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.293 5.03M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3l9 9M3 3l18 18" />
            </svg>
        </button>
    </div>

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>

@once
    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.icon-eye');
            const eyeOffIcon = button.querySelector('.icon-eye-off');

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
@endonce
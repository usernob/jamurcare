<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-cropped.ico') }}">
    <title>{{ config('app.name', 'Laravel') }} - Edit Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-on-surface font-poppins w-screen h-screen">
    <section class="container p-4 h-full mx-auto flex items-center justify-center">
        <form class="w-full max-w-[30rem] grow rounded-xl bg-surface shadow-lg p-4 mb-20" method="POST"
            enctype="multipart/form-data" action="{{ route('profile.edit') }}">
            @csrf
            <div class="flex items-center gap-4 mb-3">
                <a href="{{ route('dashboard.default') }}" class="material-symbols-outlined !text-2xl">arrow_back
                </a>
                <h2 class="text-2xl font-bold">Edit Profile</h2>
            </div>
            <label for="profile-photo" class="text-lg font-semibold">Photo Profile</label>
            <div class="flex items-center gap-4 mt-3 mb-5">
                <img src="{{ $user->getAvatarUrlAttribute() }}" alt="Profile Photo" referrerpolicy="no-referrer"
                    class="rounded-full w-20 aspect-square object-cover object-center bg-surface-container transition-opacity duration-200"
                    id="profile-photo-preview">
                <input type="file" name="profile-photo" id="profile-photo" accept="image/*"
                    class="file:mr-4 file:rounded-full file:border-0 file:bg-surface-container file:px-4 file:py-2 file:text-sm file:font-semibold file:text-on-surface hover:file:bg-primary" />
            </div>

            <label for="username" class="text-lg font-semibold">Username</label>
            <input type="text" name="username" id="username"
                class="mt-3 w-full px-4 py-3 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 @error('username') ring-red-500 @enderror"
                placeholder="Jhon Doe" value="{{ $user->name }}">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-red-500">{{ $error }}</div>
                @endforeach
            @endif
            <button type="submit"
                class="bg-primary text-surface w-full px-2 py-3 rounded-lg text-lg font-semibold mt-6 cursor-pointer">
                Update Profile
            </button>
        </form>
    </section>
</body>
<script>
    function initImagePreview(inputId, imgId) {
        const input = document.getElementById(inputId);
        const img = document.getElementById(imgId);

        if (!input || !img) return;

        input.addEventListener("change", () => {
            const file = input.files?.[0];
            if (!file) return;

            if (!file.type.startsWith("image/")) return;

            const previewUrl = URL.createObjectURL(file);
            img.src = previewUrl;

            img.onload = () => URL.revokeObjectURL(previewUrl);
        });
    }
    initImagePreview("profile-photo", "profile-photo-preview");
</script>

</html>

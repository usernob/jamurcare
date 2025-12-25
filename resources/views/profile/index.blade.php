<!DOCTYPE html>
<html data-theme="dark"
      lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <link type="image/x-icon"
          href="{{ asset('img/logo-cropped.ico') }}"
          rel="icon">
    <title>{{ config('app.name', 'Laravel') }} - Edit Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-on-surface font-poppins h-screen w-screen">
    <section class="container mx-auto flex h-full items-center justify-center p-4">
        <form class="bg-surface mb-20 w-full max-w-[30rem] grow rounded-xl p-4 shadow-lg"
              method="POST"
              enctype="multipart/form-data"
              action="{{ route('profile.edit') }}">
            @csrf
            <div class="mb-3 flex items-center gap-4">
                <a class="material-symbols-outlined !text-2xl"
                   href="{{ route('dashboard.default') }}">arrow_back
                </a>
                <h2 class="text-2xl font-bold">Edit Profile</h2>
            </div>
            <label class="text-lg font-semibold"
                   for="profile-photo">Photo Profile</label>
            <div class="mb-5 mt-3 flex items-center gap-4">
                <img class="bg-surface-container aspect-square w-20 rounded-full object-cover object-center transition-opacity duration-200"
                     id="profile-photo-preview"
                     src="{{ $user->getAvatarUrlAttribute() }}"
                     alt="Profile Photo"
                     referrerpolicy="no-referrer">
                <input class="file:bg-surface-container file:text-on-surface hover:file:bg-primary file:mr-4 file:rounded-full file:border-0 file:px-4 file:py-2 file:text-sm file:font-semibold"
                       id="profile-photo"
                       name="profile-photo"
                       type="file"
                       accept="image/*" />
            </div>

            <label class="text-lg font-semibold"
                   for="username">Username</label>
            <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary @error('username') ring-red-500 @enderror mt-3 w-full rounded-lg border px-4 py-3 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="username"
                   name="username"
                   type="text"
                   value="{{ $user->name }}"
                   placeholder="Jhon Doe">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-red-500">{{ $error }}</div>
                @endforeach
            @endif
            <button class="bg-primary text-surface mt-6 w-full cursor-pointer rounded-lg px-2 py-3 text-lg font-semibold"
                    type="submit">
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

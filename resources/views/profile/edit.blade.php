@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded-md mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex flex-col items-center">
                <label for="photo" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                <div id="image-preview" class="w-40 h-52 border rounded-md overflow-hidden mt-2">
                    @if(Auth::user()->photo)
                        <img id="profile-img" src="{{ route('profile.photo', ['filename' => Auth::user()->photo]) }}" alt="Profile Picture" class="w-full h-full object-cover">
                    @else
                        <img id="profile-img" src="{{ asset('images/default-profile.jpg') }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <input type="file" name="photo" id="photo" class="mt-2 text-sm">
                <input type="hidden" name="cropped_photo" id="cropped_photo">
                <button type="button" class="bg-gray-600 text-white px-3 py-1 rounded-md mt-2 hidden" id="crop-btn">Adjust Image</button>
            </div>

            <div class="col-span-2 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" value="{{ Auth::user()->name }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" value="{{ Auth::user()->email }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Division</label>
                    <select name="division_id" class="w-full px-3 py-2 border rounded-md">
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ Auth::user()->division_id == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Update Profile</button>
            </div>
        </div>
    </form>
</div>

<!-- Croppie Modal -->
<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" id="cropModal">
    <div class="bg-white p-6 rounded-md shadow-md">
        <h3 class="text-lg font-semibold mb-4">Adjust Profile Picture</h3>
        <div id="croppie-container"></div>
        <div class="flex justify-end mt-4 space-x-2">
            <button class="bg-gray-500 text-white px-3 py-1 rounded-md" id="cancel-crop">Cancel</button>
            <button class="bg-blue-600 text-white px-3 py-1 rounded-md" id="save-cropped">Save</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">

<script>
    let croppieInstance;
    let rawImage;
    let fileInput = document.getElementById('photo');

    fileInput.addEventListener('change', function(event) {
        if (event.target.files && event.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                rawImage = e.target.result;
                document.getElementById('profile-img').src = rawImage;
                document.getElementById('crop-btn').classList.remove('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });

    document.getElementById('crop-btn').addEventListener('click', function() {
        document.getElementById('cropModal').classList.remove('hidden');

        if (croppieInstance) {
            croppieInstance.destroy();
        }

        croppieInstance = new Croppie(document.getElementById('croppie-container'), {
            viewport: { width: 300, height: 400, type: 'square' },
            boundary: { width: 350, height: 450 },
            showZoomer: true
        });

        croppieInstance.bind({ url: rawImage });
    });

    document.getElementById('save-cropped').addEventListener('click', function() {
        croppieInstance.result({ type: 'blob', size: 'viewport', format: 'jpeg', quality: 0.9 }).then(function(blob) {
            let objectUrl = URL.createObjectURL(blob);
            document.getElementById('profile-img').src = objectUrl;

            let fileName = fileInput.files[0].name;
            let newFile = new File([blob], fileName, {type: 'image/jpeg'});
            
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(newFile);
            
            fileInput.files = dataTransfer.files;
            
            document.getElementById('cropModal').classList.add('hidden');
        });
    });

    document.getElementById('cancel-crop').addEventListener('click', function() {
        document.getElementById('cropModal').classList.add('hidden');
    });
</script>
@endsection
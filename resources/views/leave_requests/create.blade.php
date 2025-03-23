@extends('layouts.app')

@section('title', 'New Leave Request')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Submit Leave Request</h2>

        <form action="{{ route('leave-requests.store') }}" method="POST">
            @csrf
        
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        
            <div class="mb-4">
                <label class="block font-semibold">Request Date:</label>
                <input type="date" name="request_date" class="w-full border p-2 rounded" required value="{{ old('request_date') }}">
            </div>
        
            <div class="mb-4">
                <label class="block font-semibold">Leave Type:</label>
                <select name="leave_type" class="w-full border p-2 rounded">
                    <option value="Annual Leave" {{ old('leave_type') == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                    <option value="Sick Leave" {{ old('leave_type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="Public Holiday" {{ old('leave_type') == 'Public Holiday' ? 'selected' : '' }}>Public Holiday</option>
                </select>
            </div>
        
            <div class="mb-4">
                <label class="block font-semibold">
                    <input type="hidden" name="is_full_day" value="0"> 
                    <input type="checkbox" name="is_full_day" id="is_full_day" value="1" onchange="toggleTimeFields()"
                        {{ old('is_full_day') ? 'checked' : '' }}> Full Day
                </label>
            </div>
        
            <div id="time_fields" style="{{ old('is_full_day') ? 'display: none;' : 'display: block;' }}">
                <div class="mb-4">
                    <label class="block font-semibold">Start Time:</label>
                    <input type="time" name="start_time" class="w-full border p-2 rounded" value="{{ old('start_time') }}">
                </div>
        
                <div class="mb-4">
                    <label class="block font-semibold">End Time:</label>
                    <input type="time" name="end_time" class="w-full border p-2 rounded" value="{{ old('end_time') }}">
                </div>
            </div>
        
            <div class="mb-4">
                <label class="block font-semibold">Reason:</label>
                <textarea name="reason" class="w-full border p-2 rounded" required>{{ old('reason') }}</textarea>
            </div>
        
            <div class="mb-4">
                <label class="block font-semibold">Remark (Optional):</label>
                <textarea name="remark" class="w-full border p-2 rounded">{{ old('remark') }}</textarea>
            </div>
        
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded w-full">
                Submit Request
            </button>
        </form>
        
    </div>

    <script>
        function toggleTimeFields() {
            const isFullDay = document.getElementById('is_full_day').checked;
            document.getElementById('time_fields').style.display = isFullDay ? 'none' : 'block';
        }
    </script>
@endsection
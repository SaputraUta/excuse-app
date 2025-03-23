@extends('layouts.app')

@section('title', 'Edit Leave Request')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Edit Leave Request</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <strong>Oops! Something went wrong.</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold">Request Date:</label>
                <input type="date" name="request_date" value="{{ old('request_date', $leaveRequest->request_date) }}" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Leave Type:</label>
                <select name="leave_type" class="w-full border p-2 rounded">
                    <option value="Annual Leave" {{ old('leave_type', $leaveRequest->leave_type) == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                    <option value="Sick Leave" {{ old('leave_type', $leaveRequest->leave_type) == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="Public Holiday" {{ old('leave_type', $leaveRequest->leave_type) == 'Public Holiday' ? 'selected' : '' }}>Public Holiday</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">
                    <input type="hidden" name="is_full_day" value="0"> {{-- Ensure value is sent when unchecked --}}
                    <input type="checkbox" name="is_full_day" id="is_full_day" value="1" {{ old('is_full_day', $leaveRequest->is_full_day) ? 'checked' : '' }} onchange="toggleTimeFields()">
                    Full Day
                </label>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Start Time:</label>
                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $leaveRequest->start_time) }}" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">End Time:</label>
                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $leaveRequest->end_time) }}" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Reason:</label>
                <textarea name="reason" class="w-full border p-2 rounded" required>{{ old('reason', $leaveRequest->reason) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Remark (Optional):</label>
                <textarea name="remark" class="w-full border p-2 rounded">{{ old('remark', $leaveRequest->remark) }}</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded shadow-md hover:bg-blue-600">
                Update Request
            </button>
        </form>
    </div>

    <script>
        function toggleTimeFields() {
            const isFullDay = document.getElementById('is_full_day').checked;
            const startTimeField = document.getElementById('start_time');
            const endTimeField = document.getElementById('end_time');

            startTimeField.disabled = isFullDay;
            endTimeField.disabled = isFullDay;

            if (isFullDay) {
                if (!startTimeField.value) startTimeField.value = "09:00";
                if (!endTimeField.value) endTimeField.value = "18:00";
            }
        }

        document.addEventListener("DOMContentLoaded", toggleTimeFields);
    </script>
@endsection

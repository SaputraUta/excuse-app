@extends('layouts.app')

@section('title', 'Edit Leave Request')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-white p-4 sm:p-6 shadow rounded">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Edit Leave Request</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm sm:text-base">
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

            <div class="mb-3 sm:mb-4">
                <label class="block font-semibold text-sm sm:text-base mb-1">Request Date:</label>
                <input type="date" name="request_date" value="{{ old('request_date', $leaveRequest->request_date) }}" class="w-full border p-2 rounded text-sm sm:text-base" required>
            </div>

            <div class="mb-3 sm:mb-4">
                <label class="block font-semibold text-sm sm:text-base mb-1">Leave Type:</label>
                <select name="leave_type" class="w-full border p-2 rounded text-sm sm:text-base">
                    <option value="Annual Leave" {{ old('leave_type', $leaveRequest->leave_type) == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                    <option value="Sick Leave" {{ old('leave_type', $leaveRequest->leave_type) == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="Public Holiday" {{ old('leave_type', $leaveRequest->leave_type) == 'Public Holiday' ? 'selected' : '' }}>Public Holiday</option>
                    <option value="Overtime" {{ old('leave_type') == 'Overtime' ? 'selected' : '' }}>Overtime</option>
                </select>
            </div>

            <div class="mb-3 sm:mb-4">
                <label class="block font-semibold text-sm sm:text-base">
                    <input type="hidden" name="is_full_day" value="0"> {{-- Ensure value is sent when unchecked --}}
                    <input type="checkbox" name="is_full_day" id="is_full_day" value="1" {{ old('is_full_day', $leaveRequest->is_full_day) ? 'checked' : '' }} onchange="toggleTimeFields()" class="mr-2">
                    Full Day
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="mb-3 sm:mb-4">
                    <label class="block font-semibold text-sm sm:text-base mb-1">Start Time:</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $leaveRequest->start_time) }}" class="w-full border p-2 rounded text-sm sm:text-base">
                </div>

                <div class="mb-3 sm:mb-4">
                    <label class="block font-semibold text-sm sm:text-base mb-1">End Time:</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $leaveRequest->end_time) }}" class="w-full border p-2 rounded text-sm sm:text-base">
                </div>
            </div>

            <div class="mb-3 sm:mb-4">
                <label class="block font-semibold text-sm sm:text-base mb-1">Reason:</label>
                <textarea name="reason" class="w-full border p-2 rounded text-sm sm:text-base" rows="3" required>{{ old('reason', $leaveRequest->reason) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold text-sm sm:text-base mb-1">Remark (Optional):</label>
                <textarea name="remark" class="w-full border p-2 rounded text-sm sm:text-base" rows="2">{{ old('remark', $leaveRequest->remark) }}</textarea>
            </div>

            @php
                $isRejected = $leaveRequest->status === 'rejected';
            @endphp

            <button type="submit" 
                class="w-full {{ $isRejected ? 'bg-orange-500 hover:bg-orange-600' : 'bg-blue-500 hover:bg-blue-600' }} 
                text-white px-4 py-2 rounded shadow-md text-sm sm:text-base">
                {{ $isRejected ? 'Resubmit Leave Request' : 'Update Request' }}
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
@extends('layouts.app')

@section('title', 'New Leave Request')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Submit Leave Request</h2>

        <form action="{{ route('leave-requests.store') }}" method="POST" onsubmit="enableRequiredFields()">
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

            {{-- Checkbox for Multiple Dates --}}
            <div class="mb-4">
                <label class="block font-semibold">
                    <input type="checkbox" id="multi_date_check" name="multi_date" value="1" onchange="toggleDateFields()"
                        {{ old('multi_date') ? 'checked' : '' }}> Request for Multiple Dates
                </label>
            </div>

            {{-- Single Request Date (Default) --}}
            <div id="single_date_field" class="mb-4">
                <label class="block font-semibold">Request Date:</label>
                <input type="date" id="request_date" name="request_date" class="w-full border p-2 rounded"
                    value="{{ old('request_date') }}" required>
            </div>

            {{-- Multiple Date Range (Hidden by Default) --}}
            <div id="multi_date_fields" class="mb-4 hidden">
                <div class="mb-4">
                    <label class="block font-semibold">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="w-full border p-2 rounded" 
                        value="{{ old('start_date') }}">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="w-full border p-2 rounded" 
                        value="{{ old('end_date') }}">
                </div>
            </div>

            {{-- Leave Type --}}
            <div class="mb-4">
                <label class="block font-semibold">Leave Type:</label>
                <select name="leave_type" class="w-full border p-2 rounded">
                    <option value="Annual Leave" {{ old('leave_type') == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                    <option value="Sick Leave" {{ old('leave_type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="Public Holiday" {{ old('leave_type') == 'Public Holiday' ? 'selected' : '' }}>Public Holiday</option>
                </select>
            </div>

            {{-- Full Day Checkbox (Checked & Disabled if Multiple Dates is Selected) --}}
            <div class="mb-4">
                <label class="block font-semibold">
                    <input type="hidden" name="is_full_day" value="0"> 
                    <input type="checkbox" name="is_full_day" id="is_full_day" value="1" onchange="toggleTimeFields()" 
                        {{ old('is_full_day') ? 'checked' : '' }}> Full Day
                </label>
            </div>

            {{-- Time Fields (Hidden if Full Day is Selected) --}}
            <div id="time_fields" class="mb-4">
                <div class="mb-4">
                    <label class="block font-semibold">Start Time:</label>
                    <input type="time" name="start_time" class="w-full border p-2 rounded" value="{{ old('start_time') }}">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold">End Time:</label>
                    <input type="time" name="end_time" class="w-full border p-2 rounded" value="{{ old('end_time') }}">
                </div>
            </div>

            {{-- Reason --}}
            <div class="mb-4">
                <label class="block font-semibold">Reason:</label>
                <textarea name="reason" class="w-full border p-2 rounded" required>{{ old('reason') }}</textarea>
            </div>

            {{-- Remark (Optional) --}}
            <div class="mb-4">
                <label class="block font-semibold">Remark (Optional):</label>
                <textarea name="remark" class="w-full border p-2 rounded">{{ old('remark') }}</textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded w-full">
                Submit Request
            </button>
        </form>
    </div>

    {{-- JavaScript to Handle Date Selection and Full-Day Checkbox --}}
    <script>
        function toggleDateFields() {
            const isMultiDate = document.getElementById('multi_date_check').checked;
            const requestDateField = document.getElementById('request_date');
            const startDateField = document.getElementById('start_date');
            const endDateField = document.getElementById('end_date');
            const fullDayCheckbox = document.getElementById('is_full_day');

            if (isMultiDate) {
                document.getElementById('single_date_field').style.display = 'none';
                document.getElementById('multi_date_fields').style.display = 'block';

                requestDateField.removeAttribute('required');
                startDateField.setAttribute('required', 'required');
                endDateField.setAttribute('required', 'required');

                // Auto-check and disable Full Day checkbox
                fullDayCheckbox.checked = true;
                fullDayCheckbox.disabled = true;
                toggleTimeFields();
            } else {
                document.getElementById('single_date_field').style.display = 'block';
                document.getElementById('multi_date_fields').style.display = 'none';

                requestDateField.setAttribute('required', 'required');
                startDateField.removeAttribute('required');
                endDateField.removeAttribute('required');

                // Enable Full Day checkbox again
                fullDayCheckbox.disabled = false;
            }
        }

        function toggleTimeFields() {
            const isFullDay = document.getElementById('is_full_day').checked;
            document.getElementById('time_fields').style.display = isFullDay ? 'none' : 'block';
        }

        // Run on page load to set correct visibility
        window.onload = function() {
            toggleDateFields();
            toggleTimeFields();
        };

        function enableRequiredFields() {
            // Ensure the required attribute is set on the right fields before submitting
            const isMultiDate = document.getElementById('multi_date_check').checked;
            if (isMultiDate) {
                document.getElementById('start_date').setAttribute('required', 'required');
                document.getElementById('end_date').setAttribute('required', 'required');
            } else {
                document.getElementById('request_date').setAttribute('required', 'required');
            }
        }
    </script>
@endsection

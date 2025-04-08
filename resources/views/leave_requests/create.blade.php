@extends('layouts.app')

@section('title', 'New Leave Request')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-white p-4 sm:p-6 shadow rounded">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Submit Leave Request</h2>

        <form action="{{ route('leave-requests.store') }}" method="POST" onsubmit="enableRequiredFields()">
            @csrf

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach 
                    </ul>
                </div>
            @endif

            {{-- Checkbox for Multiple Dates --}}
            <div class="mb-4">
                <label class="flex items-center font-medium text-sm sm:text-base cursor-pointer">
                    <input type="checkbox" id="multi_date_check" name="multi_date" value="1" 
                        class="mr-2 h-4 w-4" onchange="toggleDateFields()"
                        {{ old('multi_date') ? 'checked' : '' }}>
                    <span>Request for Multiple Dates</span>
                </label>
            </div>

            {{-- Single Request Date (Default) --}}
            <div id="single_date_field" class="mb-4">
                <label class="block font-medium text-sm sm:text-base mb-1">Request Date:</label>
                <input type="date" id="request_date" name="request_date" 
                    class="w-full border p-2 rounded text-sm sm:text-base"
                    value="{{ old('request_date') }}" required>
            </div>

            {{-- Multiple Date Range (Hidden by Default) --}}
            <div id="multi_date_fields" class="mb-4 hidden">
                <div class="mb-3">
                    <label class="block font-medium text-sm sm:text-base mb-1">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" 
                        class="w-full border p-2 rounded text-sm sm:text-base" 
                        value="{{ old('start_date') }}">
                </div>

                <div class="mb-3">
                    <label class="block font-medium text-sm sm:text-base mb-1">End Date:</label>
                    <input type="date" id="end_date" name="end_date" 
                        class="w-full border p-2 rounded text-sm sm:text-base" 
                        value="{{ old('end_date') }}">
                </div>
            </div>

            {{-- Leave Type --}}
            <div class="mb-4">
                <label class="block font-medium text-sm sm:text-base mb-1">Leave Type:</label>
                <select name="leave_type" class="w-full border p-2 rounded text-sm sm:text-base">
                    <option value="Annual Leave" {{ old('leave_type') == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                    <option value="Sick Leave" {{ old('leave_type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="Public Holiday" {{ old('leave_type') == 'Public Holiday' ? 'selected' : '' }}>Public Holiday</option>
                    <option value="Overtime" {{ old('leave_type') == 'Overtime' ? 'selected' : '' }}>Overtime</option>
                </select>
            </div>

            {{-- Full Day Checkbox --}}
            <div class="mb-4">
                <label class="flex items-center font-medium text-sm sm:text-base cursor-pointer">
                    <input type="hidden" name="is_full_day" value="0"> 
                    <input type="checkbox" id="is_full_day" name="is_full_day" value="1" 
                        class="mr-2 h-4 w-4" onchange="toggleTimeFields()" 
                        {{ old('is_full_day') ? 'checked' : '' }}> 
                    <span>Full Day</span>
                </label>
            </div>

            {{-- Time Fields (Hidden if Full Day is Selected) --}}
            <div id="time_fields" class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm sm:text-base mb-1">Start Time:</label>
                        <input type="time" name="start_time" 
                            class="w-full border p-2 rounded text-sm sm:text-base" 
                            value="{{ old('start_time') }}">
                    </div>

                    <div>
                        <label class="block font-medium text-sm sm:text-base mb-1">End Time:</label>
                        <input type="time" name="end_time" 
                            class="w-full border p-2 rounded text-sm sm:text-base" 
                            value="{{ old('end_time') }}">
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <div class="mb-4">
                <label class="block font-medium text-sm sm:text-base mb-1">Reason:</label>
                <textarea name="reason" 
                    class="w-full border p-2 rounded text-sm sm:text-base h-20" 
                    required>{{ old('reason') }}</textarea>
            </div>

            {{-- Remark (Optional) --}}
            <div class="mb-5">
                <label class="block font-medium text-sm sm:text-base mb-1">Remark (Optional):</label>
                <textarea name="remark" 
                    class="w-full border p-2 rounded text-sm sm:text-base h-20">{{ old('remark') }}</textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit" 
                class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-3 rounded w-full transition-colors duration-200 text-sm sm:text-base">
                Submit Request
            </button>
        </form>
    </div>

    {{-- JavaScript to Handle Date Selection and Full-Day Checkbox --}}
    <script>
        function toggleDateFields() {
            const isMultiDate = document.getElementById('multi_date_check').checked;
            const singleDateField = document.getElementById('single_date_field');
            const multiDateFields = document.getElementById('multi_date_fields');
            const requestDateField = document.getElementById('request_date');
            const startDateField = document.getElementById('start_date');
            const endDateField = document.getElementById('end_date');
            const fullDayCheckbox = document.getElementById('is_full_day');

            if (isMultiDate) {
                singleDateField.classList.add('hidden');
                multiDateFields.classList.remove('hidden');

                requestDateField.removeAttribute('required');
                startDateField.setAttribute('required', 'required');
                endDateField.setAttribute('required', 'required');

                // Auto-check and disable Full Day checkbox
                fullDayCheckbox.checked = true;
                fullDayCheckbox.disabled = true;
                toggleTimeFields();
            } else {
                singleDateField.classList.remove('hidden');
                multiDateFields.classList.add('hidden');

                requestDateField.setAttribute('required', 'required');
                startDateField.removeAttribute('required');
                endDateField.removeAttribute('required');

                // Enable Full Day checkbox again
                fullDayCheckbox.disabled = false;
            }
        }

        function toggleTimeFields() {
            const isFullDay = document.getElementById('is_full_day').checked;
            const timeFields = document.getElementById('time_fields');
            
            if (isFullDay) {
                timeFields.classList.add('hidden');
            } else {
                timeFields.classList.remove('hidden');
            }
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
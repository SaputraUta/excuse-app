@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-white p-4 sm:p-6 shadow-lg rounded-lg">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Leave Request Details</h2>

        <div class="space-y-3 sm:space-y-4">
            <!-- Status Banner -->
            <div class="w-full py-2 px-4 mb-4 text-center rounded-md text-white font-medium 
                {{ $leaveRequest->status == 'approved' ? 'bg-green-500' : ($leaveRequest->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                {{ ucfirst($leaveRequest->status) }}
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-gray-700 text-sm sm:text-base">
                        <span class="font-semibold block text-gray-600">Request Date</span>
                        {{ $leaveRequest->request_date }}
                    </p>
                </div>

                <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-gray-700 text-sm sm:text-base">
                        <span class="font-semibold block text-gray-600">Leave Type</span>
                        {{ $leaveRequest->leave_type }}
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-gray-700 text-sm sm:text-base">
                    <span class="font-semibold block text-gray-600">Duration</span>
                    {{ $leaveRequest->is_full_day ? 'Full Day' : $leaveRequest->start_time . ' - ' . $leaveRequest->end_time }}
                </p>
            </div>

            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-gray-700 text-sm sm:text-base">
                    <span class="font-semibold block text-gray-600">Reason</span>
                    {{ $leaveRequest->reason }}
                </p>
            </div>

            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-gray-700 text-sm sm:text-base">
                    <span class="font-semibold block text-gray-600">Remark</span>
                    {{ $leaveRequest->remark ?? 'N/A' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-gray-700 text-sm sm:text-base">
                        <span class="font-semibold block text-gray-600">Created</span>
                        {{ $leaveRequest->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <div class="bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-gray-700 text-sm sm:text-base">
                        <span class="font-semibold block text-gray-600">Updated</span>
                        {{ $leaveRequest->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 shadow-sm hover:shadow-md transition-shadow mt-4">
                <h3 class="text-lg font-bold text-gray-700 mb-2">Approvals</h3>
                @if(count($leaveRequest->approvals) > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach ($leaveRequest->approvals as $approval)
                            <li class="py-3 first:pt-0 last:pb-0">
                                <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-800">{{ $approval->admin->name }}</span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $approval->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                               ($approval->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600"><span class="font-medium">Remark:</span> {{ $approval->remark ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500 mt-1"><span class="font-medium">Time:</span> {{ $approval->approved_at ? $approval->approved_at->format('d M Y, H:i') : 'Pending' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm italic">No approvals yet</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row sm:justify-between gap-2">
            <a href="{{ route('leave-requests.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-center sm:text-left">
                <span class="flex items-center justify-center sm:justify-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to List
                </span>
            </a>
            <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition text-center sm:text-left">
                <span class="flex items-center justify-center sm:justify-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit Request
                </span>
            </a>
        </div>
    </div>
@endsection
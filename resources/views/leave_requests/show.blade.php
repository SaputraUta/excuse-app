@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Leave Request Details</h2>

        <div class="space-y-4">
            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Request Date:</strong> {{ $leaveRequest->request_date }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Leave Type:</strong> {{ $leaveRequest->leave_type }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700">
                    <strong>Duration:</strong> 
                    {{ $leaveRequest->is_full_day ? 'Full Day' : $leaveRequest->start_time . ' - ' . $leaveRequest->end_time }}
                </p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Reason:</strong> {{ $leaveRequest->reason }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Remark:</strong> {{ $leaveRequest->remark ?? 'N/A' }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Status:</strong> 
                    <span class="px-2 py-1 text-white text-sm font-medium rounded
                        {{ $leaveRequest->status == 'approved' ? 'bg-green-500' : ($leaveRequest->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Created At:</strong> {{ $leaveRequest->created_at->format('d M Y, H:i') }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="text-gray-700"><strong>Updated At:</strong> {{ $leaveRequest->updated_at->format('d M Y, H:i') }}</p>
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <h3 class="text-lg font-bold">Approvals:</h3>
                <ul>
                    @foreach ($leaveRequest->approvals as $approval)
                        <li class="bg-white p-2 mt-2 rounded shadow">
                            <p><strong>Admin:</strong> {{ $approval->admin->name }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($approval->status) }}</p>
                            <p><strong>Remark:</strong> {{ $approval->remark ?? 'N/A' }}</p>
                            <p><strong>Approved At:</strong> {{ $approval->approved_at ? $approval->approved_at->format('d M Y, H:i') : 'Pending' }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('leave-requests.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                Back
            </a>
            <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                Edit
            </a>
        </div>
    </div>
@endsection

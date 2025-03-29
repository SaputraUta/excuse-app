@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="w-full max-w-lg mx-auto bg-white p-4 sm:p-6 shadow-lg rounded-lg">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Leave Request Details</h2>

    <div class="space-y-3 sm:space-y-4">
        <div class="bg-gray-100 p-2 sm:p-3 rounded">
            <p class="text-sm sm:text-base text-gray-700"><strong>Employee:</strong> {{ $leaveRequest->user->name }}</p>
        </div>

        <div class="bg-gray-100 p-2 sm:p-3 rounded">
            <p class="text-sm sm:text-base text-gray-700"><strong>Request Date:</strong> {{ $leaveRequest->request_date }}</p>
        </div>

        <div class="bg-gray-100 p-2 sm:p-3 rounded">
            <p class="text-sm sm:text-base text-gray-700"><strong>Type:</strong> {{ $leaveRequest->leave_type }}</p>
        </div>

        <div class="bg-gray-100 p-2 sm:p-3 rounded">
            <p class="text-sm sm:text-base text-gray-700"><strong>Reason:</strong> {{ $leaveRequest->reason }}</p>
        </div>

        <div class="bg-gray-100 p-2 sm:p-3 rounded">
            <p class="text-sm sm:text-base text-gray-700"><strong>Status:</strong> 
                <span class="px-2 py-0.5 text-white text-xs sm:text-sm font-medium rounded
                    {{ $leaveRequest->status == 'approved' ? 'bg-green-500' : ($leaveRequest->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                    {{ ucfirst($leaveRequest->status) }}
                </span>
            </p>
        </div>
    </div>

    @if ($leaveRequest->status == 'pending')
        <form action="{{ route('approvals.update', $leaveRequest->id) }}" method="POST" class="mt-4 sm:mt-6">
            @csrf
            @method('PATCH')

            <div class="flex flex-col sm:flex-row sm:justify-between gap-2">
                <button type="submit" name="status" value="approved" class="bg-green-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded text-sm sm:text-base hover:bg-green-600 transition">
                    Approve
                </button>
                <button type="submit" name="status" value="rejected" class="bg-red-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded text-sm sm:text-base hover:bg-red-600 transition">
                    Reject
                </button>
            </div>
        </form>
    @endif

    <div class="mt-4 sm:mt-6">
        <a href="{{ route('approvals.index') }}" class="inline-block bg-gray-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded text-sm sm:text-base hover:bg-gray-600 transition">
            Back
        </a>
    </div>
</div>
@endsection
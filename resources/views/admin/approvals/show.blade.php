@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Leave Request Details</h2>

    <div class="space-y-4">
        <div class="bg-gray-100 p-3 rounded">
            <p class="text-gray-700"><strong>Employee:</strong> {{ $leaveRequest->user->name }}</p>
        </div>

        <div class="bg-gray-100 p-3 rounded">
            <p class="text-gray-700"><strong>Request Date:</strong> {{ $leaveRequest->request_date }}</p>
        </div>

        <div class="bg-gray-100 p-3 rounded">
            <p class="text-gray-700"><strong>Type:</strong> {{ $leaveRequest->leave_type }}</p>
        </div>

        <div class="bg-gray-100 p-3 rounded">
            <p class="text-gray-700"><strong>Reason:</strong> {{ $leaveRequest->reason }}</p>
        </div>

        <div class="bg-gray-100 p-3 rounded">
            <p class="text-gray-700"><strong>Status:</strong> 
                <span class="px-2 py-1 text-white text-sm font-medium rounded
                    {{ $leaveRequest->status == 'approved' ? 'bg-green-500' : ($leaveRequest->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                    {{ ucfirst($leaveRequest->status) }}
                </span>
            </p>
        </div>
    </div>

    @if ($leaveRequest->status == 'pending')
        <form action="{{ route('approvals.update', $leaveRequest->id) }}" method="POST" class="mt-6">
            @csrf
            @method('PATCH')

            <div class="flex justify-between">
                <button type="submit" name="status" value="approved" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                    Approve
                </button>
                <button type="submit" name="status" value="rejected" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Reject
                </button>
            </div>
        </form>
    @endif

    <div class="mt-6">
        <a href="{{ route('approvals.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
            Back
        </a>
    </div>
</div>
@endsection

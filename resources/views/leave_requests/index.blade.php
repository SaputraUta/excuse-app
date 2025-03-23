@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Leave Requests</h2>

        <div class="mb-4 text-right">
            <a href="{{ route('leave-requests.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded">
                + New Request
            </a>
        </div>

        @if (session('success'))
            <p class="text-green-600 mt-2">{{ session('success') }}</p>
        @endif

        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Date</th>
                    <th class="border p-2">Type</th>
                    <th class="border p-2">Duration</th>
                    <th class="border p-2">Reason</th>
                    <th class="border p-2">Remark</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaveRequests as $request)
                    <tr>
                        <td class="border p-2">{{ $request->request_date }}</td>
                        <td class="border p-2">{{ $request->leave_type }}</td>
                        <td class="border p-2">
                            {{ $request->is_full_day ? 'Full Day' : $request->start_time . ' - ' . $request->end_time }}
                        </td>
                        <td class="border p-2">{{ $request->reason }}</td>
                        <td class="border p-2">{{ $request->remark ?? '-' }}</td>
                        <td class="border p-2">
                            <span class="px-2 py-1 text-white text-sm font-medium rounded
                                {{ $request->status == 'approved' ? 'bg-green-500' : ($request->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="border p-2">
                            <a href="{{ route('leave-requests.show', $request->id) }}" class="text-blue-500">View</a> |
                            <a href="{{ route('leave-requests.edit', $request->id) }}" class="text-yellow-500">Edit</a> |
                            <form action="{{ route('leave-requests.destroy', $request->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

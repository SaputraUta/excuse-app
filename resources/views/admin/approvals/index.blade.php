@extends('layouts.app')

@section('title', 'Manage Approvals')

@section('content')
<div class="container mx-auto p-6 bg-white shadow-md rounded-md">
    <h2 class="text-2xl font-bold mb-4">Leave Approvals</h2>

    <div class="mb-4">
        <label class="font-semibold">Filter by Status:</label>
        <select id="status-filter" class="border p-2 rounded" onchange="filterStatus()">
            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
        </select>
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">Employee</th>
                <th class="border px-4 py-2">Leave Type</th>
                <th class="border px-4 py-2">Requested Date</th>
                <th class="border px-4 py-2">Times</th>
                <th class="border px-4 py-2">Reason</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Remark</th>
                <th class="border px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvals as $approval)
            <tr class="border">
                <td class="border px-4 py-2">{{ $approval->leaveRequest->user->name }}</td>
                <td class="border px-4 py-2">{{ $approval->leaveRequest->leave_type }}</td>
                <td class="border px-4 py-2">{{ $approval->leaveRequest->request_date }}</td>
                <td class="border px-4 py-2">
                    {{ $approval->leaveRequest->start_time }} - {{ $approval->leaveRequest->end_time }}
                </td>
                <td class="border px-4 py-2">{{ $approval->leaveRequest->reason }}</td>
                <td class="border px-4 py-2">
                    <span class="px-2 py-1 rounded 
                        {{ $approval->status == 'approved' ? 'bg-green-500 text-white' : 
                        ($approval->status == 'rejected' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-black') }}">
                        {{ ucfirst($approval->status) }}
                    </span>
                </td>
                <td class="border px-4 py-2 text-gray-500 italic">{{ $approval->remark ?? 'No remarks' }}</td>
                <td class="border px-4 py-2">
                    @if($approval->status == 'pending')
                    <form action="{{ route('approvals.store') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                        <input type="text" name="remark" placeholder="Add remark (optional)" class="border p-1 rounded">
                        <button type="submit" name="status" value="approved" class="bg-green-500 text-white px-3 py-1 rounded">Approve</button>
                        <button type="submit" name="status" value="rejected" class="bg-red-500 text-white px-3 py-1 rounded">Reject</button>
                    </form>
                    @endif
                    <button onclick="openModal({{ $approval->leaveRequest->id }})" 
                        class="bg-blue-500 text-white px-3 py-1 rounded inline-block mt-1">View Details</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="approval-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-md w-1/2 shadow-lg relative">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 text-xl">&times;</button>
        <h3 class="text-xl font-bold mb-4 text-center">Leave Request Details</h3>
        <div id="modal-content">
            <p class="text-center">Loading...</p>
        </div>
    </div>
</div>

<script>
    function filterStatus() {
        let status = document.getElementById("status-filter").value;
        window.location.href = `?status=${status}`;
    }

    function openModal(leaveRequestId) {
        document.getElementById("approval-modal").classList.remove("hidden");

        fetch(`/admin/approvals/${leaveRequestId}/details`)
            .then(response => response.json())
            .then(data => {
                let content = `
                    <p><strong>Employee:</strong> ${data.user_name}</p>
                    <p><strong>Leave Type:</strong> ${data.leave_type}</p>
                    <p><strong>Request Date:</strong> ${data.request_date}</p>
                    <p><strong>Start Time:</strong> ${data.start_time ?? 'Full Day'}</p>
                    <p><strong>End Time:</strong> ${data.end_time ?? 'Full Day'}</p>
                    <p><strong>Reason:</strong> ${data.reason}</p>
                    <p><strong>Overall Status:</strong> 
                        <span class="px-2 py-1 rounded ${data.status_class}">
                            ${data.status}
                        </span>
                    </p>
                    <h3 class="text-lg font-bold mt-4">Admin Approvals</h3>
                    <table class="w-full border-collapse border border-gray-300 mt-2">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Admin</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Remark</th>
                                <th class="border px-4 py-2">Approved At</th>
                            </tr>
                        </thead>
                        <tbody>`;
                data.approvals.forEach(approval => {
                    content += `
                        <tr class="border">
                            <td class="border px-4 py-2">${approval.admin_name}</td>
                            <td class="border px-4 py-2">
                                <span class="px-2 py-1 rounded ${approval.status_class}">
                                    ${approval.status}
                                </span>
                            </td>
                            <td class="border px-4 py-2">${approval.remark ?? 'No remark'}</td>
                            <td class="border px-4 py-2">${approval.approved_at ?? 'Pending'}</td>
                        </tr>`;
                });
                content += `</tbody></table>`;
                document.getElementById("modal-content").innerHTML = content;
            });
    }

    function closeModal() {
        document.getElementById("approval-modal").classList.add("hidden");
    }
</script>
@endsection

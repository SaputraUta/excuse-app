@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">My Leave Requests</h2>
            <div>
                <a href="{{ route('leave-requests.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                    + New Request
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="status" id="status" onchange="this.form.submit()"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Month Filter (Only Month Names) -->
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Filter by Month</label>
                <select name="month" id="month" onchange="this.form.submit()"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Months</option>
                    @foreach (range(1, 12) as $m)
                        @php
                            $monthName = \Carbon\Carbon::createFromFormat('m', $m)->format('F'); // e.g., "February"
                        @endphp
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ $monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($leaveRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->request_date)->format('l, j F Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $request->leave_type }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $request->is_full_day ? 'Full Day' : $request->start_time . ' - ' . $request->end_time }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 line-clamp-2">{{ $request->reason }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $request->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($request->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                    @if ($request->resubmission_count > 0)
                                        <span class="text-xs text-gray-500">({{ $request->resubmission_count }}x)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">
                                <div class="flex gap-3">
                                    <a href="{{ route('leave-requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    @if ($request->status === 'pending')
                                        <a href="{{ route('leave-requests.edit', $request->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                            Edit
                                        </a>
                                        <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                            data-id="{{ $request->id }}"
                                            data-date="{{ $request->request_date }}"
                                            data-type="{{ $request->leave_type }}">
                                            Delete
                                        </button>
                                    @endif
                                </div>                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">No leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-4">
            @forelse ($leaveRequests as $request)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                    <div class="flex justify-between mb-3">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($request->request_date)->format('l, j F Y') }}</h3>
                            <p class="text-xs text-gray-500">{{ $request->leave_type }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $request->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($request->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            @if ($request->resubmission_count > 0)
                                <span class="text-xs text-gray-500">({{ $request->resubmission_count }}x)</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Duration</p>
                            <p class="font-medium">
                                {{ $request->is_full_day ? 'Full Day' : $request->start_time . ' - ' . $request->end_time }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="text-xs text-gray-500">Reason</p>
                        <p class="text-sm">{{ $request->reason }}</p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200 flex gap-3">
                        <a href="{{ route('leave-requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                        @if ($request->status === 'pending')
                            <a href="{{ route('leave-requests.edit', $request->id) }}" class="text-yellow-600 hover:text-yellow-800 text-sm">Edit</a>
                            <button type="button" class="text-red-600 hover:text-red-800 text-sm delete-btn"
                                data-id="{{ $request->id }}"
                                data-date="{{ $request->request_date }}"
                                data-type="{{ $request->leave_type }}">
                                Delete
                            </button>
                        @endif
                    </div>                    
                </div>
            @empty
                <p class="text-sm text-gray-500">No leave requests found.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Confirm Delete</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-gray-600">Are you sure you want to delete this leave request?</p>
                <p id="deleteDetailsText" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteDetailsText = document.getElementById('deleteDetailsText');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const date = this.dataset.date;
                const type = this.dataset.type;

                deleteForm.action = "{{ route('leave-requests.destroy', '') }}/" + id;
                deleteDetailsText.textContent = `Type: ${type} | Date: ${date}`;
                deleteModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });
    });

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('deleteModal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Manage Approvals')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Leave Approvals</h2>
            
            <div class="w-full md:w-auto flex flex-col gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm" onchange="updateFilters()">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <!-- Division Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                        <select id="division-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm" onchange="updateFilters()">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- User Filter (only non-admin users) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                        <select id="user-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm" onchange="updateFilters()">
                            <option value="">All Employees</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Reset Filters Button -->
                <div class="mt-2">
                    <button onclick="resetFilters()" class="text-sm text-blue-600 hover:text-blue-800">
                        Reset All Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remark</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($approvals as $approval)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $approval->leaveRequest->user->name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $approval->leaveRequest->user->division->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $approval->leaveRequest->leave_type }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $approval->leaveRequest->request_date }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $approval->leaveRequest->start_time }} - {{ $approval->leaveRequest->end_time }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <div class="line-clamp-2">{{ $approval->leaveRequest->reason }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                    {{ $approval->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                    ($approval->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                                @if ($approval->leaveRequest->resubmission_count > 0)
                                    <span class="text-xs text-gray-500">({{ $approval->leaveRequest->resubmission_count }}x)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 italic">
                            <div class="line-clamp-2">{{ $approval->remark ?? 'No remarks' }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-col gap-2">
                                @if($approval->status == 'pending')
                                <form action="{{ route('approvals.store') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                                    @csrf
                                    <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                                    <input type="text" name="remark" placeholder="Remark" 
                                        class="flex-1 min-w-[120px] text-xs border-gray-300 rounded focus:border-blue-500 focus:ring-blue-500">
                                    <div class="flex gap-2">
                                        <button type="submit" name="status" value="approved" 
                                            class="flex-1 bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                            Approve
                                        </button>
                                        <button type="submit" name="status" value="rejected" 
                                            class="flex-1 bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">
                                            Reject
                                        </button>
                                    </div>
                                </form>
                                @endif
                                <button onclick="openModal({{ $approval->leaveRequest->id }})" 
                                    class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700 w-full">
                                    Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (shown on mobile/tablet) -->
        <div class="md:hidden space-y-4">
            @foreach($approvals as $approval)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Employee</p>
                        <p class="text-sm font-medium">{{ $approval->leaveRequest->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Division</p>
                        <p class="text-sm font-medium">{{ $approval->leaveRequest->user->division->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Leave Type</p>
                        <p class="text-sm font-medium">{{ $approval->leaveRequest->leave_type }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Request Date</p>
                        <p class="text-sm font-medium">{{ $approval->leaveRequest->request_date }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Times</p>
                        <p class="text-sm font-medium">
                            {{ $approval->leaveRequest->start_time }} - {{ $approval->leaveRequest->end_time }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <div class="flex items-center gap-1">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                {{ $approval->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                ($approval->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($approval->status) }}
                            </span>
                            @if ($approval->leaveRequest->resubmission_count > 0)
                                <span class="text-xs text-gray-500">({{ $approval->leaveRequest->resubmission_count }}x)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="text-xs text-gray-500">Reason</p>
                    <p class="text-sm">{{ $approval->leaveRequest->reason }}</p>
                </div>

                <div class="mb-3">
                    <p class="text-xs text-gray-500">Remark</p>
                    <p class="text-sm italic">{{ $approval->remark ?? 'No remarks' }}</p>
                </div>

                @if($approval->status == 'pending')
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <form action="{{ route('approvals.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                        <div>
                            <label for="remark-{{ $approval->id }}" class="sr-only">Remark</label>
                            <input type="text" name="remark" id="remark-{{ $approval->id }}" placeholder="Add remark (optional)" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm">
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" name="status" value="approved" 
                                class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Approve
                            </button>
                            <button type="submit" name="status" value="rejected" 
                                class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <div class="mt-3">
                    <button onclick="openModal({{ $approval->leaveRequest->id }})" 
                        class="w-full bg-blue-50 text-blue-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        View Details
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($approvals->hasPages())
        <div class="mt-6">
            {{ $approvals->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal -->
<div id="approval-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Leave Request Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="modal-content">
                <div class="animate-pulse flex space-x-4">
                    <div class="flex-1 space-y-4 py-1">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show/hide custom date range fields
    document.getElementById('date-range-filter').addEventListener('change', function() {
        const customDateRange = document.getElementById('custom-date-range');
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
            updateFilters();
        }
    });

    function updateFilters() {
    const status = document.getElementById("status-filter").value;
    const division = document.getElementById("division-filter").value;
    const user = document.getElementById("user-filter").value;
    
    let url = `?status=${status}`;
    
    if (division) url += `&division=${division}`;
    if (user) url += `&user=${user}`;
    
    window.location.href = url;
    }

    function resetFilters() {
        window.location.href = "{{ route('approvals.index') }}";
    }

    function updateDateRangeFilter() {
        const dateRange = document.getElementById("date-range-filter").value;
        if (dateRange !== 'custom') {
            updateFilters();
        }
    }

    function applyCustomDateRange() {
        const fromDate = document.getElementById("from-date").value;
        const toDate = document.getElementById("to-date").value;
        
        if (fromDate && toDate) {
            const status = document.getElementById("status-filter").value;
            const division = document.getElementById("division-filter").value;
            const user = document.getElementById("user-filter").value;
            
            let url = `?status=${status}&date_range=custom&from_date=${fromDate}&to_date=${toDate}`;
            
            if (division) url += `&division=${division}`;
            if (user) url += `&user=${user}`;
            
            window.location.href = url;
        }
    }

    function openModal(leaveRequestId) {
        document.getElementById("approval-modal").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");

        // Show loading state
        document.getElementById("modal-content").innerHTML = `
            <div class="animate-pulse flex space-x-4">
                <div class="flex-1 space-y-4 py-1">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>`;

        fetch(`/admin/approvals/${leaveRequestId}/details`)
            .then(response => response.json())
            .then(data => {
                let content = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Employee</p>
                            <p class="font-medium">${data.user_name}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Division</p>
                            <p class="font-medium">${data.division || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Leave Type</p>
                            <p class="font-medium">${data.leave_type}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Request Date</p>
                            <p class="font-medium">${data.request_date}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Start Time</p>
                            <p class="font-medium">${data.start_time || 'Full Day'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Time</p>
                            <p class="font-medium">${data.end_time || 'Full Day'}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">Reason</p>
                        <p class="font-medium whitespace-pre-line">${data.reason}</p>
                    </div>
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">Overall Status</p>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${data.status_class}">
                            ${data.status}
                        </span>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-medium mb-3">Approval History</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Admin</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remark</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approved At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">`;
                
                data.approvals.forEach(approval => {
                    content += `
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${approval.admin_name}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full ${approval.status_class}">
                                    ${approval.status}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">${approval.remark || 'No remark'}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">${approval.approved_at || 'Pending'}</td>
                        </tr>`;
                });
                
                content += `</tbody></table></div></div>`;
                document.getElementById("modal-content").innerHTML = content;
            })
            .catch(error => {
                document.getElementById("modal-content").innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Failed to load details. Please try again.</p>
                        <button onclick="openModal(${leaveRequestId})" class="mt-4 text-blue-600 hover:text-blue-800">
                            Retry
                        </button>
                    </div>`;
            });
    }

    function closeModal() {
        document.getElementById("approval-modal").classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }

    // Close modal when clicking outside content
    document.getElementById('approval-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
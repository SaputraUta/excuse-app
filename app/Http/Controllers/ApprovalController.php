<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Approval;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Get filter values from request
        $status = $request->query('status', 'all');
        $division = $request->query('division');
        $user = $request->query('user');
        $month = $request->query('month');

        // Get all divisions and regular users (non-admins) for filter dropdowns
        $divisions = Division::all();
        $users = User::where('role', '!=', 'admin')->get();

        // Build the query
        $approvals = Approval::where('admin_id', Auth::id())
            ->with(['leaveRequest.user.division'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($division, function ($query) use ($division) {
                return $query->whereHas('leaveRequest.user', function ($q) use ($division) {
                    $q->where('division_id', $division);
                });
            })
            ->when($user, function ($query) use ($user) {
                return $query->whereHas('leaveRequest', function ($q) use ($user) {
                    $q->where('user_id', $user);
                });
            })
            ->when($month, function ($query) use ($month) {
                return $query->whereHas('leaveRequest', function ($q) use ($month) {
                    $q->whereMonth('request_date', $month);
                });
            })
            ->join('leave_requests', 'approvals.leave_request_id', '=', 'leave_requests.id')
            ->orderBy('leave_requests.request_date', 'asc') // Sort by closest request date
            ->select('approvals.*')
            ->paginate(10)
            ->appends($request->query()); // This preserves all query parameters in pagination links

        return view('admin.approvals.index', compact(
            'approvals',
            'status',
            'divisions',
            'users',
            'division',
            'user',
            'month'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'approval_id' => 'required|exists:approvals,id',
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:255'
        ]);

        $approval = Approval::findOrFail($validated['approval_id']);

        if ($approval->admin_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $approval->update([
            'status' => $validated['status'],
            'remark' => $validated['remark'],
            'approved_at' => now(),
        ]);

        // Update the leave request status if all approvals are done
        $this->updateLeaveRequestStatus($approval->leaveRequest);

        return redirect()->route('approvals.index')->with('success', 'Leave request ' . $validated['status'] . ' successfully.');
    }

    protected function updateLeaveRequestStatus($leaveRequest)
    {
        $approvals = $leaveRequest->approvals;
        
        if ($approvals->where('status', 'rejected')->count() > 0) {
            $leaveRequest->update(['status' => 'rejected']);
        } elseif ($approvals->where('status', 'approved')->count() === $approvals->count()) {
            $leaveRequest->update(['status' => 'approved']);
        } else {
            $leaveRequest->update(['status' => 'pending']);
        }
    }

    public function showApprovalDetails($id)
    {
        $leaveRequest = LeaveRequest::with('user', 'approvals.admin')->findOrFail($id);

        return response()->json([
            'user_name' => $leaveRequest->user->name,
            'leave_type' => $leaveRequest->leave_type,
            'request_date' => \Carbon\Carbon::parse($leaveRequest->request_date)->format('l, j F Y'),
            'start_time' => $leaveRequest->start_time,
            'end_time' => $leaveRequest->end_time,
            'reason' => $leaveRequest->reason,
            'division' => $leaveRequest->user->division->name ?? 'N/A',
            'status' => ucfirst($leaveRequest->status),
            'status_class' => $leaveRequest->status === 'approved' ? 'bg-green-100 text-green-800' : 
                            ($leaveRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'),
            'approvals' => $leaveRequest->approvals->map(fn($a) => [
                'admin_name' => $a->admin->name,
                'status' => ucfirst($a->status),
                'status_class' => $a->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                ($a->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'),
                'remark' => $a->remark,
                'approved_at' => $a->approved_at ? \Carbon\Carbon::parse($a->approved_at)->format('l, j F Y') : 'Pending',
            ])
        ]);
    }
}
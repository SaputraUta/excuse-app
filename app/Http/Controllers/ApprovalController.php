<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // Default: Pending

        $approvals = Approval::where('admin_id', Auth::id())
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->with(['leaveRequest.user'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.approvals.index', compact('approvals', 'status'));
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

        return redirect()->route('approvals.index')->with('success', 'Leave request ' . strtolower($validated['status']) . ' successfully.');
    }

    public function showApprovalDetails($id)
    {
        $leaveRequest = LeaveRequest::with('user', 'approvals.admin')->findOrFail($id);

        return response()->json([
            'user_name' => $leaveRequest->user->name,
            'leave_type' => $leaveRequest->leave_type,
            'request_date' => $leaveRequest->request_date,
            'start_time' => $leaveRequest->start_time,
            'end_time' => $leaveRequest->end_time,
            'reason' => $leaveRequest->reason,
            'status' => ucfirst($leaveRequest->status),
            'status_class' => $leaveRequest->status === 'approved' ? 'bg-green-500 text-white' : 
                            ($leaveRequest->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-black'),
            'approvals' => $leaveRequest->approvals->map(fn($a) => [
                'admin_name' => $a->admin->name,
                'status' => ucfirst($a->status),
                'status_class' => $a->status === 'approved' ? 'bg-green-500 text-white' : 
                                ($a->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-black'),
                'remark' => $a->remark,
                'approved_at' => $a->approved_at ? $a->approved_at->format('d M Y, H:i') : 'Pending',
            ])
        ]);
    }
}

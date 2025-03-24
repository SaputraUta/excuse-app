<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::where('user_id', Auth::id())->with('approvals')->get();
        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('leave_requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'leave_type' => 'required|in:Annual Leave,Sick Leave,Public Holiday',
            'is_full_day' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'reason' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        if ($request->boolean('is_full_day')) {
            $validated['start_time'] = '09:00';
            $validated['end_time'] = '18:00';
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => Auth::id(),
            'request_date' => $validated['request_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_full_day' => $request->boolean('is_full_day'),
            'leave_type' => $validated['leave_type'],
            'reason' => $validated['reason'],
            'remark' => $validated['remark'] ?? null,
        ]);

        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return redirect()->route('leave-requests.index')->with('error', 'No admins available to approve your request.');
        }

        foreach ($admins as $admin) {
            Approval::create([
                'leave_request_id' => $leaveRequest->id,
                'admin_id' => $admin->id,
                'status' => 'pending',
                'remark' => null,
                'approved_at' => null,
            ]);
        }

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }


    public function show(LeaveRequest $leaveRequest) {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }    
        $leaveRequest->load('approvals');
        return view('leave_requests.show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        return view('leave_requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'leave_type' => 'required|in:Annual Leave,Sick Leave,Public Holiday',
            'is_full_day' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'reason' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        if ($request->boolean('is_full_day')) {
            $validated['start_time'] = '09:00';
            $validated['end_time'] = '18:00';
        }

        $leaveRequest->update([
            'request_date' => $validated['request_date'],
            'leave_type' => $validated['leave_type'],
            'is_full_day' => $request->boolean('is_full_day'),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'],
            'remark' => $validated['remark'] ?? null,
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated');
    }


    public function destroy(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted');
    }
}

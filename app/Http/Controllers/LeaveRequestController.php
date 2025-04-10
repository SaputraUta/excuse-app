<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::where('user_id', Auth::id())->with('approvals')->get();

        // Original status filter
        if ($request->filled('status')) {
            $query = $query->filter(function ($leaveRequest) use ($request) {
                return $leaveRequest->status === $request->input('status');
            });
        }

        // Month filter (only month, ignoring year)
        if ($request->filled('month')) {
            $month = (int) $request->input('month'); // Convert to integer (1-12)
            $query = $query->filter(function ($leaveRequest) use ($month) {
                return \Carbon\Carbon::parse($leaveRequest->request_date)->month === $month;
            });
        }
        
        // Sort leave requests from nearest to farthest date
        $today = Carbon::today();
        $query = $query->filter(function ($leaveRequest) use ($today) {
            return Carbon::parse($leaveRequest->request_date)->greaterThanOrEqualTo($today);
        });
    
        // Sort from nearest to farthest upcoming request_date
        $leaveRequests = $query->sortBy(function ($leaveRequest) use ($today) {
            $requestDate = Carbon::parse($leaveRequest->request_date);
            return abs($requestDate->timestamp - $today->timestamp);
        })->values();

        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('leave_requests.create');
    }

    public function store(Request $request)
    {
        $isMultiDate = $request->has('multi_date');

        // Validation rules
        $validated = $request->validate([
            'leave_type' => 'required|in:Annual Leave,Sick Leave,Public Holiday,Overtime',
            'reason' => 'required|string',
            'remark' => 'nullable|string',
            'is_full_day' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
        ]);

        if ($isMultiDate) {
            $validated += $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
        } else {
            $validated += $request->validate([
                'request_date' => 'required|date',
            ]);
        }

        // Auto-set Full Day if Multiple Dates
        if ($isMultiDate) {
            $validated['is_full_day'] = true;
            $validated['start_time'] = '09:00';
            $validated['end_time'] = '18:00';
        } elseif ($request->boolean('is_full_day')) {
            $validated['start_time'] = '09:00';
            $validated['end_time'] = '18:00';
        }

        // Find admins
        $admins = User::where('role', 'admin')->get();
        if ($admins->isEmpty()) {
            return redirect()->route('leave-requests.index')->with('error', 'No admins available to approve your request.');
        }

        // Loop through each date if multiple dates are selected
        $dates = $isMultiDate
            ? Carbon::parse($validated['start_date'])->daysUntil(Carbon::parse($validated['end_date']))->toArray()
            : [Carbon::parse($validated['request_date'])];

        foreach ($dates as $date) {
            $leaveRequest = LeaveRequest::create([
                'user_id' => Auth::id(),
                'request_date' => $date->toDateString(),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'is_full_day' => $validated['is_full_day'],
                'leave_type' => $validated['leave_type'],
                'reason' => $validated['reason'],
                'remark' => $validated['remark'] ?? null,
            ]);

            foreach ($admins as $admin) {
                Approval::create([
                    'leave_request_id' => $leaveRequest->id,
                    'admin_id' => $admin->id,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
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
        $leaveRequest->start_time = Carbon::parse($leaveRequest->start_time)->format('H:i');
        $leaveRequest->end_time = Carbon::parse($leaveRequest->end_time)->format('H:i');
        return view('leave_requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'leave_type' => 'required|in:Annual Leave,Sick Leave,Public Holiday,Overtime',
            'is_full_day' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'reason' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        // Ensure the update does not modify multiple days
        if ($request->has('request_dates') && is_array($request->input('request_dates'))) {
            return redirect()->back()->with('error', 'You cannot update multiple leave requests at once.');
        }

        // If full-day, enforce 09:00 - 18:00
        if ($request->boolean('is_full_day')) {
            $validated['start_time'] = '09:00';
            $validated['end_time'] = '18:00';
        }

        if ($leaveRequest->status === 'rejected') {
            $leaveRequest->approvals()->delete();

            $admins = User::where('role', 'admin')->get();
            if ($admins->isEmpty()) {
                return redirect()->route('leave-requests.index')->with('error', 'No admins available to approve your request.');
            }

            $leaveRequest->update(array_merge($validated, [
                'resubmission_count' => $leaveRequest->resubmission_count + 1
            ]));

            foreach ($admins as $admin) {
                Approval::create([
                    'leave_request_id' => $leaveRequest->id,
                    'admin_id' => $admin->id,
                    'status' => 'pending',
                ]);
            }
            return redirect()->route('leave-requests.index')->with('success', 'Leave request resubmitted successfully.');
        } else {
            $leaveRequest->update($validated);
            return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
        }
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully.');
    }
}

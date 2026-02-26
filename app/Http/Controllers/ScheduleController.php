<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index(Request $request)
    {
        $schedules = Schedule::where('user_id', $request->user()->id)
            ->orderBy('start_time', 'asc')
            ->get();

        return Inertia::render('Schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time', 
            'status' => 'required|string|in:pending,completed,cancelled,rescheduled',
        ]);

        $request->user()->schedules()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        return redirect()->route('schedules')->with('success', 'Schedule created successfully!');
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Ensure the user owns this schedule
        if ($request->user()->id !== $schedule->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:pending,completed,cancelled,rescheduled',
        ]);

        // Validate that if date/time changed, the status is set to rescheduled
        $startChanged = Carbon::parse($schedule->start_time)->ne(Carbon::parse($validated['start_time']));
        $endChanged = Carbon::parse($schedule->end_time)->ne(Carbon::parse($validated['end_time']));

        if (($startChanged || $endChanged) && $validated['status'] !== 'rescheduled') {
            throw ValidationException::withMessages([
                'status' => 'Because the date or time was changed, the status must be set to "Rescheduled".'
            ]);
        }

        $schedule->update($validated);

        return redirect()->route('schedules')->with('success', 'Schedule updated successfully!');
    }

    /**
     * //Remove the specified schedule from storage.
     */
    public function destroy(Request $request, Schedule $schedule)
    {
        if ($request->user()->id !== $schedule->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->delete();

        return redirect()->route('schedules')->with('success', 'Schedule deleted successfully!');
    }
}
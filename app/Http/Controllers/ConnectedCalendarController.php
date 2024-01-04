<?php 
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConnectedCalendar;
use Illuminate\Http\Request;

class ConnectedCalendarController extends Controller
{
    public function index()
    {
        $connectedCalendars = ConnectedCalendar::all();
        return response()->json($connectedCalendars);
    }

    public function show($id)
    {
        $connectedCalendar = ConnectedCalendar::findOrFail($id);
        return response()->json($connectedCalendar);
    }

    public function store(Request $request)
    {
        $connectedCalendar = ConnectedCalendar::create($request->all());
        return response()->json($connectedCalendar, 201);
    }

    public function update(Request $request, $id)
    {
        $connectedCalendar = ConnectedCalendar::findOrFail($id);
        $connectedCalendar->update($request->all());
        return response()->json($connectedCalendar, 200);
    }

    public function destroy($id)
    {
        $connectedCalendar = ConnectedCalendar::findOrFail($id);
        $connectedCalendar->delete();
        return response()->json(null, 204);
    }


    public function get_connected_calendars(Request $request){
        $user_id = $request->user()->id;
        $connectedCalendarsList = [];
        try {
            $connectedCalendars = ConnectedCalendar::with('userCalendars')->where('user_id', $user_id)->get();
            if ($connectedCalendars) {
                foreach ($connectedCalendars as $connectedCalendar) {
                    $serializedValue = $connectedCalendar->userCalendars->pluck('check_for_conflicts')[0];
                    $checkForConflicts = @unserialize($serializedValue);
                    $connectedCalendarsList[] = [
                        'id' => $connectedCalendar->id,
                        'slug' => $connectedCalendar->slug,
                        'is_primary' => $connectedCalendar->userCalendars->pluck('is_primary')[0],
                        'check_for_conflicts_in' => $checkForConflicts,
                        'primary_sub_calendar' => $connectedCalendar->userCalendars->pluck('primary_sub_calendar')[0]
                    ];
                }
            }
            return response()->json(['data' => $connectedCalendarsList, 'success' => true, 'message' => "Connected Calendars Found"]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th, 'success' => false], 400);
        }

    }
}

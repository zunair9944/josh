<?php
namespace App\Http\Controllers;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\ConnectedCalendar;
use App\Models\UserCalendar;


class OutlookCalendarController extends Controller
{
    public function auth(Request $request, $token)
    {
        $t = PersonalAccessToken::findToken($token);
        $user = $t->tokenable;
        return Socialite::driver('microsoft')->with(['state' => 'user_id=' . $user->id ])->scopes(['offline_access','Calendars.Read', 'Calendars.ReadWrite'])->redirect();
    }

    public function callback(Request $request)
    {
        $state = $request->input('state');
        $userAuth = Socialite::driver('microsoft')->stateless()->user();
        parse_str($state, $result);

        $user = User::findOrFail($result['user_id']);
        $connectedCalendar = ConnectedCalendar::updateOrCreate(
            ['slug' => 'outlook-calendar'],
            ['user_id' => $user->id, 'access_token' => $userAuth->token, 'refresh_token' => $userAuth->refreshToken]
        );
        $connectedCalendars = ConnectedCalendar::all();
        $is_primary = (sizeof($connectedCalendars) < 2) ? true : false;
        $calendarSettings = UserCalendar::updateOrCreate(
            ['calendar_id' => $connectedCalendar->id],
            ['user_id' => $user->id, 'is_primary' => $is_primary,'primary_sub_calendar' => 0, 'check_for_conflicts' => serialize([])]
        );

        if($connectedCalendar){
            echo '<script>window.location.href = "http://localhost:3000/settings" </script>';
            return 'All Done! Your calendar has been connected. You may proceed to the app now.';
        }
    }

    public function list_calendars(Request $request){
        
        $user_id = $request->user()->id;
        $access_token = ConnectedCalendar::where('user_id', $user_id)->where('slug','outlook-calendar')->firstOrFail()->access_token;
        $graph = new Graph();
        $graph->setAccessToken($access_token);
        $calendarsList = [];
        try {
            $calendars = $graph->createRequest('GET', '/me/calendars')
                ->setReturnType(Model\Calendar::class)
                ->execute();
            if($calendars){
                foreach($calendars as $calendar){
                    $calendarsList[] = $calendar;
                }
            }
            return response()->json($calendarsList,200);
        } catch (GraphException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'An Error has occured'],500);
        }
    }


    public function setAsPrimary()
    {
        $this->update(['is_primary' => true]);
        $this->where('id', '!=', $this->id)->update(['is_primary' => false]);
    }


    public function add_conflicts_check(Request $request)
    {
        try{
            $sub_calendar_id = $request->sub_calendar_id;
        $calendar_id = $request->calendar_id;
        
        $userCalendar = UserCalendar::where('calendar_id', $calendar_id)->firstOrFail();
        $checkForConflicts = $userCalendar->check_for_conflicts ? unserialize($userCalendar->check_for_conflicts) : [];

        $checkForConflicts[] = $sub_calendar_id;

        $userCalendar->check_for_conflicts = serialize($checkForConflicts);
        $userCalendar->save();
        
        return response()->json(['message' => 'Calendar ID added successfully.'], 200);
        }
        catch(Exception $e){
            return response()->json(['error' => $e,'success' => false],500);
        }
    }
    
    
    public function remove_conflicts_check(Request $request)
    {
        $sub_calendar_id = $request->sub_calendar_id;
        $calendar_id = $request->calendar_id;

        $userCalendar = UserCalendar::where('calendar_id',$calendar_id)->firstOrFail();
        $checkForConflicts = unserialize($userCalendar->check_for_conflicts);
        $index = array_search($sub_calendar_id, $checkForConflicts);

        if ($index !== false) {
            array_splice($checkForConflicts, $index, 1);
            $userCalendar->check_for_conflicts = serialize($checkForConflicts);
            $userCalendar->save();

            return response()->json(['message' => 'Calendar id removed successfully.'],200);
        }
        return response()->json(['message' => 'No Record Found for that calendar id'],200);
    }


    public function set_primary_sub_calendar(Request $request)
    {
        $request->validate([
            'calendar_id' => 'required',
            'primary_sub_calendar' => 'required'
        ]);

        $updatePrimarySubcalendar = UserCalendar::updateOrCreate(
            ['calendar_id' => $request->calendar_id],
            ['primary_sub_calendar' => $request->primary_sub_calendar]
        );
        if($updatePrimarySubcalendar){
            return response()->json(['message' => 'Primary Sub Calendar updated successfully.'],200);
        }

        return response()->json(['message' => 'Could not update the primary key.'],400);
    }
}

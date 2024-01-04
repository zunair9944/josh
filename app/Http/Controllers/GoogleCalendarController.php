<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;
use App\Models\ConnectedCalendar;
use App\Models\UserCalendar;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{


    public function auth(Request $request, $token)
    {        
        $t = PersonalAccessToken::findToken($token);
        $user = $t->tokenable;

        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CALENDAR_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CALENDAR_CLIENT_SECRET'));
        $client->addScope(\Google_Service_Calendar::CALENDAR);
        $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setPrompt("consent");

        $params = [
            'user_id' => $user->id
        ];
        $client->setState(base64_encode(json_encode($params)));
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {

        $state = $request->query('state'); 
        $params = json_decode(base64_decode($state), true);
        $user_id = $params['user_id'];


        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CALENDAR_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CALENDAR_CLIENT_SECRET'));
        $client->addScope(\Google_Service_Calendar::CALENDAR);
        $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            $refresh_token = $token['refresh_token'];
            $connectedCalendar = ConnectedCalendar::updateOrCreate(
                ['slug' => 'google-calendar'],
                ['user_id' => $user_id, 'access_token' => $token['access_token'], 'refresh_token' => $token['refresh_token']]
            );

            $connectedCalendars = ConnectedCalendar::where('user_id', $user_id)->get();
            $is_primary = (sizeof($connectedCalendars) < 2) ? true : false;
            $calendarSettings = UserCalendar::updateOrCreate(
                ['calendar_id' => $connectedCalendar->id],
                ['user_id' => $user_id, 'is_primary' => $is_primary,'primary_sub_calendar' => 0,'check_for_conflicts' => serialize([])]
            );

            $user = User::find($user_id);
            $already_connected_calendars = $user->selectedCalendars;
            $connected_calendar = array_push($already_connected_calendars,$connected_calendar->id);
            $user->selectedCalendars = $connected_calendar;
            $user->save();

            if($connectedCalendar){
                echo '<script>window.location.href = "http://localhost:3000/settings" </script>';
                return 'All Done! Your calendar has been connected. You may proceed to the app now.';
            }
        } else {
            return 'failed';
        }
    }

    /** 
     * Helper Function - Prepare Client 
     * @return $client - GoogleClient 
     * @param $user_id
     */
    public function prepare_calendar_client($user_id)
    {
        $record = ConnectedCalendar::where('user_id',$user_id)->firstOrFail();

        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CALENDAR_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CALENDAR_CLIENT_SECRET'));
        $access_token['access_token'] = $record->access_token;
        $access_token['refresh_token'] = $record->refresh_token;
        $client->setAccessToken($access_token);

        if($client->isAccessTokenExpired()){
            $client->fetchAccessTokenWithRefreshToken();
            $newAccessToken = $client->getAccessToken();
            $connectedCalendar = ConnectedCalendar::updateOrCreate(
                ['user_id' => $user_id],
                ['slug' => 'google-calendar', 'access_token' => $newAccessToken['access_token']]
            );
        }
        $calendarService = new \Google_Service_Calendar($client);
        return $calendarService;
    }

    /** 
     * Get All Calendars created. 
     * @param Request 
     * @return JSON 
     * @author devsyed
     */
    public function list_calendars(Request $request)
    {
        $client = $this->prepare_calendar_client($request->user()->id);
        return response()->json($client->calendarList->listCalendarList());
    }

    /** 
     * Get All Events by Calendar Id
     * @param Request,Int ( calendarId )
     * @return JSON
     */
    public function get_all_events(Request $request, $calendarId)
    {
        $client = $this->prepare_calendar_client($request);
        $calendarId = $calendarId;
        $events = $client->events->listEvents($calendarId);
        return response()->json($events,200);
    }


    /** 
     * Get Booked Slots 
     * @param Request 
     * @return JSON | slots as { title, startTime, endTime }
     */
    public function get_booked_slots($user_id)
    {
        $client = $this->prepare_calendar_client($user_id);
        $calendarIdMain = ConnectedCalendar::where('user_id', $user_id)->first()->id;
        $serializedIds = UserCalendar::where('user_id', $user_id)->where('calendar_id', $calendarIdMain)->firstOrFail()->check_for_conflicts;
        $unserializedIds = unserialize($serializedIds);
        $slots = [];

        foreach($unserializedIds as $calendarId){
            $calendarEvents = $client->events->listEvents($calendarId);
            $allEvents = $calendarEvents->getItems();
            foreach($allEvents as $event){
                $slots[] = [
                    'start' => $event->getStart()->getDateTime(),
                    'end' => $event->getEnd()->getDateTime(),
                ];
            }
        }
        return response()->json($slots,200);
    }

    /** 
     * Create Event 
     * @param  Request $request
     * @return status | id
     */
    public function create_event($user_id,$event_details)
    {
        
        $client = $this->prepare_calendar_client($user_id);
        if(!$client) return response()->json(['message' => 'Calendar Not Connected.']);
        $event = new \Google_Service_Calendar_Event([
            'summary' => $event_details['event_name'],
            'start' => [
                'dateTime' => Carbon::parse($event_details['start'])->setTimezone('Asia/Karachi'),
                'timeZone' => 'Asia/Karachi',
            ],
            'end' => [
                'dateTime' =>  Carbon::parse($event_details['end'])->setTimezone('Asia/Karachi'),
                'timeZone' => 'Asia/Karachi',
            ]
        ]);
        $calendarId = 'primary';
        $createdEvent = $client->events->insert($calendarId, $event);
        return $createdEvent;
    }


    public function reschedule_event(Request $request)
    {
        
    }


    public function delete_event(Request $request)
    {
        
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

}

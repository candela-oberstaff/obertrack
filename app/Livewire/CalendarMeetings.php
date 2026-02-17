<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;

class CalendarMeetings extends Component
{
    public $meetings = [];
    public $activeMeetingId = null;
    public $warningMeetingId = null;
    public $errorState = null; // 'token_expired', 'access_denied', 'api_error', 'rate_limit', 'quota_exceeded', 'unknown'
    public $retryAfter = null; // seconds until retry is available
    public $notificationsEnabled = true;
    public $dismissedAlarms = [];

    public function mount(GoogleCalendarService $service)
    {
        $this->notificationsEnabled = (bool) Auth::user()->google_calendar_notifications;
        $this->updateMeetings($service);
    }

    public function poll(GoogleCalendarService $service)
    {
        $this->updateMeetings($service);
        $this->checkAlarms();
    }

    protected function updateMeetings(GoogleCalendarService $service)
    {
        if (Auth::user()->google_calendar_token) {
            $result = $service->getTodayMeetings(Auth::user());
            
            // Check if result contains an error
            if (is_array($result) && isset($result['error'])) {
                $this->errorState = $result['error'];
                $this->retryAfter = $result['retry_after'] ?? null;
                $this->meetings = [];
            } else {
                $this->errorState = null;
                $this->retryAfter = null;
                $this->meetings = $result;
            }
        }
    }

    protected function checkAlarms()
    {
        if (!$this->notificationsEnabled) {
            $this->activeMeetingId = null;
            $this->warningMeetingId = null;
            return;
        }

        $timezone = Auth::user()->timezone ?? config('app.timezone', 'America/Argentina/Buenos_Aires');
        $now = now()->timezone($timezone);
        $this->activeMeetingId = null;
        $this->warningMeetingId = null;

        foreach ($this->meetings as $index => $meeting) {
            $id = $meeting['id'];
            $start = \Carbon\Carbon::parse($meeting['start'])->timezone($timezone);
            $end = \Carbon\Carbon::parse($meeting['end'])->timezone($timezone);

            // Set is_active if now is between start and end
            $this->meetings[$index]['is_active'] = $now->between($start, $end);

            // Skip alarm logic if already dismissed
            if (in_array($id, $this->dismissedAlarms)) {
                continue;
            }
            
            // Alarm trigger: Exactly 1 minute before start, stop at start
            $secondsUntilStart = $now->diffInSeconds($start, false); // false = signed diff
            
            // Alarm sounds only during the 60 seconds BEFORE the meeting
            if ($secondsUntilStart >= 0 && $secondsUntilStart <= 60) {
                $this->activeMeetingId = $meeting['id'];
                // We don't break here because we need to calculate is_active for all meetings
            }

            // Warning 10 minutes before
            if ($secondsUntilStart > 60 && $secondsUntilStart <= 600) {
                $this->warningMeetingId = $meeting['id'];
            }
        }
    }

    public function toggleNotifications()
    {
        $this->notificationsEnabled = !$this->notificationsEnabled;
        
        // Ensure we pass a strict boolean to avoid PDO type mismatch in some DBs like PostgreSQL
        Auth::user()->update([
            'google_calendar_notifications' => (bool) $this->notificationsEnabled
        ]);
        
        $this->checkAlarms();
    }

    public function joinMeeting($id, $link)
    {
        $this->activeMeetingId = null;
        
        // Mark this meeting as dismissed so the alarm doesn't sound again 
        // until the next poll cycle or if it re-enters the window
        if (!in_array($id, $this->dismissedAlarms)) {
            $this->dismissedAlarms[] = $id;
        }
        
        $this->dispatch('meeting-joined', link: $link);
    }
    
    public function fetchMeetings()
    {
        // Clear cache and force refresh
        $service = app(GoogleCalendarService::class);
        $service->clearCache(Auth::user());
        $this->updateMeetings($service);
    }

    public function render()
    {
        return view('livewire.calendar-meetings');
    }
}

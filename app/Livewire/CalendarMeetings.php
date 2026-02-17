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

        foreach ($this->meetings as $meeting) {
            $start = \Carbon\Carbon::parse($meeting['start'])->timezone($timezone);
            
            // Alarm trigger: Exactly 1 minute before start
            $secondsUntilStart = $now->diffInSeconds($start, false); // false = signed diff
            
            // Alarm sounds between 60 seconds before and 5 minutes after (to allow manually stopping)
            if ($secondsUntilStart >= -300 && $secondsUntilStart <= 60) {
                $this->activeMeetingId = $meeting['id'];
                break;
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
        Auth::user()->update([
            'google_calendar_notifications' => $this->notificationsEnabled
        ]);
        
        $this->checkAlarms();
    }

    public function joinMeeting($id, $link)
    {
        $this->activeMeetingId = null;
        
        // This will be handled by JS to open the link if needed, 
        // though the <a> already has target="_blank"
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

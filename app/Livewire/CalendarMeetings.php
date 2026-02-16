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

    public function mount(GoogleCalendarService $service)
    {
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
            $this->meetings = $service->getTodayMeetings(Auth::user());
        }
    }

    protected function checkAlarms()
    {
        $now = now();
        $this->activeMeetingId = null;
        $this->warningMeetingId = null;

        foreach ($this->meetings as $meeting) {
            $start = \Carbon\Carbon::parse($meeting['start']);
            
            // Active alarm: meeting started within the last 2 minutes
            $minutesSinceStart = $now->diffInMinutes($start, false); // false = signed difference
            if ($minutesSinceStart >= 0 && $minutesSinceStart < 2) {
                $this->activeMeetingId = $meeting['id'];
                break;
            }

            // Warning 10 minutes before
            if ($now->lessThan($start) && $now->diffInMinutes($start) <= 10) {
                $this->warningMeetingId = $meeting['id'];
            }
        }
    }

    public function joinMeeting($id, $link)
    {
        $this->activeMeetingId = null;
        
        // This will be handled by JS to open the link if needed, 
        // though the <a> already has target="_blank"
        $this->dispatch('meeting-joined', link: $link);
    }

    public function render()
    {
        return view('livewire.calendar-meetings');
    }
}

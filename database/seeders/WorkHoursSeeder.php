<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkHours;
use App\Models\User;
use Carbon\Carbon;

class WorkHoursSeeder extends Seeder
{
    public function run()
    {
        $user = User::first(); // Asegúrate de tener un usuario en la base de datos

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                WorkHours::create([
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'hours_worked' => rand(6, 8),
                    'approved' => true,
                    'approval_comment' => 'Aprobado automáticamente',
                ]);
            }
        }
    }
}
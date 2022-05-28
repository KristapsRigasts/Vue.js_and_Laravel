<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    public function index()
    {
        $data = User::all()->toArray();

        return view('welcome', compact('data'));
    }

    public function store(Request $request)
    {
        $employeeId = $request['employeeId'];
        $workingDate = $request['date'];
        $workingMonth = $request['month'];
        $workingYear = $request['year'];
        $workedHours = $request['hours'];

        $recordForWorkingDay = WorkingHour::where('user_id', $employeeId)
            ->where('date', $workingDate)
            ->where('month', $workingMonth)
            ->where('year', $workingYear)
            ->first();

        if ($recordForWorkingDay != null) {
            WorkingHour::where('id', $recordForWorkingDay->id)
                ->update(['hours' => $workedHours]);

        } else {
            WorkingHour::create([
                'user_id' => $employeeId,
                'date' => $workingDate,
                'month' => $workingMonth,
                'year' => $workingYear,
                'hours' => $workedHours
            ]);
        }
        return response()->json(['status' => 'success']);
    }
}

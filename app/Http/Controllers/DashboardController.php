<?php

namespace App\Http\Controllers;

use App\Services\DashboardDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function admin(DashboardDataService $data)
    {
        Gate::authorize('dashboard-admin');

        return view('dashboards.admin', $data->admin());
    }

    public function staff(DashboardDataService $data)
    {
        Gate::authorize('dashboard-staff');

        return view('dashboards.staff', $data->staff());
    }

    public function instructor(Request $request, DashboardDataService $data)
    {
        Gate::authorize('dashboard-instructor');

        return view('dashboards.instructor', $data->instructor($request->user()));
    }

    public function student(Request $request, DashboardDataService $data)
    {
        Gate::authorize('dashboard-student');

        return view('dashboards.student', $data->student($request->user()));
    }
}

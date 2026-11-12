<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManagedAccountRequest;
use App\Models\User;
use App\Services\ManagedAccountService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    private function ensureStaff(User $user): void
    {
        abort_unless($user->roles()->where('name', 'staff')->exists() && ! $user->roles()->where('name', 'admin')->exists(), 403);
    }

    public function index(Request $r)
    {
        return view('admin.staff.index', ['staff' => User::with('roles')->whereHas('roles', fn ($q) => $q->where('name', 'staff'))->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))->when($r->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$r->string('q').'%')->orWhere('email', 'like', '%'.$r->string('q').'%')))->paginate(15)->withQueryString()]);
    }

    public function create(Request $r)
    {
        $r->validate(['existing_email' => ['nullable', 'email', 'max:255']]);

        return view('admin.staff.form', ['staffMember' => new User, 'existingUser' => $r->filled('existing_email') ? User::where('email', $r->input('existing_email'))->first() : null]);
    }

    public function store(CreateManagedAccountRequest $r, ManagedAccountService $s)
    {
        $u = $s->create($r->validated(), 'staff');

        return to_route('admin.staff.edit', $u)->setStatusCode(303)->with('status', 'تم تجهيز حساب الموظف.');
    }

    public function edit(User $staffMember)
    {
        $this->ensureStaff($staffMember);

        return view('admin.staff.form', compact('staffMember') + ['existingUser' => null]);
    }

    public function update(Request $r, User $staffMember)
    {
        $this->ensureStaff($staffMember);
        $staffMember->update($r->validate(['name' => ['required', 'string', 'max:150']]));

        return back()->setStatusCode(303)->with('status', 'تم تحديث الموظف.');
    }

    public function status(Request $r, User $staffMember)
    {
        $this->ensureStaff($staffMember);
        $r->validate(['is_active' => ['required', 'boolean']]);
        abort_if($staffMember->id === $r->user()->id, 403);
        $staffMember->forceFill(['is_active' => $r->boolean('is_active')])->save();

        return back()->setStatusCode(303)->with('status', 'تم تحديث حالة الموظف.');
    }
}

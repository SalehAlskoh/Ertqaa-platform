<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManagedAccountRequest;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\ManagedAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstructorController extends Controller
{
    public function index(Request $r)
    {
        return view('admin.instructors.index', ['instructors' => InstructorProfile::with('user')->withCount('trainingGroups')->when($r->filled('q'), fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$r->string('q').'%')->orWhere('email', 'like', '%'.$r->string('q').'%')))->paginate(15)->withQueryString()]);
    }

    public function create(Request $r)
    {
        $r->validate(['existing_email' => ['nullable', 'email', 'max:255']]);

        return view('admin.instructors.form', ['instructor' => (new InstructorProfile)->setRelation('user', new User), 'existingUser' => $r->filled('existing_email') ? User::where('email', $r->input('existing_email'))->first() : null]);
    }

    public function store(CreateManagedAccountRequest $r, ManagedAccountService $s)
    {
        $user = $s->create($r->validated(), 'instructor');

        return to_route('admin.instructors.show', $user->instructorProfile)->setStatusCode(303)->with('status', 'تم تجهيز حساب المحاضر.');
    }

    public function show(InstructorProfile $instructor)
    {
        return view('admin.instructors.show', ['instructor' => $instructor->load('user'), 'groups' => $instructor->trainingGroups()->with(['course', 'instructor.user'])->latest()->paginate(15)]);
    }

    public function edit(InstructorProfile $instructor)
    {
        return view('admin.instructors.form', ['instructor' => $instructor->load('user'), 'existingUser' => null]);
    }

    public function update(Request $r, InstructorProfile $instructor)
    {
        $d = $r->validate(['name' => ['required', 'string', 'max:150'], 'professional_title' => ['nullable', 'string', 'max:150'], 'biography' => ['nullable', 'string', 'max:65000'], 'experience_years' => ['nullable', 'integer', 'min:0', 'max:65535']]);
        DB::transaction(function () use ($instructor, $d) {
            $instructor->user()->firstOrFail()->update(['name' => $d['name']]);
            unset($d['name']);
            $instructor->update($d);
        });

        return to_route('admin.instructors.show', $instructor)->setStatusCode(303)->with('status', 'تم تحديث الملف المهني.');
    }

    public function status(Request $r, InstructorProfile $instructor)
    {
        $r->validate(['is_active' => ['required', 'boolean']]);
        $user = $instructor->user;
        if (! $r->boolean('is_active') && ($user->id === $r->user()->id || $user->roles()->where('name', 'admin')->exists())) {
            throw ValidationException::withMessages(['is_active' => 'لا يمكن تعطيل حساب مدير أو حسابك الحالي من هذه الصفحة.']);
        }$user->forceFill(['is_active' => $r->boolean('is_active')])->save();

        return back()->setStatusCode(303)->with('status', 'تم تحديث حالة الحساب.');
    }
}

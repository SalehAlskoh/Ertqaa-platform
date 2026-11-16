<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command?->warn('تُخطي AdminSeeder: إنشاء المدير بهذه الطريقة مسموح في البيئة المحلية فقط.');

            return;
        }

        if (! Role::where('name', 'admin')->exists()) {
            $this->call(RoleSeeder::class);
        }

        $name = trim((string) config('admin_seed.name'));
        $email = mb_strtolower(trim((string) config('admin_seed.email')));
        $password = (string) config('admin_seed.password');

        if ($name === '' || $email === '' || trim($password) === '') {
            $this->command?->warn('تُخطي إنشاء المدير: أكمل SEED_ADMIN_NAME وSEED_ADMIN_EMAIL وSEED_ADMIN_PASSWORD في .env المحلي. لم تتغير الحسابات.');

            return;
        }

        $created = DB::transaction(function () use ($name, $email, $password): bool {
            // Serialize concurrent seeders; the unique email constraint remains authoritative.
            $role = Role::where('name', 'admin')->lockForUpdate()->firstOrFail();
            $existing = User::where('email', $email)->lockForUpdate()->first();

            if ($existing) {
                // Check the actual role, including disabled accounts; never reactivate or upgrade.
                if (! $existing->roles()->whereKey($role->id)->exists()) {
                    throw new RuntimeException('تعارض AdminSeeder: البريد مستخدم لحساب لا يملك دور admin. لم تتغير أدواره أو كلمة مروره.');
                }

                return false;
            }

            $validator = Validator::make(compact('name', 'email', 'password'), [
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', Password::min(12)->mixedCase()->numbers()->symbols()],
            ]);
            if ($validator->fails()) {
                throw new RuntimeException('بيانات AdminSeeder غير صالحة. تحقق من الاسم والبريد وكلمة مرور من 12 حرفًا على الأقل بأحرف كبيرة وصغيرة وأرقام ورموز.');
            }

            $user = new User;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->is_active = true;
            $user->save();
            $user->roles()->attach($role->id);

            return true;
        }, 3);

        $this->command?->info($created
            ? 'أُنشئ المدير المحلي وربط بدور admin بحساب نشط.'
            : 'المدير موجود ويملك دور admin؛ لم تتغير بياناته أو كلمة مروره أو حالة نشاطه.');
    }
}

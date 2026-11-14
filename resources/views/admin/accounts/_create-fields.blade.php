@php($accountMode = old('account_mode', ($existingUser ?? null) ? 'existing' : 'new'))
<div>
    <div class="form-grid">
        <x-select name="account_mode" label="طريقة إضافة الحساب" required data-account-mode>
            <option value="new" @selected($accountMode === 'new')>إنشاء حساب جديد</option>
            <option value="existing" @selected($accountMode === 'existing')>إضافة الدور إلى حساب موجود</option>
        </x-select>
        <x-input name="email" label="البريد الإلكتروني" type="email" :value="$existingUser?->email" dir="ltr" maxlength="255" autocomplete="off" required help="استخدم البريد الدقيق للشخص. لا تُنشأ حسابات مكررة للبريد نفسه." />
    </div>

    <div class="account-mode-section" data-account-pane="new" @if($accountMode !== 'new') hidden @endif>
        <h3><x-icon name="lock" :size="18" />بيانات الحساب الجديد</h3>
        <div class="form-grid">
            <div class="md:col-span-2"><x-input name="name" label="الاسم الكامل" maxlength="150" autocomplete="off" required :disabled="$accountMode !== 'new'" /></div>
            <x-input name="password" label="كلمة المرور" type="password" autocomplete="new-password" minlength="12" required :disabled="$accountMode !== 'new'" help="12 حرفًا على الأقل. تُحدَّد للحساب الجديد فقط." />
            <x-input name="password_confirmation" label="تأكيد كلمة المرور" type="password" autocomplete="new-password" minlength="12" required :disabled="$accountMode !== 'new'" />
        </div>
    </div>

    <div class="account-mode-section" data-account-pane="existing" @if($accountMode !== 'existing') hidden @endif>
        <h3><x-icon name="user" :size="18" />إضافة الدور إلى الحساب الموجود</h3>
        @if($existingUser)
            <div class="account-verified">
                <span class="account-avatar" aria-hidden="true">{{ mb_substr($existingUser->name, 0, 1) }}</span>
                <div><p class="account-verified-title">الحساب الذي عُثر عليه</p><p class="account-verified-name">{{ $existingUser->name }}</p><p class="account-verified-email"><bdi>{{ $existingUser->email }}</bdi></p>
                    @if(!$existingUser->is_active)<p class="field-help mt-2">هذا الحساب معطّل حاليًا. إضافة الدور لا تُفعّل الحساب تلقائيًا.</p>@endif
                </div>
            </div>
        @endif
        <p class="field-help">تأكّد من هوية صاحب البريد قبل المتابعة. ستُضاف صلاحية {{ $roleLabel }} فقط، دون استبدال الاسم أو كلمة المرور أو الأدوار الحالية.</p>
        <label class="existing-account-confirm"><input type="checkbox" name="confirm_existing" value="1" @checked(old('confirm_existing')) @disabled($accountMode !== 'existing') required @if($errors->has('confirm_existing')) aria-invalid="true" aria-describedby="confirm-existing-error" @endif><span>أؤكد مراجعة هوية الحساب وإضافة دور {{ $roleLabel }} إليه.</span></label>
        @error('confirm_existing')<p class="field-error mt-2" id="confirm-existing-error">{{ $message }}</p>@enderror
    </div>
</div>

<section class="account-lookup" aria-label="البحث عن حساب موجود">
    <h2><x-icon name="search" :size="18" />هل لدى الشخص حساب في ارتقاء؟</h2>
    <p class="field-help mb-4">ابحث بالبريد الإلكتروني لمراجعة هوية الحساب قبل إضافة دور {{ $roleLabel }} إليه. لن تتغير كلمة مروره أو أدواره الحالية.</p>
    <form action="{{ $lookupRoute }}" method="get">
        <div class="field">
            <label class="field-label" for="existing_email">البريد الإلكتروني للحساب الموجود</label>
            <input class="input" id="existing_email" name="existing_email" type="email" value="{{ request('existing_email') }}" dir="ltr" maxlength="255" autocomplete="off" required @if($errors->has('existing_email')) aria-invalid="true" aria-describedby="existing-email-error" @endif>
            @error('existing_email')<p class="field-error" id="existing-email-error">{{ $message }}</p>@enderror
        </div>
        <button class="button button-secondary" type="submit"><x-icon name="search" :size="17" />البحث عن الحساب</button>
    </form>
    @if(request()->filled('existing_email') && !($existingUser ?? null))
        <p class="field-error mt-3" role="status">لم يُعثر على حساب بهذا البريد. راجع العنوان أو أنشئ حسابًا جديدًا.</p>
    @endif
</section>

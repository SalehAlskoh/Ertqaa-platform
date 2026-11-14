<?php
return [
    'required'=>'حقل :attribute مطلوب.', 'accepted'=>'يجب تأكيد :attribute.', 'prohibited'=>'لا يسمح بإرسال :attribute.',
    'email'=>'أدخل بريدًا إلكترونيًا صحيحًا.', 'unique'=>'قيمة :attribute مستخدمة من قبل.', 'exists'=>'القيمة المختارة لحقل :attribute غير متاحة.',
    'confirmed'=>'تأكيد :attribute غير متطابق.', 'current_password'=>'كلمة المرور الحالية غير صحيحة.',
    'string'=>'يجب أن يكون :attribute نصًا.', 'integer'=>'يجب أن يكون :attribute عددًا صحيحًا.', 'numeric'=>'يجب أن يكون :attribute رقمًا.',
    'boolean'=>'قيمة :attribute غير صحيحة.', 'in'=>'القيمة المختارة لحقل :attribute غير مسموحة.', 'regex'=>'صيغة :attribute غير صحيحة.',
    'date'=>'تاريخ :attribute غير صحيح.', 'date_format'=>'صيغة التاريخ في :attribute غير صحيحة.', 'after'=>'يجب أن يأتي :attribute بعد :date.',
    'before_or_equal'=>'يجب ألا يتجاوز :attribute تاريخ :date.', 'decimal'=>'العدد في :attribute يحتوي عددًا غير مسموح من المنازل العشرية.',
    'different'=>'يجب أن تختلف قيمة :attribute عن :other.', 'required_with'=>'حقل :attribute مطلوب مع :values.',
    'array'=>'يجب أن يكون :attribute قائمة اختيارات.', 'distinct'=>'لا تكرر الاختيار في :attribute.',
    'between'=>['numeric'=>'يجب أن تكون قيمة :attribute بين :min و:max.'],
    'min'=>['string'=>'يجب ألا يقل :attribute عن :min أحرف.','numeric'=>'يجب ألا يقل :attribute عن :min.','array'=>'اختر :min على الأقل في :attribute.'],
    'max'=>['string'=>'يجب ألا يتجاوز :attribute :max حرفًا.','numeric'=>'يجب ألا يتجاوز :attribute :max.','array'=>'لا تتجاوز :max اختيارات في :attribute.'],
    'attributes'=>['training_group_id'=>'المجموعة','student_user_id'=>'الطالب','confirmed_by_user_id'=>'الموظف المؤكد','reviewed_by_user_id'=>'المراجع','paid_amount'=>'المبلغ المستلم','paid_at'=>'وقت التحصيل','enrolled_at'=>'وقت التسجيل النهائي','offer_expires_at'=>'مهلة الحجز','rejection_reason'=>'سبب الرفض','change_reason'=>'سبب التغيير','starts_at'=>'بداية المحاضرة','ends_at'=>'نهاية المحاضرة','lecture_number'=>'رقم المحاضرة','first_date'=>'تاريخ أول محاضرة','weekdays'=>'أيام التدريب','weekdays.*'=>'يوم التدريب','start_time'=>'ساعة البداية','end_time'=>'ساعة النهاية','count'=>'عدد المحاضرات','group'=>'المجموعة','q'=>'البحث','name'=>'الاسم','email'=>'البريد الإلكتروني','password'=>'كلمة المرور','password_confirmation'=>'تأكيد كلمة المرور','phone_number'=>'رقم الهاتف','category_id'=>'التصنيف','title'=>'العنوان','slug'=>'الرابط المختصر','planned_hours'=>'الساعات المخططة','code'=>'رمز المجموعة','capacity'=>'السعة','fee_amount'=>'الرسوم','currency_code'=>'رمز العملة','registration_opens_at'=>'بداية التسجيل','registration_closes_at'=>'نهاية التسجيل','instructor_user_id'=>'المحاضر','status'=>'الحالة','confirm_existing'=>'هوية صاحب الحساب','account_mode'=>'طريقة إضافة الحساب'],
];

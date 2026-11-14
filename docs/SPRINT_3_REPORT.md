# تقرير تسليم Sprint 3 — ارتقاء

تاريخ البداية: 2026-11-13. إغلاق الاستئناف: 2026-11-14.

**الحالة: نطاق Sprint 3 مكتمل ومتحقق منه بالفحوص المذكورة أدناه.** لا اختبارات PHPUnit/Feature Tests، ولا Commit أو Push، ولا حذف لبيانات المستخدمين أو المعاينة القائمة.

## 1. ما كان مكتملًا وما استُكمل

عند الاستئناف الأخير كانت Migration والخدمات والسياسات والمسارات وواجهات Sprint 3 الثلاث عشرة منفذة بالفعل. كما كان البناء وتجميع Blade والتقييم البصري و81 حالة عرض قد نجحوا؛ ملف التقدم لم يكن يعكس هذه المرحلة بعد. جرى التحقق من الكود والقاعدة والنتائج المحفوظة بدل إعادة تنفيذ السيناريوهات أو الاعتماد على الملف وحده.

كان آخر إجراء في المتصفح انسحاب طالب الفحص. أثبتت قاعدة الفحص أنه التزم قبل التوقف: التسجيل9 منسحب، والمبلغ30.00 وهوية الموظف2 ووقت التحصيل محفوظة، والمقاعد المشغولة/المحجوزة للمجموعة16 تساوي صفرًا. أكمل الاستئناف فحص HTTP: السجل يظهر للطالب، زر الانسحاب اختفى، محاضرة المجموعة تعيد403، وقائمة محاضراتها خالية لديه. كذلك ثبت حفظ تعليم الإشعار كمقروء.

أُعيد تشغيل MariaDB المستقلة الموجودة وLaravel بعد توقف عملياتهما مع الانقطاع، دون تثبيت أو تهيئة أو إيقاف خدمة أخرى. أُكمل توثيق التقدم والتقرير ودليل التشغيل وصور التسليم. لم يحتج الاستئناف لإعادة بناء وظيفة أو توليد شاشة أو إضافة Migration أخرى.

## 2. قاعدة البيانات والعقد

قاعدة المشروع `ertqaa_platform` على MariaDB11.4.13 والمنفذ3308. نجحت Migration الإضافية:

`2026_11_13_000001_add_confirmation_details_to_enrollments.php` — **Ran، batch3**.

| العمود الجديد | البنية والمعنى |
|---|---|
| confirmed_by_user_id | BIGINT UNSIGNED NULL، FK إلى users مع RESTRICT؛ يحدده الحساب المصادق عليه |
| paid_amount | DECIMAL(10,2) NULL، CHECK يمنع القيم السالبة؛ إلزامي في كل تسجيل جديد، وصفر للمجاني |
| paid_at | DATETIME NULL، من ساعة الخادم عند التحصيل؛ NULL للمجاني |

أُعيد استخدام enrolled_at للتسجيل النهائي، وUNIQUE(application_id) لمنع التسجيل المكرر. لم يُضف payments أو is_paid أو تاريخ تأكيد مكرر. Nullable يحمي التاريخ السابق؛ لا يعني قبول تسجيل جديد ناقص التوثيق. rollback الآلي لهذه Migration يرفض التنفيذ عمدًا لحماية التاريخ المالي.

تطابقت أعداد وبصمات صفوف **الجداول الخمسة عشر** في قاعدة المشروع مع لقطة بداية Sprint 3، ولم تُستعمل بياناتها لسيناريوهات الكتابة. الوثيقة الأصلية وMigration السابقة وAuth/User والاعتمادات مطابقة لبصماتها. بقيت بيانات XAMPP القديمة خارج نطاق أي تغيير.

لم تُعدّل TRAINING_CENTER_DATABASE_SCHEMA.md. تجاوز القرارات القديمة المتعلقة بتأكيد الطالب واستبعاد التحصيل موثق في [SPRINT_3_SCHEMA_ADDENDUM.md](SPRINT_3_SCHEMA_ADDENDUM.md)، وهو مع الأصل مرجع التنفيذ.

## 3. Models والبنية البرمجية

أُعيد استخدام Models وجداول المجال الموجودة. التعديلان المباشران:

- Enrollment: تحويل paid_amount إلى decimal:2 وpaid_at إلى datetime، وعلاقة confirmedBy إلى المستخدم. حقول التحصيل والموظف ليست ضمن fillable؛ تكتبها خدمة التأكيد صراحة.
- EnrollmentApplication: حالات العقد الست، وeffective_status وScope للتصفية، لإظهار العرض المنتهي فورًا حتى قبل تحديث حالته المخزنة.

| الطبقة | الملفات المنشأة/المعدلة في نطاق المرحلة |
|---|---|
| Services جديدة | EnrollmentService، LectureService، InternalNotificationService |
| Service محسنة | TrainingGroupService: السعة، تثبيت الرسوم والعملات، وقفل المحاضر ومنع إسناد جدول متعارض |
| Controllers جديدة | LectureController، ApplicationReviewController، StudentApplicationController، StudentEnrollmentController، GroupStudentsController، InternalNotificationController |
| Controller قائم | PublicCatalogController: أهلية زر التقديم والسعة والطلب السابق |
| Policies جديدة | EnrollmentApplicationPolicy، EnrollmentPolicy، LecturePolicy، NotificationRecipientPolicy |
| Policy قائمة | TrainingGroupPolicy: عرض طلاب المجموعة ضمن الصلاحية المناسبة |
| Form Requests جديدة | LectureRequest، GenerateLecturesRequest، ApplyToGroupRequest، ConfirmEnrollmentRequest، DecisionReasonRequest |
| أدوات التحقق | Money لمقارنة السنتات دون حساب مالي عائم؛ FitsTextColumn لحجم النص العربي وTEXT والإشعار |
| تكامل قائم | TrainingGroupRequest، AppServiceProvider، routes/web.php، routes/console.php، ورسائل التحقق العربية |
| Artisan | ExpireEnrollmentOffers بالأمر ertqaa:expire-offers |

Controllers تمرر المدخلات المتحقق منها إلى الخدمات. Policies وMiddleware تفرض الصلاحيات خادميًا، ولا تعتمد الحماية على إخفاء الزر. لا نظام أدوار جديد أو Repository Pattern أو Packages جديدة. Registered Routes أصبحت79، والمسارات القديمة محفوظة.

## 4. دورة التسجيل والتحصيل والانسحاب

1. الطالب النشط بدور student وStudentProfile يقدم من مجموعة منشورة وكورس منشور، داخل فترة التسجيل ومع توفر السعة. تؤخذ هويته من الجلسة، ولا تقبل هوية طالب آخر أو حالة يمررها Form. يبدأ الطلب pending ولا يحجز مقعدًا. UNIQUE الطالب/المجموعة يبقى قائمًا، ولا إعادة تقديم تلقائية بعد الرفض أو الإلغاء أو الانسحاب.
2. المدير/الموظف يوافق مبدئيًا على pending بعد قفل المجموعة وفحص السعة. يحفظ reviewer/time وawaiting_confirmation ومهلة48 ساعة من أول موافقة. إعادة الإرسال لا تمدد الموعد ولا تكرر الإشعار. الرفض يتطلب سببًا ويمكنه تحرير حجز صالح سابق؛ لا ينشئ Enrollment.
3. الحضور والتحصيل والتأكيد النهائي للإدارة فقط. يعرض النموذج الطالب والطلب والمجموعة والرسوم والعملة والموظف الحالي؛ الحقل المالي المرسل الوحيد paid_amount. لا اختيار موظف ولا paid_at قابل للإرسال. يحفظ الخادم الموظف ووقت التحصيل والتسجيل، وينشئ Enrollment active ويكمل الطلب في معاملة واحدة.
4. الرسوم NULL تمنع التأكيد، والصفر الصريح مجاني بمبلغ0 وpaid_at=NULL. الرسوم الموجبة تتطلب العملة وكامل المبلغ المطابق. السداد الجزئي والخصم والمبلغ السالب مرفوضة. بعد سجل مؤكد موثق القيمة تثبت رسوم المجموعة وعملتها، بما فيها التسجيل المجاني، وحتى بعد الانسحاب.
5. إعادة التأكيد تعيد التسجيل نفسه دون تحصيل أو إعادة كتابة مال أو موظف أو إشعار. الطالب لا يستطيع التأكيد، ولا توجد مدفوعات إلكترونية أو أقساط أو فواتير أو استردادات.
6. الطالب ينسحب من تسجيله النشط فقط. يحفظ withdrawn_at وحالة withdrawn والبيانات المالية والطلب، ويتحرر المقعد ويزول وصوله إلى المحاضرات وقائمة الطلاب النشطين. لا شرط إضافي لتاريخ أول محاضرة، ولا إعادة تفعيل تلقائية.

## 5. المهلة والسعة والتزامن

الحساب الموحد في TrainingGroupService:

`occupied = active enrollments`

`reserved = awaiting_confirmation AND offer_expires_at > now، مع استبعاد وجود Enrollment نشط للطلب نفسه`

`available = max(0, capacity - occupied - reserved)`

لا عداد مقاعد مكرر في قاعدة البيانات. انتهاء الحجز واستبعاد مقعده ورفض التأكيد يحصلون بحسب الوقت الفعلي، دون انتظار Scheduler. تعاد قراءة الحالة والموعد والسعة داخل المعاملة، مع قفل المجموعة ثم الطلب ثم التسجيل، والتحقق النهائي من المهلة قبل الإدراج.

جدولة المحاضر وتغيير إسناده يقفلان صفوف المحاضرين بترتيب ثابت قبل المجموعة، ثم يعاد التحقق من الإسناد المقفول. تجنبت الخدمات قراءة تثبت snapshot قبل انتظار القفل في REPEATABLE READ. توجد إعادة محاولة للـDeadlocks عبر DB::transaction(...,3). الأساس الإطاري موثق في [معاملات Laravel12](https://laravel.com/docs/12.x/database#database-transactions) و[الأقفال](https://laravel.com/docs/12.x/queries#pessimistic-locking).

الأمر ertqaa:expire-offers يقفل السجلات ويحول العرض المنتهي إلى expired ويرسل إشعارًا عند الانتقال الفعلي فقط. هو مسجل كل دقيقة في Laravel Scheduler، وإعادته آمنة. لم يُنشأ Windows Task أو Cron على مستوى النظام؛ تشغيل مجدول بيئة الاستضافة خطوة تشغيل عند النشر، كما في [دليل التطوير المحلي](LOCAL_DEVELOPMENT.md).

## 6. المحاضرات والصلاحيات والإشعارات

- التوليد من أول تاريخ وأيام أسبوعية ووقت بداية/نهاية وعدد1–200. التاريخ الأول يجب أن يطابق يومًا مختارًا. يولد ترقيمًا متسلسلًا فريدًا للمجموعة، ويرفض أي مجموعة لها محاضرات بدل تكرار الجدول أو الكتابة فوقه.
- الإنشاء المفرد يخصص الرقم خادميًا. النهاية بعد البداية. الإدخال والعرض بتوقيت Africa/Tripoli والتخزين UTC. لا جدول تكرار أو سجل تغييرات أو حالة postponed.
- التعارض بين المحاضرات scheduled بنطاق نصف مفتوح؛ الالتصاق بنهاية موعد سابق مسموح. التعديل يتطلب change_reason، والإلغاء يحفظ السبب والحالة دون حذف، والإكمال يحفظ completed. المكتملة والملغاة لا تقبلان إعادة الجدولة المعتادة.
- Admin/Staff يديران. المحاضر يعرض مجموعاته ومحاضراتها وطلابها النشطين فقط، والطالب يعرض محاضرات التسجيلات النشطة فقط. الانسحاب يزيل الوصول خادميًا، وليس من القائمة فقط.
- الإشعارات داخلية بالجداول القائمة: قبول مبدئي، رفض، انتهاء عرض، اكتمال تسجيل، تغيير موعد وإلغاء محاضرة. يتلقى إشعار المحاضرة المحاضر المعني والطلاب النشطون في مجموعتها فقط. التحول والإشعار في المعاملة نفسها، وإعادة الطلب لا تكرر الإشعار. تعليم المقروء للحساب المالك فقط وبوقت الخادم. لا Email/SMS/Push أو إعلانات عامة جديدة.

## 7. Stitch والواجهات

المشروع نفسه `1237952902287590626`. المراجع الجديدة المستخدمة:

| المرجع | معرف الشاشة |
|---|---|
| إدارة المحاضرات | ff1cea592b2a4162b1112f7d66a59b66 |
| مراجعة الطلبات | f4fc9c285dc4442798cae66c00341222 |
| طلباتي | ae0a6b97e0864bb69f7b9ccaeb277cfb |
| التأكيد الحضوري، معاد الاستخدام | cabb136db144424892ff73579e9d3f83 |

نجح get_screen للمراجع الثلاثة عند الاستئناف، رغم أن list_screens يعرض الشاشتين الأصليتين فقط؛ لم يُعد توليد أي مرجع. تنزيل وفحص HTML/PNG والتكييف موثق في [SPRINT_3_STITCH.md](SPRINT_3_STITCH.md). لم تُستورد القاعات أو الحالات أو الإحصاءات أو إجراءات الطباعة والتمديد التجريبية من التصميم.

13 قالب صفحة جديدًا: قائمة/تفاصيل المحاضرات، إنشاء/تعديل محاضرة، توليد الجدول، قائمة/تفاصيل طلب الإدارة، التأكيد الحضوري، قائمة/تفاصيل طلب الطالب، قائمة/تفاصيل تسجيله، طلاب المجموعة، والإشعارات. أعيد استخدام القوالب للقوائم ذات الأدوار المختلفة. وأربعة مكونات: application-badge وoffer-deadline وcapacity-summary وenrollment-record.

حُفظت الهوية السابقة والمكونات وCairo المحلي وRTL. أُضيفت الروابط والتقديم الحقيقي والسعة وإجراءات المرحلة إلى الصفحات القائمة دون إعادة تصميم Sprint1/2. المهلة المتبقية محسوبة خادميًا وقت عرض الصفحة، وتظهر نهايتها المطلقة؛ ليست عدادًا حيًا يُحدّث كل ثانية. يعيد الخادم التحقق عند أي إرسال.

## 8. نتائج الفحوص الفعلية

كل سيناريوهات الكتابة في قاعدة مستقلة `ertqaa_sprint3_check_e5af26b2` على الخادم نفسه، ومعاينة منفصلة على8001. لا إعادة إنشاء لقاعدة المشروع ولا حذف بياناتها. ملفات الفحوص والمعرفات والنتائج خارج Git؛ لا توجد كلمات مرور أو Cookies في هذا التقرير.

| الفحص | النتيجة والسياق |
|---|---|
| Migration/الحقول/FK/المال | نجحت في المشروع والقاعدة المعزولة؛ تحقق فعلي من DECIMAL غير السالب وFK المؤكد وUNIQUE التسجيل |
| الطلب والقبول | pending بلا حجز، رفض التكرار والمجموعة الملغاة، حجز48 ساعة، إعادة موافقة دون تمديد/إشعار آخر، رفض مقعد زائد |
| التأكيد | رفض الطالب والجزئي والسالب والرسومNULL؛ المجاني0 دون وقت تحصيل؛ المدفوع يحفظ الوقت والموظف؛ إعادة التأكيد تعيد السجل نفسه |
| الذرية | فشل مبلغ غير صحيح لم ينشئ تسجيلًا؛ وحقن فشل تخزين إشعار في القاعدة المعزولة بعد إدراج Enrollment رجع عن التسجيل وتحديث الطلب معًا |
| الانسحاب والتاريخ | رفض انسحاب طالب آخر؛ حفظ المبلغ/الوقت/الموظف، تحرير المقعد، منع إعادة التقديم والتفعيل، وتثبيت الرسوم/العملة حتى بعد الانسحاب |
| الانتهاء | استبعاد فوري من السعة قبل المجدول ورفض التأكيد؛ تشغيل الأمر مرتين أعطى1 ثم0 وإشعارًا واحدًا |
| تزامن الموافقة | عمليتا PHP وحاجز بدء مشترك لسعة1: حجز واحد واعتراض العملية الأخرى |
| تزامن التأكيد | موظفان للطلب نفسه: Enrollment واحد؛ طلبان محجوزان لسعة2: تسجيلان فقط |
| تزامن المحاضرات | إنشاء متعارض وإعادة جدولة متزامنة: اعتماد واحدة ورفض الأخرى |
| المحاضرات | توليد12، UTC الصحيح، رفض إعادة التوليد والتعارض وتراجع المجموعة كلها؛ السماح بموعدين متجاورين؛ السبب والإلغاء والإكمال ومنع تعديل النهائي |
| الاستهداف | إشعار التغيير للمحاضر والطالب النشط فقط، دون المنسحب أو طلاب مجموعات أخرى، ودون تكرار عند إعادة العملية |
| HTTP والأمان |403 لتأكيد الطالب أو تعديل طلب/تسجيل طالب آخر أو محاضرة غير مسموحة؛422 للهوية/وقت الدفع المزورين والمبلغ الجزئي والأيام/التواريخ غير الصحيحة والنص العربي الزائد |
| دورة المتصفح | تقديم → قبول مبدئي → رفض مبلغ29 بدل30 مع خطأ عربي وOld Input → تأكيد30 باسم الموظف → ظهور التسجيل والطالب للمحاضر → جدول محاضرتين وتعديل وإلغاء وإكمال → انسحاب مع تحذير عدم الاسترداد وحفظ التاريخ |
| الإشعارات في الواجهة | تعليم إشعار الحساب كمقروء وحفظ read_at؛ قائمة المالك فقط |
| العرض |81 حالة على1440/768/375: RTL وCairo ودون تجاوز خارجي أو IDs مكررة. قراءة الصور فعلًا، لا الاكتفاء بالبناء |
| المراجعة المستقلة |PASS؛ شملت42 عرضًا إضافيًا، وحوار التحصيل والانسحاب بالإلغاء فقط، والقائمة وإعادة التركيز وتمرير الجدول |
| البناء والسياق |view:cache وPHP lint للـViews المترجمة الـ130 وnpm run build وJavaScript syntax وroute:list ناجحة قبل التوقف؛ لم تُكرر بعد مطابقة الكود والنتائج المحفوظة |
| إغلاق الاستئناف |Migration Ran، جميع صفوف المشروع مطابقة للبصمات، الصفحة العامة200، والمنسحب يرى التاريخ ومحاضرته403 ولا أزرار انسحاب جديدة أو محاضرات نشطة له في المجموعة |

الفحوص هنا تشغيلية على DB حقيقية وHTTP ومتصفح، وليست PHPUnit أو Feature Test files. لا ادعاء لاختبار تحمّل طويل أو اختبار48 ساعة بانتظار زمني فعلي؛ محاكاة الموعد المنتهي والحالات الحدية جرت في القاعدة المعزولة. لا دليل نشر إنتاجي أو تشغيل Cron على مستوى النظام ضمن هذه المهمة.

## 9. الأخطاء ومعالجتها والقيود المتبقية

- خيار route:list --compact غير متاح في Laravel المثبت؛ استُخدم --json.
- احتاجت بيانات تجهيز القاعدة المعزولة إعادة تحميل User بعد إنشائه لقراءة is_active الافتراضية من DB قبل فحص السياسة. صُحح تجهيز الفحص دون تغيير Authentication أو بيانات المشروع.
- أضيف تحقق حجم TEXT بالبايت للسبب العربي، لرفض النص الطويل برسالة422 بدل خطأ تخزين؛ لا تُقبل مبالغ أو بيانات مؤكّد أو وقت من متصفح غير موثوق.
- ضُبط ترتيب الأقفال وموضع قراءة العلاقة قبل المعاملة لتفادي snapshot قديمة عند انتظار قفل المحاضر، ثم نجحت حالات التزامن الفعلية.
- انقطاع العمليات لم يكن فقدًا للكود أو Migration جزئية؛ أُعيد تشغيل البيئتين الموجودتين وفُحصت الحالة قبل أي كتابة. وكان ملف التقدم متأخرًا، فحُدّث من الكود والقاعدة والنتائج.
- قائمة Stitch لم تعرض المراجع الجديدة، لكن موارد get_screen المباشرة والصادرات سليمة؛ أُعيد استخدامها دون توليد مكرر.

**لا توجد أخطاء وظيفية قائمة مثبتة أو قرارات مانعة لإغلاق Sprint 3.** تشغيل مجدول الاستضافة عند النشر خطوة تشغيل لازمة لحفظ حالات الانتهاء وإشعاراتها، وليست شرطًا لتحرير المقاعد. النسخ الاحتياطي والنشر والإشعارات الخارجية والفواتير والأقساط والاستردادات والحضور والشهادات خارج النطاق، ولا تبدأ تلقائيًا كسبرنت جديدة.

## 10. خطوات المراجعة اليدوية

استخدم بيانات مخصصة للمراجعة في بيئة مناسبة؛ لا تعدّل سجلًا مهمًا لمجرد الفحص. قاعدة المشروع على8000، وبيئة الفحص المعزولة الموجودة على8001. لم تُنشر بيانات دخول افتراضية.

1. **مدير/موظف:** أنشئ/اختر مجموعة منشورة، نافذتها تشمل الوقت الحالي، ورسومها وعملتها محددتان وسعتها مناسبة. جهز مجموعة مجانية بأصفار صريحة وأخرى رسومها غير محددة عند مراجعة الفروق.
2. **طالب:** ادخل بحساب نشط له StudentProfile، واضغط «تقديم طلب الانضمام». راجع طلباتي وتحقق من pending ومنع تكرار التقديم، وعدم وجود تأكيد نهائي من الطالب.
3. **موظف:** افتح الطلب ووافق مبدئيًا. راجع المهلة والسعة، ثم جرّب مبلغًا أقل في نموذج التأكيد: يُرفض دون إنشاء Enrollment. بعد تحقق الحضور والتحصيل الحقيقي في الاستخدام الفعلي، أكد المبلغ الكامل؛ تظهر هوية حسابك ووقت الخادم.
4. **طالب ومحاضر:** يرى الطالب التسجيل ومحاضرات مجموعته، ويراه المحاضر ضمن الطلاب النشطين. جرّب حساب مجموعة أخرى: الوصول غير المصرح ممنوع خادميًا.
5. **مدير/موظف:** ولد جدولًا لمجموعة فارغة، ثم جرّب التوليد مجددًا والتداخل مع المحاضر نفسه. عدل مجدولة بسبب، وألغ أخرى بسبب، وأكمل محاضرة. لا تظهر هذه الإجراءات للمحاضر أو الطالب.
6. **طالب:** افتح تسجيلًا نشطًا، راجع تحذير عدم الاسترداد، ثم انسحب من سجل المراجعة المخصص. يبقى التاريخ المالي ويختفي الوصول إلى محاضرات المجموعة ويُحرر المقعد.
7. **الإشعارات والانتهاء:** راجع إشعارات الحساب وحدد واحدًا كمقروء. للحالات الزمنية استخدم قاعدة معزولة؛ تشغيل `php artisan ertqaa:expire-offers` مرتين لا يكرر إشعار الانتهاء. لا تُغيّر تواريخ عروض حقيقية لمجرد الفحص.
8. جرّب الهاتف وDesktop والتحقق من الأخطاء والرجوع من الحوار دون إرسال. طريقة تشغيل Scheduler موضحة في [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md).

## صور التسليم

صور من التطبيق الفعلي أثناء فحوص القاعدة المعزولة، وببيانات موسومة «فحص»، وليست بيانات مركز أو أرقامًا ثابتة في الواجهة:

- المحاضرات: [Desktop](previews/sprint3/lectures-desktop.png) و[Mobile](previews/sprint3/lectures-mobile.png).
- [مراجعة الطلبات](previews/sprint3/applications-desktop.png).
- التأكيد الحضوري: [Desktop](previews/sprint3/confirmation-desktop.png) و[Mobile](previews/sprint3/confirmation-mobile.png).
- [طلباتي Mobile](previews/sprint3/student-applications-mobile.png).
- [تسجيلي Mobile](previews/sprint3/student-enrollment-mobile.png).
- [طلاب المجموعة للمحاضر Mobile](previews/sprint3/instructor-students-mobile.png).
- [الإشعارات Mobile](previews/sprint3/notifications-mobile.png).

مرجع الاستئناف وحالة كل مرحلة: [SPRINT_3_PROGRESS.md](SPRINT_3_PROGRESS.md). جميع ملفات المشروع السابقة محفوظة، وتبقى قاعدة الفحص وسجلاتها منفصلة دون حذف أو تنظيف تلقائي.

## قائمة الملفات مقارنة ببداية Sprint 3

هذه القائمة تقارن ببصمات بداية المرحلة، فتفصل تغييرات Sprint 3 عن إعادة التصميم السابقة غير الملتزمة في Git.

| الحالة | الملف |
|---|---|
| جديد | [app/Console/Commands/ExpireEnrollmentOffers.php](../app/Console/Commands/ExpireEnrollmentOffers.php) |
| جديد | [app/Http/Controllers/ApplicationReviewController.php](../app/Http/Controllers/ApplicationReviewController.php) |
| جديد | [app/Http/Controllers/GroupStudentsController.php](../app/Http/Controllers/GroupStudentsController.php) |
| جديد | [app/Http/Controllers/InternalNotificationController.php](../app/Http/Controllers/InternalNotificationController.php) |
| جديد | [app/Http/Controllers/LectureController.php](../app/Http/Controllers/LectureController.php) |
| معدل | [app/Http/Controllers/PublicCatalogController.php](../app/Http/Controllers/PublicCatalogController.php) |
| جديد | [app/Http/Controllers/StudentApplicationController.php](../app/Http/Controllers/StudentApplicationController.php) |
| جديد | [app/Http/Controllers/StudentEnrollmentController.php](../app/Http/Controllers/StudentEnrollmentController.php) |
| جديد | [app/Http/Requests/ApplyToGroupRequest.php](../app/Http/Requests/ApplyToGroupRequest.php) |
| جديد | [app/Http/Requests/ConfirmEnrollmentRequest.php](../app/Http/Requests/ConfirmEnrollmentRequest.php) |
| جديد | [app/Http/Requests/DecisionReasonRequest.php](../app/Http/Requests/DecisionReasonRequest.php) |
| جديد | [app/Http/Requests/GenerateLecturesRequest.php](../app/Http/Requests/GenerateLecturesRequest.php) |
| جديد | [app/Http/Requests/LectureRequest.php](../app/Http/Requests/LectureRequest.php) |
| معدل | [app/Http/Requests/TrainingGroupRequest.php](../app/Http/Requests/TrainingGroupRequest.php) |
| معدل | [app/Models/Enrollment.php](../app/Models/Enrollment.php) |
| معدل | [app/Models/EnrollmentApplication.php](../app/Models/EnrollmentApplication.php) |
| جديد | [app/Policies/EnrollmentApplicationPolicy.php](../app/Policies/EnrollmentApplicationPolicy.php) |
| جديد | [app/Policies/EnrollmentPolicy.php](../app/Policies/EnrollmentPolicy.php) |
| جديد | [app/Policies/LecturePolicy.php](../app/Policies/LecturePolicy.php) |
| جديد | [app/Policies/NotificationRecipientPolicy.php](../app/Policies/NotificationRecipientPolicy.php) |
| معدل | [app/Policies/TrainingGroupPolicy.php](../app/Policies/TrainingGroupPolicy.php) |
| معدل | [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php) |
| جديد | [app/Rules/FitsTextColumn.php](../app/Rules/FitsTextColumn.php) |
| جديد | [app/Services/EnrollmentService.php](../app/Services/EnrollmentService.php) |
| جديد | [app/Services/InternalNotificationService.php](../app/Services/InternalNotificationService.php) |
| جديد | [app/Services/LectureService.php](../app/Services/LectureService.php) |
| معدل | [app/Services/TrainingGroupService.php](../app/Services/TrainingGroupService.php) |
| جديد | [app/Support/Money.php](../app/Support/Money.php) |
| جديد | [database/migrations/2026_11_13_000001_add_confirmation_details_to_enrollments.php](../database/migrations/2026_11_13_000001_add_confirmation_details_to_enrollments.php) |
| معدل | [docs/LOCAL_DEVELOPMENT.md](../docs/LOCAL_DEVELOPMENT.md) |
| جديد | [docs/previews/sprint3/applications-desktop.png](../docs/previews/sprint3/applications-desktop.png) |
| جديد | [docs/previews/sprint3/confirmation-desktop.png](../docs/previews/sprint3/confirmation-desktop.png) |
| جديد | [docs/previews/sprint3/confirmation-mobile.png](../docs/previews/sprint3/confirmation-mobile.png) |
| جديد | [docs/previews/sprint3/instructor-students-mobile.png](../docs/previews/sprint3/instructor-students-mobile.png) |
| جديد | [docs/previews/sprint3/lectures-desktop.png](../docs/previews/sprint3/lectures-desktop.png) |
| جديد | [docs/previews/sprint3/lectures-mobile.png](../docs/previews/sprint3/lectures-mobile.png) |
| جديد | [docs/previews/sprint3/notifications-mobile.png](../docs/previews/sprint3/notifications-mobile.png) |
| جديد | [docs/previews/sprint3/student-applications-mobile.png](../docs/previews/sprint3/student-applications-mobile.png) |
| جديد | [docs/previews/sprint3/student-enrollment-mobile.png](../docs/previews/sprint3/student-enrollment-mobile.png) |
| جديد | [docs/SPRINT_3_PROGRESS.md](../docs/SPRINT_3_PROGRESS.md) |
| جديد | [docs/SPRINT_3_REPORT.md](../docs/SPRINT_3_REPORT.md) |
| جديد | [docs/SPRINT_3_SCHEMA_ADDENDUM.md](../docs/SPRINT_3_SCHEMA_ADDENDUM.md) |
| جديد | [docs/SPRINT_3_STITCH.md](../docs/SPRINT_3_STITCH.md) |
| معدل | [lang/ar/validation.php](../lang/ar/validation.php) |
| معدل | [resources/css/app.css](../resources/css/app.css) |
| جديد | [resources/views/admin/applications/confirm.blade.php](../resources/views/admin/applications/confirm.blade.php) |
| جديد | [resources/views/admin/applications/index.blade.php](../resources/views/admin/applications/index.blade.php) |
| جديد | [resources/views/admin/applications/show.blade.php](../resources/views/admin/applications/show.blade.php) |
| معدل | [resources/views/admin/groups/form.blade.php](../resources/views/admin/groups/form.blade.php) |
| معدل | [resources/views/admin/groups/show.blade.php](../resources/views/admin/groups/show.blade.php) |
| جديد | [resources/views/admin/lectures/form.blade.php](../resources/views/admin/lectures/form.blade.php) |
| جديد | [resources/views/admin/lectures/generate.blade.php](../resources/views/admin/lectures/generate.blade.php) |
| جديد | [resources/views/components/application-badge.blade.php](../resources/views/components/application-badge.blade.php) |
| معدل | [resources/views/components/badge.blade.php](../resources/views/components/badge.blade.php) |
| جديد | [resources/views/components/capacity-summary.blade.php](../resources/views/components/capacity-summary.blade.php) |
| جديد | [resources/views/components/enrollment-record.blade.php](../resources/views/components/enrollment-record.blade.php) |
| معدل | [resources/views/components/group-details.blade.php](../resources/views/components/group-details.blade.php) |
| معدل | [resources/views/components/icon.blade.php](../resources/views/components/icon.blade.php) |
| معدل | [resources/views/components/layout.blade.php](../resources/views/components/layout.blade.php) |
| جديد | [resources/views/components/offer-deadline.blade.php](../resources/views/components/offer-deadline.blade.php) |
| معدل | [resources/views/components/public-navigation.blade.php](../resources/views/components/public-navigation.blade.php) |
| معدل | [resources/views/components/workspace-sidebar.blade.php](../resources/views/components/workspace-sidebar.blade.php) |
| معدل | [resources/views/instructor/groups/show.blade.php](../resources/views/instructor/groups/show.blade.php) |
| جديد | [resources/views/instructor/groups/students.blade.php](../resources/views/instructor/groups/students.blade.php) |
| جديد | [resources/views/lectures/index.blade.php](../resources/views/lectures/index.blade.php) |
| جديد | [resources/views/lectures/show.blade.php](../resources/views/lectures/show.blade.php) |
| جديد | [resources/views/notifications/index.blade.php](../resources/views/notifications/index.blade.php) |
| معدل | [resources/views/public/groups/index.blade.php](../resources/views/public/groups/index.blade.php) |
| معدل | [resources/views/public/groups/show.blade.php](../resources/views/public/groups/show.blade.php) |
| جديد | [resources/views/student/applications/index.blade.php](../resources/views/student/applications/index.blade.php) |
| جديد | [resources/views/student/applications/show.blade.php](../resources/views/student/applications/show.blade.php) |
| جديد | [resources/views/student/enrollments/index.blade.php](../resources/views/student/enrollments/index.blade.php) |
| جديد | [resources/views/student/enrollments/show.blade.php](../resources/views/student/enrollments/show.blade.php) |
| معدل | [routes/console.php](../routes/console.php) |
| معدل | [routes/web.php](../routes/web.php) |

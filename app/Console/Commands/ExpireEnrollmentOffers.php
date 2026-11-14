<?php

namespace App\Console\Commands;

use App\Models\EnrollmentApplication;
use App\Services\EnrollmentService;
use Illuminate\Console\Command;

class ExpireEnrollmentOffers extends Command
{
    protected $signature = 'ertqaa:expire-offers';

    protected $description = 'إنهاء عروض القبول التي انتهت مهلتها دون حذف الطلبات';

    public function handle(EnrollmentService $service): int
    {
        $count = 0;
        EnrollmentApplication::where('status', 'awaiting_confirmation')->where(fn ($q) => $q->whereNull('offer_expires_at')->orWhere('offer_expires_at', '<=', now()))->select(['id', 'training_group_id'])->chunkById(100, function ($applications) use ($service, &$count) {
            foreach ($applications as $application) {
                $count += (int) $service->expire($application);
            }
        });
        $this->info('العروض التي أُنهيت: '.$count);

        return self::SUCCESS;
    }
}

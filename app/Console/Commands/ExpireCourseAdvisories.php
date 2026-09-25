<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\CourseAccess;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('courses:expire-advisories')]
#[Description('Vence las asesorías de cursos híbridos cuya ventana de redención pasó sin haberse agendado una reserva.')]
class ExpireCourseAdvisories extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredCount = 0;

        CourseAccess::query()
            ->active()
            ->whereNotNull('advisory_redeemable_until')
            ->where('advisory_redeemable_until', '<', now())
            ->whereNull('advisory_expired_at')
            ->each(function (CourseAccess $courseAccess) use (&$expiredCount): void {
                $hasBooking = Booking::query()
                    ->where('user_id', $courseAccess->user_id)
                    ->where('course_id', $courseAccess->course_id)
                    ->exists();

                if ($hasBooking) {
                    return;
                }

                $courseAccess->update(['advisory_expired_at' => now()]);
                $expiredCount++;
            });

        Log::info('Asesorías de cursos vencidas por ventana de redención expirada.', [
            'expired_count' => $expiredCount,
        ]);
    }
}

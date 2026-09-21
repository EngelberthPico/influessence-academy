<?php

namespace App\Models;

use App\Enums\CourseType;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'description', 'price_cents', 'currency', 'vimeo_id', 'is_published', 'is_best_seller', 'published_at', 'type', 'session_count', 'duration_months', 'redemption_window_days'])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_best_seller' => 'boolean',
            'published_at' => 'datetime',
            'type' => CourseType::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Course $course): void {
            if (blank($course->slug) || $course->isDirty('title')) {
                $course->slug = Str::slug($course->title);
            }

            if ($course->is_published && $course->isDirty('is_published') && blank($course->published_at)) {
                $course->published_at = now();
            }
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function courseAccesses(): HasMany
    {
        return $this->hasMany(CourseAccess::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_accesses');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('position')->orderBy('id');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class)
            ->orderBy('course_modules.position')
            ->orderBy('lessons.position');
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_months) {
            return $this->duration_months === 1
                ? '1 mes'
                : "{$this->duration_months} meses";
        }

        if ($this->session_count) {
            return $this->session_count === 1
                ? '1 sesión'
                : "{$this->session_count} sesiones";
        }

        return $this->type->getLabel();
    }

    public function getCardLabelAttribute(): string
    {
        return match ($this->type) {
            CourseType::LiveProgram => $this->duration_label,
            CourseType::Hybrid => 'Curso + asesoría',
            CourseType::Recorded => 'Grabado',
        };
    }
}

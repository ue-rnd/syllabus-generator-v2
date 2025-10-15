<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'report_template_id',
        'frequency',
        'frequency_config',
        'next_run_at',
        'last_run_at',
        'is_active',
        'created_by',
        'output_format',
        'delivery_method',
        'delivery_config',
        'timezone',
    ];

    protected $casts = [
        'frequency_config' => 'array',
        'delivery_config' => 'array',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function reportTemplate()
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customReports()
    {
        return $this->hasMany(CustomReport::class, 'schedule_id');
    }

    public function complianceReports()
    {
        return $this->hasMany(ComplianceReport::class, 'schedule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_run_at', '<=', now())->where('is_active', true);
    }

    public function getFrequencyColorAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => 'success',
            'weekly' => 'primary',
            'monthly' => 'warning',
            'quarterly' => 'info',
            'yearly' => 'purple',
            'custom' => 'orange',
            default => 'gray',
        };
    }

    public function calculateNextRun(): void
    {
        $nextRun = match ($this->frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            'custom' => $this->calculateCustomNextRun(),
            default => now()->addDay(),
        };

        $this->update(['next_run_at' => $nextRun]);
    }

    private function calculateCustomNextRun()
    {
        $config = $this->frequency_config ?? [];
        $interval = $config['interval'] ?? 1;
        $unit = $config['unit'] ?? 'days';

        return match ($unit) {
            'hours' => now()->addHours($interval),
            'days' => now()->addDays($interval),
            'weeks' => now()->addWeeks($interval),
            'months' => now()->addMonths($interval),
            default => now()->addDay(),
        };
    }

    public function executeSchedule(): void
    {
        if (!$this->is_active || $this->next_run_at > now()) {
            return;
        }

        try {
            $report = $this->reportTemplate->generateReport([
                'name' => $this->name . ' - ' . now()->format('Y-m-d H:i'),
                'is_scheduled' => true,
                'schedule_id' => $this->id,
                'output_format' => $this->output_format,
            ]);

            $report->generateReport();

            if ($report->isCompleted() && $this->delivery_method !== 'none') {
                $this->deliverReport($report);
            }

            $this->update(['last_run_at' => now()]);
            $this->calculateNextRun();

        } catch (\Exception $e) {
            \Log::error("Failed to execute scheduled report {$this->id}: " . $e->getMessage());
        }
    }

    private function deliverReport(CustomReport $report): void
    {
        // Implementation for report delivery (email, etc.)
    }

    public static function getFrequencyOptions(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom',
        ];
    }

    public static function getDeliveryMethodOptions(): array
    {
        return [
            'none' => 'No Delivery (Save Only)',
            'email' => 'Email',
            'download' => 'Download Link',
        ];
    }
}
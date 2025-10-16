<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityChecklistItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quality_checklist_id',
        'title',
        'description',
        'field_to_check',
        'validation_rule',
        'validation_parameters',
        'weight',
        'is_mandatory',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'validation_parameters' => 'array',
        'weight' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function qualityChecklist()
    {
        return $this->belongsTo(QualityChecklist::class);
    }

    public function validateSyllabus(Syllabus $syllabus): array
    {
        $fieldValue = $this->getFieldValue($syllabus);
        $result = $this->performValidation($fieldValue);

        return [
            'item_id' => $this->id,
            'title' => $this->title,
            'status' => $result['passed'] ? 'passed' : 'failed',
            'score' => $result['score'],
            'message' => $result['message'],
            'field_checked' => $this->field_to_check,
            'checked_at' => now()->toISOString(),
        ];
    }

    private function getFieldValue(Syllabus $syllabus)
    {
        if (str_contains($this->field_to_check, '.')) {
            $parts = explode('.', $this->field_to_check);
            $value = $syllabus;

            foreach ($parts as $part) {
                if (is_object($value) && isset($value->{$part})) {
                    $value = $value->{$part};
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return null;
                }
            }

            return $value;
        }

        return $syllabus->{$this->field_to_check} ?? null;
    }

    private function performValidation($fieldValue): array
    {
        return $this->applyValidationRule(
            $this->validation_rule,
            $fieldValue,
            $this->validation_parameters ?? []
        );
    }

    private function applyValidationRule(string $rule, $value, array $params = []): array
    {
        $ruleType = (string) $rule;

        return match ($ruleType) {
            'required' => $this->validateRequired($value),
            'min_length' => $this->validateMinLength($value, $params['min_length'] ?? 10),
            'max_length' => $this->validateMaxLength($value, $params['max_length'] ?? 5000),
            'array_min_items' => $this->validateArrayMinItems($value, $params['min_items'] ?? 1),
            'array_max_items' => $this->validateArrayMaxItems($value, $params['max_items'] ?? 50),
            'numeric_range' => $this->validateNumericRange($value, $params['min'] ?? 0, $params['max'] ?? 100),
            'date_range' => $this->validateDateRange($value, $params['start_date'] ?? null, $params['end_date'] ?? null),
            'format_check' => $this->validateFormat($value, $params['pattern'] ?? ''),
            'completeness' => $this->validateCompleteness($value),
            default => ['passed' => true, 'score' => 100, 'message' => 'No validation rule applied'],
        };
    }

    private function validateRequired($value): array
    {
        $passed = ! empty($value) && trim(strip_tags((string) $value)) !== '';

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : 0,
            'message' => $passed ? 'Field is present and not empty' : 'Field is required but missing or empty',
        ];
    }

    private function validateMinLength($value, int $minLength): array
    {
        $text = strip_tags($value ?? '');
        $length = strlen(trim($text));
        $passed = $length >= $minLength;

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : round(($length / $minLength) * 100, 1),
            'message' => $passed
                ? "Content meets minimum length requirement ({$length}/{$minLength} characters)"
                : "Content too short: {$length} characters, minimum required: {$minLength}",
        ];
    }

    private function validateMaxLength($value, int $maxLength): array
    {
        $text = strip_tags($value ?? '');
        $length = strlen(trim($text));
        $passed = $length <= $maxLength;

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : max(0, 100 - round((($length - $maxLength) / $maxLength) * 100, 1)),
            'message' => $passed
                ? "Content within length limit ({$length}/{$maxLength} characters)"
                : "Content too long: {$length} characters, maximum allowed: {$maxLength}",
        ];
    }

    private function validateArrayMinItems($value, int $minItems): array
    {
        $items = is_array($value) ? $value : [];
        $count = count($items);
        $passed = $count >= $minItems;

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : round(($count / $minItems) * 100, 1),
            'message' => $passed
                ? "Has sufficient items ({$count}/{$minItems})"
                : "Insufficient items: {$count}, minimum required: {$minItems}",
        ];
    }

    private function validateArrayMaxItems($value, int $maxItems): array
    {
        $items = is_array($value) ? $value : [];
        $count = count($items);
        $passed = $count <= $maxItems;

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : max(0, 100 - round((($count - $maxItems) / $maxItems) * 100, 1)),
            'message' => $passed
                ? "Within item limit ({$count}/{$maxItems})"
                : "Too many items: {$count}, maximum allowed: {$maxItems}",
        ];
    }

    private function validateNumericRange($value, $min, $max): array
    {
        $number = is_numeric($value) ? (float) $value : 0;
        $passed = $number >= $min && $number <= $max;

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : 0,
            'message' => $passed
                ? "Value ({$number}) within acceptable range ({$min}-{$max})"
                : "Value ({$number}) outside acceptable range ({$min}-{$max})",
        ];
    }

    private function validateDateRange($value, $startDate, $endDate): array
    {
        if (! $value) {
            return ['passed' => false, 'score' => 0, 'message' => 'No date provided'];
        }

        $date = \Carbon\Carbon::parse($value);
        $start = $startDate ? \Carbon\Carbon::parse($startDate) : null;
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : null;

        $passed = true;
        $message = 'Date is valid';

        if ($start && $date->lt($start)) {
            $passed = false;
            $message = "Date is before acceptable start date ({$start->format('Y-m-d')})";
        } elseif ($end && $date->gt($end)) {
            $passed = false;
            $message = "Date is after acceptable end date ({$end->format('Y-m-d')})";
        }

        return [
            'passed' => $passed,
            'score' => $passed ? 100 : 0,
            'message' => $message,
        ];
    }

    private function validateFormat($value, string $pattern): array
    {
        if (! $pattern) {
            return ['passed' => true, 'score' => 100, 'message' => 'No format pattern specified'];
        }

        $text = strip_tags($value ?? '');
        $passed = preg_match($pattern, $text);

        return [
            'passed' => (bool) $passed,
            'score' => $passed ? 100 : 0,
            'message' => $passed
                ? 'Content matches required format'
                : 'Content does not match required format pattern',
        ];
    }

    private function validateCompleteness($value): array
    {
        if (is_array($value)) {
            $completeness = $this->calculateArrayCompleteness($value);
        } else {
            $completeness = $this->calculateTextCompleteness($value);
        }

        $passed = $completeness >= 80; // 80% completeness threshold

        return [
            'passed' => $passed,
            'score' => $completeness,
            'message' => "Content completeness: {$completeness}%",
        ];
    }

    private function calculateArrayCompleteness($array): float
    {
        if (empty($array)) {
            return 0;
        }

        $totalItems = count($array);
        $completeItems = 0;

        foreach ($array as $item) {
            if (is_array($item)) {
                $itemCompleteness = 0;
                $itemFields = count($item);
                $completedFields = 0;

                foreach ($item as $value) {
                    if (! empty($value) && trim(strip_tags((string) $value)) !== '') {
                        $completedFields++;
                    }
                }

                $itemCompleteness = $itemFields > 0 ? ($completedFields / $itemFields) : 1;
                $completeItems += $itemCompleteness;
            } else {
                if (! empty($item) && trim(strip_tags((string) $item)) !== '') {
                    $completeItems++;
                }
            }
        }

        return round(($completeItems / $totalItems) * 100, 1);
    }

    private function calculateTextCompleteness($text): float
    {
        $cleanText = trim(strip_tags($text ?? ''));

        if (empty($cleanText)) {
            return 0;
        }

        $wordCount = str_word_count($cleanText);
        $sentenceCount = preg_match_all('/[.!?]+/', $cleanText);

        // Basic completeness heuristics
        $lengthScore = min(100, ($wordCount / 50) * 100); // Assume 50 words is "complete"
        $structureScore = $sentenceCount > 0 ? min(100, ($sentenceCount / 3) * 100) : 0; // At least 3 sentences

        return round(($lengthScore + $structureScore) / 2, 1);
    }

    public static function getValidationRuleOptions(): array
    {
        return [
            'required' => 'Required Field',
            'min_length' => 'Minimum Length',
            'max_length' => 'Maximum Length',
            'contains_keywords' => 'Contains Keywords',
            'array_min_items' => 'Minimum Array Items',
            'array_max_items' => 'Maximum Array Items',
            'numeric_range' => 'Numeric Range',
            'date_range' => 'Date Range',
            'format_check' => 'Format Pattern',
            'completeness' => 'Content Completeness',
        ];
    }
}

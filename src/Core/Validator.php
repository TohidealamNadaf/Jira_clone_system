<?php
/**
 * Input Validator
 */

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $validated = [];

    private array $messages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'min' => 'The :field must be at least :param characters.',
        'max' => 'The :field must not exceed :param characters.',
        'minValue' => 'The :field must be at least :param.',
        'maxValue' => 'The :field must not exceed :param.',
        'numeric' => 'The :field must be a number.',
        'integer' => 'The :field must be an integer.',
        'string' => 'The :field must be a string.',
        'array' => 'The :field must be an array.',
        'boolean' => 'The :field must be true or false.',
        'confirmed' => 'The :field confirmation does not match.',
        'unique' => 'The :field has already been taken.',
        'exists' => 'The selected :field is invalid.',
        'in' => 'The selected :field is invalid.',
        'not_in' => 'The selected :field is invalid.',
        'date' => 'The :field must be a valid date.',
        'date_format' => 'The :field does not match the format :param.',
        'before' => 'The :field must be a date before :param.',
        'after' => 'The :field must be a date after :param.',
        'url' => 'The :field must be a valid URL.',
        'regex' => 'The :field format is invalid.',
        'alpha' => 'The :field may only contain letters.',
        'alpha_num' => 'The :field may only contain letters and numbers.',
        'alpha_dash' => 'The :field may only contain letters, numbers, dashes, and underscores.',
        'file' => 'The :field must be a file.',
        'image' => 'The :field must be an image.',
        'mimes' => 'The :field must be a file of type: :param.',
        'max_file' => 'The :field must not exceed :param kilobytes.',
        'same' => 'The :field and :param must match.',
        'different' => 'The :field and :param must be different.',
        'password' => 'The :field must meet the password requirements.',
    ];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Run validation
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $rules = is_string($rules) ? explode('|', $rules) : $rules;
            $value = $this->getValue($field);
            $isNullable = in_array('nullable', $rules);

            foreach ($rules as $rule) {
                if ($rule === 'nullable') {
                    continue;
                }

                // Skip validation if nullable and empty
                if ($isNullable && ($value === null || $value === '')) {
                    continue;
                }

                $this->validateRule($field, $value, $rule);
            }

            // Add to validated if no errors for this field
            if (!isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }

        return empty($this->errors);
    }

    /**
     * Get value from data using dot notation
     */
    private function getValue(string $field): mixed
    {
        $keys = explode('.', $field);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Validate a single rule
     */
    private function validateRule(string $field, mixed $value, string $rule): void
    {
        // Parse rule:param format
        $param = null;
        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        $method = 'validate' . str_replace('_', '', ucwords($rule, '_'));

        if (!method_exists($this, $method)) {
            throw new \RuntimeException("Unknown validation rule: $rule");
        }

        if (!$this->$method($value, $param, $field)) {
            $this->addError($field, $rule, $param);
        }
    }

    /**
     * Add validation error
     */
    private function addError(string $field, string $rule, ?string $param = null): void
    {
        $message = $this->messages[$rule] ?? "The :field field is invalid.";
        $message = str_replace(':field', $this->humanize($field), $message);
        $message = str_replace(':param', $param ?? '', $message);

        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Convert field name to human readable
     */
    private function humanize(string $field): string
    {
        return str_replace(['_', '.'], ' ', $field);
    }

    /**
     * Get validation errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get validated data
     */
    public function validated(): array
    {
        return $this->validated;
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    // Validation Rules

    private function validateRequired(mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        if (is_array($value) && count($value) === 0) {
            return false;
        }
        return true;
    }

    private function validateEmail(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin(mixed $value, string $param): bool
    {
        $length = is_array($value) ? count($value) : mb_strlen((string) $value);
        return $length >= (int) $param;
    }

    private function validateMax(mixed $value, string $param): bool
    {
        $length = is_array($value) ? count($value) : mb_strlen((string) $value);
        return $length <= (int) $param;
    }

    private function validateMinValue(mixed $value, string $param): bool
    {
        return is_numeric($value) && $value >= $param;
    }

    private function validateMaxValue(mixed $value, string $param): bool
    {
        return is_numeric($value) && $value <= $param;
    }

    private function validateNumeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    private function validateInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateString(mixed $value): bool
    {
        return is_string($value);
    }

    private function validateArray(mixed $value): bool
    {
        return is_array($value);
    }

    private function validateBoolean(mixed $value): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1', 'true', 'false'], true);
    }

    private function validateConfirmed(mixed $value, ?string $param, string $field): bool
    {
        $confirmField = $field . '_confirmation';
        return $value === ($this->data[$confirmField] ?? null);
    }

    private function validateUnique(mixed $value, string $param, string $field): bool
    {
        $parts = explode(',', $param);
        $table = $parts[0];
        $column = $parts[1] ?? $field;
        $exceptId = $parts[2] ?? null;

        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
        $params = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        return Database::selectValue($sql, $params) == 0;
    }

    private function validateExists(mixed $value, string $param): bool
    {
        $parts = explode(',', $param);
        $table = $parts[0];
        $column = $parts[1] ?? 'id';

        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
        return Database::selectValue($sql, [$value]) > 0;
    }

    private function validateIn(mixed $value, string $param): bool
    {
        $allowed = explode(',', $param);
        return in_array($value, $allowed, true);
    }

    private function validateNotIn(mixed $value, string $param): bool
    {
        $disallowed = explode(',', $param);
        return !in_array($value, $disallowed, true);
    }

    private function validateDate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        return strtotime($value) !== false;
    }

    private function validateDateFormat(mixed $value, string $param): bool
    {
        $date = \DateTime::createFromFormat($param, $value);
        return $date && $date->format($param) === $value;
    }

    private function validateBefore(mixed $value, string $param): bool
    {
        return strtotime($value) < strtotime($param);
    }

    private function validateAfter(mixed $value, string $param): bool
    {
        return strtotime($value) > strtotime($param);
    }

    private function validateUrl(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateRegex(mixed $value, string $param): bool
    {
        return preg_match($param, (string) $value) === 1;
    }

    private function validateAlpha(mixed $value): bool
    {
        return preg_match('/^[\pL\pM]+$/u', (string) $value) === 1;
    }

    private function validateAlphaNum(mixed $value): bool
    {
        return preg_match('/^[\pL\pM\pN]+$/u', (string) $value) === 1;
    }

    private function validateAlphaDash(mixed $value): bool
    {
        return preg_match('/^[\pL\pM\pN_-]+$/u', (string) $value) === 1;
    }

    private function validateSame(mixed $value, string $param): bool
    {
        return $value === ($this->data[$param] ?? null);
    }

    private function validateDifferent(mixed $value, string $param): bool
    {
        return $value !== ($this->data[$param] ?? null);
    }

    private function validatePassword(mixed $value): bool
    {
        $config = config('security', []);
        $minLength = $config['password_min_length'] ?? 8;
        
        if (mb_strlen((string) $value) < $minLength) {
            return false;
        }
        
        if (($config['password_require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $value)) {
            return false;
        }
        
        if (($config['password_require_number'] ?? true) && !preg_match('/[0-9]/', $value)) {
            return false;
        }
        
        if (($config['password_require_special'] ?? false) && !preg_match('/[^a-zA-Z0-9]/', $value)) {
            return false;
        }
        
        return true;
    }

    private function validateFile(mixed $value, ?string $param, string $field): bool
    {
        $file = $_FILES[$field] ?? null;
        return $file && $file['error'] === UPLOAD_ERR_OK;
    }

    private function validateImage(mixed $value, ?string $param, string $field): bool
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($file['type'], $imageTypes);
    }

    private function validateMimes(mixed $value, string $param, string $field): bool
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $allowed = explode(',', $param);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, $allowed);
    }

    private function validateMaxFile(mixed $value, string $param, string $field): bool
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return true; // Let required rule handle missing files
        }
        
        $maxKb = (int) $param;
        return $file['size'] <= ($maxKb * 1024);
    }
}

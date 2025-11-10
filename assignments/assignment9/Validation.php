<?php
class Validation {
    private $errors = [];

    public function checkFormat($value, $type, $customErrorMsg = null) {
        $patterns = [
            'name'    => '/^[a-z\'\s-]{1,50}$/i',
            'phone'   => '/^\d{3}\.\d{3}\.\d{4}$/',
            'address' => '/^[a-zA-Z0-9\s,.\'-]{1,100}$/',
            'zip'     => '/^\d{5}(-\d{4})?$/',
            'email'   => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'none'    => '/.*/'
        ];

        $pattern = $patterns[$type] ?? '/.*/';

        if (!preg_match($pattern, $value)) {
            $errorMessage = $customErrorMsg ?? "Invalid $type format.";
            $this->errors[$type] = $errorMessage;
            return false;
        }

        return true;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }
}
?>

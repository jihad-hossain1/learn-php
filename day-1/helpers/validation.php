<?php
function validateUserInput($data) {
    $errors = [];
    
    // Validate name
    $name = isset($data['name']) ? htmlspecialchars($data['name']) : '';
    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    // Validate age
    $age = isset($data['age']) ? filter_var($data['age'], FILTER_VALIDATE_INT) : null;
    if ($age === null || $age < 0 || $age > 120) {
        $errors[] = 'Please enter a valid age between 0 and 120';
    }

    // Validate email
    $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : '';
    if (empty($email)) {
        $errors[] = 'Valid email is required';
    }

    // Validate gender
    $gender = isset($data['gender']) ? htmlspecialchars($data['gender']) : '';
    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }

    return [
        'errors' => $errors,
        'data' => [
            'name' => $name,
            'age' => $age,
            'email' => $email,
            'gender' => $gender
        ]
    ];
}
?>
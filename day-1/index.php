
<?php
require_once 'config/config.php';
require_once 'helpers/validation.php';

// Process form only if submitted
if (isset($_GET['reset'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])) {
    $validation = validateUserInput($_GET);
    
    if (!empty($validation['errors'])) {
        foreach ($validation['errors'] as $error) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"> ' . $error . '</span>
                  </div>';
        }
    } else {
        $data = $validation['data'];
        echo '<script>window.formSubmitted = true;</script>';
        echo '<script>
            window.submittedData = {
                name: "' . $data['name'] . '",
                age: "' . $data['age'] . '",
                email: "' . $data['email'] . '",
                gender: "' . ucfirst($data['gender']) . '"
            };
        </script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center"><?php echo SITE_NAME; ?></h2>
        <?php include 'templates/form.php'; ?>
    </div>

    <?php include 'templates/modal.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
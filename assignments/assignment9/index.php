<?php
require_once 'classes/Db_conn.php';
require_once 'classes/Pdo_Methods.php';
require_once 'classes/Validation.php';
require_once 'classes/StickyForm.php';

// Instantiate StickyForm 
$form = new StickyForm();
$pdo = new PdoMethods();

// Initialize the form 
$formConfig = [
    'first_name' => [
        'id' => 'first_name',
        'name' => 'first_name',
        'label' => 'First Name',
        'type' => 'text',
        'regex' => 'name',
        'errorMsg' => 'Please enter a valid first name.',
        'value' => '' 
    ],
    'last_name' => [
        'id' => 'last_name',
        'name' => 'last_name',
        'label' => 'Last Name',
        'type' => 'text',
        'regex' => 'name',
        'errorMsg' => 'Please enter a valid last name.',
        'value' => '' 
    ],
    'email' => [
        'id' => 'email',
        'name' => 'email',
        'label' => 'Email',
        'type' => 'text',
        'regex' => 'email',
        'errorMsg' => 'Please enter a valid email.',
        'value' => '' 
    ],
    'password' => [
        'id' => 'password',
        'name' => 'password',
        'label' => 'Password',
        'type' => 'password',
        'regex' => 'none', 
        'errorMsg' => 'Password is required.',
        'value' => '' 
    ],
    'confirm_password' => [
        'id' => 'confirm_password',
        'name' => 'confirm_password',
        'label' => 'Confirm Password',
        'type' => 'password',
        'regex' => 'none',
        'errorMsg' => 'Password confirmation is required.',
        'value' => '' 
    ],
    'masterStatus' => ['error' => false] 
];

// Initialize form submission 
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $formConfig = $form->validateForm($_POST, $formConfig);

    //password length & match
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $formConfig['password']['error'] = 'Password must be at least 6 characters.';
        $formConfig['masterStatus']['error'] = true;
    }

    if ($password !== $confirmPassword) {
        $formConfig['confirm_password']['error'] = 'Passwords do not match.';
        $formConfig['masterStatus']['error'] = true;
    }

    // Check for duplicate email
    if (!$formConfig['masterStatus']['error']) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $bindings = [
            [':email', $_POST['email'], 'str']
        ];
        $result = $pdo->selectBinded($sql, $bindings);
        if (!empty($result)) {
            $formConfig['email']['error'] = 'Email already exists.';
            $formConfig['masterStatus']['error'] = true;
        }
    }

    if (!$formConfig['masterStatus']['error']) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertSql = "INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";
        $bindings = [
            [':first_name', $_POST['first_name'], 'str'],
            [':last_name', $_POST['last_name'], 'str'],
            [':email', $_POST['email'], 'str'],
            [':password', $hashedPassword, 'str']
        ];

        $insertResult = $pdo->otherBinded($insertSql, $bindings);
        if ($insertResult === 'noerror') {
            $successMsg = 'Registration successful!';
            //clearing form values
            foreach ($formConfig as $key => $element) {
                if ($key !== 'masterStatus') {
                    $formConfig[$key]['value'] = '';
                }
            }
        }
    }
}

$users = $pdo->selectNotBinded("SELECT first_name, last_name, email, password FROM users ORDER BY id DESC");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h2>User Registration Form</h2>

<?php if ($successMsg): ?>
    <div class="alert alert-success"><?= $successMsg ?></div>
<?php endif; ?>

<form method="POST" action="">
  <!-- First Name / Last Name -->
  <div class="row mb-3">
    <div class="col-md-6">
      <?php echo $form->renderInput($formConfig['first_name']); ?>
    </div>
    <div class="col-md-6">
      <?php echo $form->renderInput($formConfig['last_name']); ?>
    </div>
  </div>

  <!-- Email / Password / Confirm Password -->
  <div class="row mb-3">
    <div class="col-md-4">
      <?php echo $form->renderInput($formConfig['email']); ?>
    </div>
    <div class="col-md-4">
      <?php echo $form->renderPassword($formConfig['password']); ?>
    </div>
    <div class="col-md-4">
      <?php echo $form->renderPassword($formConfig['confirm_password']); ?>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Register</button>
</form>

    
<hr>

<h3>Registered Users</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Password</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['password']) ?></td> <!-- hashed password -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No users registered yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


</body>
</html>

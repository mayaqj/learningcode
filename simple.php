<?php
require_once('config.php');

// PONCE
$time = time();
$action = 'submit_form';
$str = sprintf('%s_%s_%s', $action, $time, PONCE_SALT);
$hash = hash('sha512', $str);

if (! empty($_POST)) {

    // Extract $_POST data to variables
    extract( $_POST );

    // check nonce
    $cal_str = sprintf('%s_%s_%s', $form_action, $timestamp, PONCE_SALT);
    $cal_hash = hash('sha512', $cal_str); 

    if ( $cal_str == $form_hash) {
        $filter_name = filter_var($name, FILTER_SANITIZE_STRING);
        $filter_email = filter_var($email, FILTER_VALIDATE_EMAIL);

        // Only submit if email is valid
        if ( $filter_email != false) {
            // send to database
            $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $stmt = $mysql->prepare("INSERT INTO users (name, email) VALUES(?,?)");
            $stmt->bind_param("ss", $filter_name, $filter_email);
            $insert = $stmt->execute();
            // close connection
            $stmt->close();
            $mysql->close();
        } else {
            $insert = false;
        }

    } else {
    $insert = false;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>HTML Form Tutorial</title>
</head>
<body>

    <?php if ( isset($insert)) : ?>
        <div class="message">
            <?php if ($insert == true) : ?>
                <p class="success">Data was inserted successfully</p>
            <?php else : ?>
                <p class="error">There was an error with the submission</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <form type="hidden" name="timestamp" value="<?php echo $time; ?>">
        <form type="hidden" name="form_action" value="<?php echo $action; ?>">
        <form type="hidden" name="form_hash" value="<?php echo $hash; ?>">
        <div class="form_field">
            <input type="text" class="text" name="name" placeholder="Enter your name" required>
        </div>
        <div class="form_field">
            <input type="email" class="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form_field">
            <button class="button">Submit</button>
        </div>
    </form>
    
</body>
</html>

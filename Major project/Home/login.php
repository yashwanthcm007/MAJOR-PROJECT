<?php

@include 'config.php';
session_start();

if(isset($_POST['submit'])){
   
    $name= mysqli_real_escape_string($conn,$_POST['name']);
    $email= mysqli_real_escape_string($conn,$_POST['email']);
    $pass= md5($_POST['password']);
    $cpass= md5($_POST['cpassword']);
    $user_type = $_POST['user_type'];

    $select="SELECT * FROM user_form WHERE email = '$email' && password='$pass'";

    $result = mysqli_query($conn, $select);

    if(mysqli_num_rows($result)>0){
        $row=mysqli_fetch_array($result);

        if($row['user_type']== 'admin'){
            $_SESSION['admin_name']=$row['name'];
            header('location:home.html');
        }
        
        elseif($row['user_type']== 'user'){
            $_SESSION['user_name']=$row['name'];
            header('location:dashboard.html');
        }
       
    }
    else{
        $error[] = 'incorrect email or password';
    }

};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--custom css link-->
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <h3>login now</h3>

        <?php
        if(isset($error)){
           foreach($error as $error){
            echo '<span class="error-msg">' .$error.'</span>';
        };
        };
        ?>

        <input type="email" name="email" required placeholder="enter your email">
        <input type="password" name="password" required placeholder="enter your password">
        <input type="submit" name="submit" value="Login" class="form-btn">
        <p>dont have an account? <a href="register.php">register</a></p>
        <p><a href="forgotpassword.php" style="margin-bottom: 15px; display: block; text-align:center;">Forgot Password?</a></p>
    </form>

</div>
 
</body>
</html>
<?php include 'components/header.php'; ?>
<title>Register to Flip It!</title>

<style>
.d-flex {
    margin: 150px auto;
}

.form {
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    padding: 2rem;
}
</style>

<body>
    <?php
        getRegister();
    ?>
    <div class="d-flex justify-content-center">
        <form class="form" action="" method="post">
            <h1 class="login-title mb-4">Registration</h1>
            <div class="mb-3">
                <input type="text" class="form-control" name="fullname" placeholder="Fullname" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
            </div>
            <p class="link">Already have an account? <a href="login.php">Click here to Login</a></p>
        </form>
    </div>

</body>

</html>
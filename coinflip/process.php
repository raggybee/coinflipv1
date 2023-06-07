<?php
    include 'connect.php';

    function getRegister() {
        global $conn;
    
        if (isset($_POST['submit'])) {
            $fullname = $_POST['fullname'];
            $username = $_POST['username'];
            $password = md5($_POST['password']);
            
            $query = "INSERT INTO `users` (`fullname`, `username`, `password`) VALUES ('$fullname', '$username', '$password')";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                echo "<script>alert('You have registered successfully. Best of luck flipping!'); window.location.href='login.php'</script>";
            } else {
                echo "<script>alert('Required fields are missing!'); window.location.href='registration.php'</script>";
            }
        }
    }

    function getLogin() {
        global $conn;
    
        if (isset($_SESSION['username'])) {
            header("Location: index.php");
            exit();
        }
    
        if (isset($_POST['submit'])) {
            $username = $_POST['username'];
            $password = md5($_POST['password']);
    
            if (empty($username) || empty($password)) {
                echo "<script>alert('Please enter a username and password.');</script>";
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $row['username'];
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<script>alert('Incorrect username or password.');</script>";
                }
            }
        }
    }

    function updateBalance() {
        global $conn;
    
        if (isset($_POST['submit'])) {
            $amount = $_POST["amount"];
            $transactionType = $_POST["transactionType"];
            $username = $_SESSION['username'];
    
            $stmt = $conn->prepare("SELECT `balance` FROM `users` WHERE `username` = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $currentBalance = $row["balance"];
    
                if ($transactionType == "cashIn") {
                    $newBalance = $currentBalance + $amount;
                } elseif ($transactionType == "cashOut") {
                    $newBalance = $currentBalance - $amount;
                }
    
                $updateStmt = $conn->prepare("UPDATE `users` SET `balance` = ? WHERE `username` = ?");
                $updateStmt->bind_param("ds", $newBalance, $username);
                if ($updateStmt->execute()) {
                    echo "<script>alert('Balance updated successfully!');</script>";
                } else {
                    echo "Error updating balance: " . $conn->error;
                }
            } else {
                echo "<script>alert('User not found.');</script>";
            }
        }
    }
?>
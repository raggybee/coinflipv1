<?php
    include 'connect.php';

    if (isset($_POST['betAmount'], $_POST['guess'], $_POST['result'])) {
        $betAmount = $_POST['betAmount'];
        $guess = $_POST['guess'];
        $result = $_POST['result'];

        session_start();
        $username = $_SESSION['username'];

        $stmt_user = $conn->prepare("SELECT User_ID, balance FROM users WHERE username = ?");
        $stmt_user->bind_param("s", $username);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        $user_data = $user_result->fetch_assoc();
        $user_id = $user_data['User_ID'];
        $balance = $user_data['balance'];
        $stmt_user->close();

        $stmt_history = $conn->prepare("
            INSERT INTO match_history (user_id, date, bet_amount, guess, result)
            VALUES (?, NOW(), ?, ?, ?)"
        );
        $stmt_history->bind_param("ssss", $user_id, $betAmount, $guess, $result);

        $stmt_balance = $conn->prepare("
            UPDATE users
            SET balance = CASE WHEN ? = 'Win' THEN balance + ? ELSE balance - ? END
            WHERE username = ?"
        );
        $stmt_balance->bind_param("ssss", $result, $betAmount, $betAmount, $username);

        $conn->begin_transaction();
        $response = array();

        if ($stmt_history->execute() && $stmt_balance->execute()) {
            $conn->commit();
            $response = array(
                'success' => true,
                'balance' => $balance
            );
        } else {
            $conn->rollback();
            $response = array(
                'success' => false,
                'message' => "Error: " . $stmt_history->error
            );
        }

        echo json_encode($response);
    } else {
        $response = array(
            'success' => false,
            'message' => "Error: Incomplete data"
        );
        echo json_encode($response);
    }
?>
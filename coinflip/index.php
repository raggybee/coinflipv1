<?php
include 'components/header.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$query = "SELECT fullname, balance FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
    $balance = $row['balance'];
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

<title>Flip It! Dashboard</title>

<style>
.container {
    margin: 250px auto;
}
</style>

<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h1 class="card-title">Welcome back, <?php echo isset($fullname) ? $fullname : ''; ?>!</h1>
                        <p class="card-text">Your current balance is: <?php echo isset($balance) ? $balance : 0; ?></p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#playModal">Play Flip It!</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#matchHistoryModal">Match History</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#cashModal">Cash In/Out</button>
                        <p><a href="logout.php">Logout</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
      updateBalance();
    ?>

    <!-- Cash In/Out Modal -->
    <div class="modal fade" id="cashModal" tabindex="-1" aria-labelledby="cashModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cashModalLabel">Cash In/ Cash Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="amount" class="mb-1">Amount:</label>
                            <input type="number" class="form-control" id="amount" name="amount"
                                placeholder="Enter amount" required>
                        </div>
                        <div class="form-group mt-2">
                            <label for="transactionType" class="mb-1">Transaction Type:</label>
                            <select class="form-control" id="transactionType" name="transactionType" required>
                                <option value="cashIn">Cash In</option>
                                <option value="cashOut">Cash Out</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Match History Modal -->
    <div class="modal fade" id="matchHistoryModal" tabindex="-1" aria-labelledby="matchHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="matchHistoryModalLabel">Match History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bet Amount</th>
                                <th>Guess</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $user_id = $_SESSION['username'];

                                $stmt = $conn->prepare("SELECT mh.date, mh.bet_amount, mh.guess, mh.result FROM match_history mh INNER JOIN users u ON mh.user_id = u.User_ID WHERE u.username = ?");
                                $stmt->bind_param("s", $user_id);
                                $stmt->execute();
                                $results = $stmt->get_result();

                                while ($row = $results->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['bet_amount']; ?></td>
                                <td><?php echo $row['guess']; ?></td>
                                <td><?php echo $row['result']; ?></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Play Modal -->
    <div class="modal fade" id="playModal" tabindex="-1" aria-labelledby="playModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="playModalLabel">Play Flip It!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="form-group mb-1">
                            <label for="balance" class="mb-1">Balance:</label>
                            <input type="number" class="form-control bet" id="balance" name="balance"
                                value="<?= $balance; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="betAmount" class="mb-1">Bet Amount:</label>
                            <input type="number" class="form-control bet" id="betAmount" name="betAmount"
                                placeholder="Enter bet amount" required>
                        </div>
                        <div class="form-group mt-2">
                            <label for="guess" class="mb-1">Guess:</label>
                            <select class="form-control" id="guess" name="guess" required>
                                <option value="heads">Heads</option>
                                <option value="tails">Tails</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="results" class="mb-1">Results:</label>
                            <input type="text" class="form-control bet" id="results" name="results"
                                placeholder="Here is your Results">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name="play" onclick="playGame()">Play</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this code to your JavaScript file or script tag -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const playButton = document.querySelector("button[name='play']");
        playButton.addEventListener("click", playGame);

        function playGame() {
            const betAmountInput = document.getElementById("betAmount");
            const guessSelect = document.getElementById("guess");
            const resultsInput = document.getElementById("results");
            const balanceInput = document.getElementById("balance");

            const betAmount = parseFloat(betAmountInput.value);
            const guess = guessSelect.value;
            const balance = parseFloat(balanceInput.value);

            resultsInput.value = "Loading...";

            const randomResult = Math.random() < 0.5 ? "Win" : "Lost";

            setTimeout(() => {
                resultsInput.value = randomResult;
                saveHistory(balance, betAmount, guess, randomResult);
                updateBalance(balanceInput, betAmount, randomResult);
            }, 2000);
        }

        function saveHistory(balance, betAmount, guess, result) {
            const xhr = new XMLHttpRequest();
            const url = "save_history.php";
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        updateBalance(response.balance);
                    } else {
                        console.log("Error: " + response.message);
                    }
                }
            };

            const formData = new URLSearchParams();
            formData.append("balance", balance);
            formData.append("betAmount", betAmount);
            formData.append("guess", guess);
            formData.append("result", result);

            xhr.send(formData.toString());
        }

        function updateBalance(balanceInput, betAmount, result) {
            const balance = parseFloat(balanceInput.value);

            if (result === "Win") {
                balanceInput.value = balance + betAmount;
            } else if (result === "Lost") {
                balanceInput.value = balance - betAmount;
            }
        }
    });
    </script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

</html>
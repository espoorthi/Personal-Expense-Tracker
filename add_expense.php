<?php
include("session.php"); // must contain $con and $userid

$update = false;
$del = false;
$expenseamount = "";
$expensedate = date("Y-m-d");
$expensecategory = "";

/* ================== ADD EXPENSE ================== */
if (isset($_POST['add'])) {

    $expenseamount = floatval($_POST['expenseamount']);
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];

    // Basic validation
    if ($expenseamount <= 0) {
        die("Invalid expense amount");
    }

    if ($expensedate > date("Y-m-d")) {
        die("Invalid expense date");
    }

    $stmt = $con->prepare("INSERT INTO expenses (user_id, expense, expensedate, expensecategory) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $userid, $expenseamount, $expensedate, $expensecategory);

    if ($stmt->execute()) {
        header("Location: add_expense.php");
        exit();
    } else {
        die("Error adding expense");
    }
}

/* ================== UPDATE EXPENSE ================== */
if (isset($_POST['update'])) {

    $id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $expenseamount = floatval($_POST['expenseamount']);
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];

    $stmt = $con->prepare("UPDATE expenses SET expense=?, expensedate=?, expensecategory=? WHERE user_id=? AND expense_id=?");
    $stmt->bind_param("dssii", $expenseamount, $expensedate, $expensecategory, $userid, $id);

    if ($stmt->execute()) {
        header("Location: manage_expense.php");
        exit();
    } else {
        die("Error updating expense");
    }
}

/* ================== DELETE EXPENSE ================== */
if (isset($_POST['delete'])) {

    $id = isset($_GET['delete']) ? intval($_GET['delete']) : 0;

    $stmt = $con->prepare("DELETE FROM expenses WHERE user_id=? AND expense_id=?");
    $stmt->bind_param("ii", $userid, $id);

    if ($stmt->execute()) {
        header("Location: manage_expense.php");
        exit();
    } else {
        die("Error deleting expense");
    }
}

/* ================== FETCH FOR EDIT ================== */
if (isset($_GET['edit'])) {

    $id = intval($_GET['edit']);
    $update = true;

    $stmt = $con->prepare("SELECT expense, expensedate, expensecategory FROM expenses WHERE user_id=? AND expense_id=?");
    $stmt->bind_param("ii", $userid, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $expenseamount = $row['expense'];
        $expensedate = $row['expensedate'];
        $expensecategory = $row['expensecategory'];
    } else {
        die("Unauthorized access attempt");
    }
}

/* ================== FETCH FOR DELETE ================== */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);
    $del = true;

    $stmt = $con->prepare("SELECT expense, expensedate, expensecategory FROM expenses WHERE user_id=? AND expense_id=?");
    $stmt->bind_param("ii", $userid, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $expenseamount = $row['expense'];
        $expensedate = $row['expensedate'];
        $expensecategory = $row['expensecategory'];
    } else {
        die("Unauthorized access attempt");
    }
}
?>

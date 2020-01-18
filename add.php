<?php
require_once "pdo.php";
session_start();

if ( isset($_SESSION['name'])) {
  $name = $_SESSION['name'];
} else {
  die('Not logged in.');
}

// If any error recorded in session, show once, then reset.
if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
}

if ( isset($_POST['make']) && isset($_POST['model']) &&
      isset($_POST['year']) && isset($_POST['mileage'])) {

  if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
      strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add.php");
        return;
  }

  if (is_numeric($_POST['year']) && is_numeric($_POST['mileage'])) {
      $sql = "INSERT INTO autos (make, model, year, mileage)
                VALUES (:make, :model, :year, :mileage)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          ':make' => $_POST['make'],
          ':model' => $_POST['model'],
          ':year' => $_POST['year'],
          ':mileage' => $_POST['mileage']));
          // now redirect to view.php
          $_SESSION['success'] = "New Auto added";
          header('Location: index.php');
          return;
    } else {
      $_SESSION['error'] = "Mileage and year must be numeric.";
      header("Location: add.php");
      return;
    }
}

?>
<html>
<head>
<title>Kelly Loyd's Profile Add</title>
</head><body>
  <?php echo("<h1>Adding Profile for $name</h1>\n"); ?>
<p>Add A New Auto</p>
<form method="post">
  <p>Make:
  <input type="text" name="make" size="40"></p>
  <p>Model:
  <input type="text" name="model" size="40"></p>
  <p>Year:
  <input type="text" name="year"></p>
  <p>Mileage:
  <input type="text" name="mileage"></p>
  <p>
    <input type="submit" value="Add New" name="addnew" />
    <a href="index.php">Cancel</a>
  </p>
</form>


</body>

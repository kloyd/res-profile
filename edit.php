<?php
require_once "pdo.php";
session_start();

if ( isset($_SESSION['name'])) {
  $name = $_SESSION['name'];
} else {
  die('Not logged in');
}

if ( isset($_POST['make']) && isset($_POST['model']) &&
      isset($_POST['year']) && isset($_POST['mileage'])) {

  if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
      strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: edit.php?auto_id=".$_POST['auto_id']);
        return;
  }

  if (is_numeric($_POST['year']) && is_numeric($_POST['mileage'])) {
      $sql = "update autos
                set make = :make, model = :model,
                year = :year, mileage = :mileage
                where auto_id = :auto_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          ':make' => $_POST['make'],
          ':model' => $_POST['model'],
          ':year' => $_POST['year'],
          ':mileage' => $_POST['mileage'],
          ':auto_id' => $_POST['auto_id']));
          // now redirect to index.php
          $_SESSION['success'] = "Auto updated";
          header('Location: index.php');
          return;
    } else {
      $_SESSION['error'] = "Mileage and year must be numeric.";
      header("Location: edit.php?auto_id=".$_POST['auto_id']);
      return;
    }
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['auto_id']) ) {
  $_SESSION['error'] = "Missing auto_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where auto_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['auto_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for auto_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$mk = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$yr = $row['year'];
$mi = $row['mileage'];
$auto_id = $row['auto_id'];
?>
<p>Edit Auto</p>
<form method="post">
  <p>Make:
  <input type="text" name="make" size="40" value="<?= $mk ?>"></p>
  <p>Model:
  <input type="text" name="model" size="40" value="<?= $mo ?>"></p>
  <p>Year:
  <input type="text" name="year" value="<?= $yr ?>"></p>
  <p>Mileage:
  <input type="text" name="mileage" value="<?= $mi ?>"></p>
  <input type="hidden" name="auto_id" value="<?= $auto_id ?>">
  <p>
    <input type="submit" value="Save" />
    <a href="index.php">Cancel</a>
  </p>
</form>


</body>

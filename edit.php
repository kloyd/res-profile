<?php
require_once "pdo.php";
session_start();

if ( isset($_SESSION['name'])) {
  $name = $_SESSION['name'];
} else {
  die('Not logged in');
}

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline'])
    && isset($_POST['summary'])) {

  if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
      strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
      || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
  }

  if (strpos($_POST['email'], '@') !== false) {
      $sql = "update profile
                set first_name = :fn, last_name = :ln,
                email = :em, headline = :he, summary = :su
                where profile_id = :profile_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));
          // now redirect to index.php
          $_SESSION['success'] = "Profile updated";
          header('Location: index.php');
          return;
    } else {
      $_SESSION['error'] = "email requires @ sign.";
      header("Location: edit.php?profile_id=".$_POST['profile_id']);
      return;
    }
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>

<html>
<head>
<title>Kelly Loyd's Profile Edit</title>
</head>
<body>
  <div class="container">
  <?php echo("<h1>Editing Profile for $name</h1>\n"); ?>
  <form method="post">
  <p>First Name:
  <input type="text" name="first_name" size="60" value="<?= $fn ?>"/></p>
  <p>Last Name:
  <input type="text" name="last_name" size="60" value="<?= $ln ?>"/></p>
  <p>Email:
  <input type="text" name="email" size="30" value="<?= $em ?>"/></p>
  <p>Headline:<br/>
  <input type="text" name="headline" size="80" value="<?= $he ?>"/></p>
  <p>Summary:<br/>
  <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea>
  <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
  <p>
    <input type="submit" value="Save" />
    <a href="index.php">Cancel</a>
  </p>
</form>


</body>
</html>

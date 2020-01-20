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

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline'])
    && isset($_POST['summary'])) {

  if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
      strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
      || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add.php");
        return;
  }

  if (strpos($_POST['email'], '@') !== false) {
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');

    $stmt->execute(array(
      ':uid' => $_SESSION['user_id'],
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':he' => $_POST['headline'],
      ':su' => $_POST['summary'])
    );
    // now redirect to view.php
    $_SESSION['success'] = "New Profile added";
    header('Location: index.php');
    return;
  } else {
    $_SESSION['error'] = "email requires @ sign.";
    header("Location: add.php");
    return;
  }
}

?>
<html>
<head>
<title>Kelly Loyd's Profile Add</title>
</head>
<body>
  <div class="container">
  <?php echo("<h1>Adding Profile for $name</h1>\n"); ?>
  <form method="post">
  <p>First Name:
  <input type="text" name="first_name" size="60"/></p>
  <p>Last Name:
  <input type="text" name="last_name" size="60"/></p>
  <p>Email:
  <input type="text" name="email" size="30"/></p>
  <p>Headline:<br/>
  <input type="text" name="headline" size="80"/></p>
  <p>Summary:<br/>
  <textarea name="summary" rows="8" cols="80"></textarea>
  <p>
  <input type="submit" value="Add">
  <input type="submit" name="cancel" value="Cancel">
  </p>
</form>


</body>

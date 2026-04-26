<?php
  session_start();
  if (!isset($_SESSION['day'])) {
    $_SESSION['day'] = 0;
  }

  if (!isset($_SESSION['last_visit']) || time() - $_SESSION['last_visit'] > 60*60*24) {
    $_SESSION['day'] = $_SESSION['day'] + 1;
  }
?>

<h1>Visit this page 100 days in a row and get the flag</h1>

<?php 
if ($_SESSION['day'] >= 100) {
?>
<h2>Your flag is <?= getenv("FLAG") ?></h2>
<?php } else {  ?>
<h2><?= 100 - $_SESSION['day'] ?> remains</h2>
<?php
}

$_SESSION['last_visit'] = time();
?>
 

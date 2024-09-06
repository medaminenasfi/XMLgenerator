

<?php
$dsn = 'mysql:host=localhost;dbname=declaration';
$username = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Connexion réussie à la base de données';
} catch (PDOException $e) {
    echo 'La connexion a échoué : ' . $e->getMessage();
}
?>
     





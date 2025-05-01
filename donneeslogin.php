<?php
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header('Location: ../formulairelogin.html?msg=Email%20et%20mot%20de%20passe%20requis');
    exit();
}

include_once 'configDB.php';
$dsn = 'mysql:host=' . SERVER . ';dbname=' . BASE;

try {
    $connexion = new PDO($dsn, USER, PASSWD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header('Location: ../formulairelogin.html?msg=Erreur%20de%20connexion%20à%20la%20base%20de%20données');
    exit();
}
// Récupération des données du formulaire
$email = $_POST['email'];
$mot_de_passe = $_POST['password'];

// Requête pour retrouver l'utilisateur
$sql = "SELECT password, firstname, name FROM user WHERE email = :email";
$stmt = $connexion->prepare($sql);
$stmt->execute([':email' => $email]);
$utilisateur = $stmt->fetch();

if ($utilisateur && ($mot_de_passe== $utilisateur['password'])) {
    echo "Connexion réussie. Bienvenue, " . htmlspecialchars($utilisateur['firstname']) . " " . htmlspecialchars($utilisateur['name']) . " !";
} else {
    echo "Email ou mot de passe incorrect.";
}
?>
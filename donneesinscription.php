<?php
if (!isset($_POST['firstname']) || !isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['password_confirm'])) {
    header('Location: ../formulaireinscription.html?msg=Tous%20les%20champs%20sont%20requis');
    exit();
}

// Vérification que les mots de passe correspondent
if ($_POST['password'] !== $_POST['password_confirm']) {
    header('Location: ../formulaireinscription.html?msg=Les%20mots%20de%20passe%20ne%20correspondent%20pas');
    exit();
}

include_once 'configDB.php';
$dsn = 'mysql:host=' . SERVER . ';dbname=' . BASE;

try {
    $connexion = new PDO($dsn, USER, PASSWD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header('Location: ../formulaireinscription.html?msg=Erreur%20de%20connexion%20à%20la%20base%20de%20données');
    exit();
}

// Récupération des données du formulaire
$firstname = htmlspecialchars($_POST['firstname']);
$name = htmlspecialchars($_POST['name']);
$phone = htmlspecialchars($_POST['phone']);
$email = htmlspecialchars($_POST['email']);
$password = $_POST['password']; // Idéalement, à hasher avec password_hash()

// Vérifier si l'email existe déjà
$check_email = "SELECT COUNT(*) FROM user WHERE email = :email";
$stmt = $connexion->prepare($check_email);
$stmt->execute([':email' => $email]);
$email_existe = $stmt->fetchColumn();

if ($email_existe > 0) {
    header('Location: ../formulaireinscription.html?msg=Cet%20email%20est%20déjà%20utilisé');
    exit();
}

// Hasher le mot de passe (recommandé pour la sécurité)
// $password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertion des données dans la base
$sql = "INSERT INTO user (firstname, name, phone, email, password) VALUES (:firstname, :name, :phone, :email, :password)";
$stmt = $connexion->prepare($sql);
$success = $stmt->execute([
    ':firstname' => $firstname,
    ':name' => $name,
    ':phone' => $phone,
    ':email' => $email,
    ':password' => $password  // Idéalement, utiliser $password_hash à la place
]);

if ($success) {
    header('Location: ../formulairelogin.html?msg=Inscription%20réussie.%20Vous%20pouvez%20maintenant%20vous%20connecter.');
} else {
    header('Location: ../formulaireinscription.html?msg=Erreur%20lors%20de%20l\'inscription');
}
?>
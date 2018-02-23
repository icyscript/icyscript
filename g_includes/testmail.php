<?php

// Get the actions
require_once('admin.scripts.inc.php');
$fanlisting->LastChecked();

    ini_set( 'display_errors', 1 );


    $from = "rafael.dossantos.lurcat@gmail.com";

    $to = $member->mail;

    $subject = "Vérification PHP mail";

    $message = "PHP mail marche";

    $headers = "From:" . $from;

    mail($to,$subject,$message, $headers);

    echo "L'email a été envoyé.";
?>
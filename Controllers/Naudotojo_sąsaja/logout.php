<?php
session_start(); // Pradeda sesiją, kad galėtume ją nutraukti

// Išvalo visas sesijos reikšmes
session_unset();

// Sunaikina sesiją
session_destroy();

// Nukreipia vartotoją į prisijungimo puslapį
header("Location: ../../Sablonai/prisijungti.html");
exit; // Užtikrina, kad tolesnis kodas nebūtų vykdomas
?>

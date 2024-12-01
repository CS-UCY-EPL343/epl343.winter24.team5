<?php
require 'db_functions.php'; // Το αρχείο που περιέχει τη συνάρτηση notifyUserForPoll

// Dummy data για δοκιμή
$voterId = 5; // Αντικατάστησε με έγκυρο User_ID από τη βάση
$pollId = 1; // Αντικατάστησε με έγκυρο Poll_ID από τη βάση

// Κλήση της συνάρτησης για δοκιμή
$result = notifyUserForPoll($voterId, $pollId);
echo $result;

<?php
require 'iet/PDODB.php';

  try {
        $dbh = new PDODB('admissions-feed');
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br />";
        die();
    }
?>
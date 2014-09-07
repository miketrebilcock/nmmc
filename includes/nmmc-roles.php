<?php

defined('ABSPATH') or die("No script kiddies please!");

///////////////////////////////////////////
// Register NMMC User Roles
///////////////////////////////////////////


add_role('curator', 'Curator', array(
    'read'=> true
     ));
     
add_role('nmmc_admin', 'NMMC Administrator', array(
    'read'=> true
     ));

add_role('marketing', 'Marketing', array(
    'read'=> true
     ));


?>
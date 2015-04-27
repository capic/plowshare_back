<?php
define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "capic_20_04_1982");
define("DB_NAME", "plowshare");
define("DB_DNS", 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);

define("DOWNLOAD_STATUS_WAITING", "1");
define("DOWNLOAD_STATUS_IN_PROGRESS", "2");
define("DOWNLOAD_STATUS_FINISHED", "3");
define("DOWNLOAD_STATUS_ERROR", "4");
define("DOWNLOAD_STATUS_PAUSE", "5");
define("DOWNLOAD_STATUS_CANCEL", "6");
define("DOWNLOAD_STATUS_UNDEFINED", "7");
define("DOWNLOAD_STATUS_STARTING", "8");

define("LINK_STATUS_ON_LINE", "1");
define("LINK_STATUS_DELETED", "2");

define("DIRECTORY_TELECHARGEMENT_TEXT", "/mnt/HD/HD_a2/telechargement/telechargements_texte/");
define("DIRECTORY_TELECHARGEMENT_LOG", "/mnt/HD/HD_a2/telechargement/temp_plowdown/log/");
define("DIRECTORY_TELECHARGEMENT_DESTINATION", "/mnt/HD/HD_a2/telechargement/");
define("DIRECTORY_TELECHARGEMENT_DESTINATION_TEMP", "/mnt/HD/HD_a2/telechargement/temp_plowdown/");
?>
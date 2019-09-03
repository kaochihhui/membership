<?php

// Debug
$GLOBALS['log'] = false;

// Update info
$GLOBALS['app_version_number'] = 1.0;
$GLOBALS['force_update_date'] = "1000-01-01"; 

// Level requirements
$GLOBALS['level1_days'] = 5;
$GLOBALS['level2_days'] = 15;
$GLOBALS['level3_days'] = 66;

// Multiplier addition per level
$GLOBALS['level1_multiplier'] = 0.5;
$GLOBALS['level2_multiplier'] = 1.0;
$GLOBALS['level3_multiplier'] = 1.5;

// Multiplier partner related
$GLOBALS['having_partner'] = 0.1;
$GLOBALS['partner_1day_multiplier'] = 0.2;
$GLOBALS['partner_2day_multiplier'] = 0.3;
$GLOBALS['partner_3day_multiplier'] = 0.4;
$GLOBALS['partner_4day_multiplier'] = 0.5;

$GLOBALS['points_per_activity'] = 10;
$GLOBALS['points_per_daily'] = 10;
$GLOBALS['points_per_learn'] = 100;

$GLOBALS['first_mama_daily_video_id'] = 10000;

$GLOBALS['weeks_to_remind'] = 2;
$GLOBALS['days_to_unsubscribe'] = 30;

// TODO: Replace these data to use assets on our hosting instead
$GLOBALS['default_picture_url'] = "https://mamadiario.com/pictures/default_avatar.png";
$GLOBALS['default_news_icon_url'] = "https://mamadiario.com/activity_icons/icons8-idea-64.png";
$GLOBALS['default_news_title'] = "¡Bienvenida a Mamá Diario!";
$GLOBALS['default_news_description'] = "Para saber más como funciona esta app, haz click en la imagen de la derecha";
$GLOBALS['default_news_url'] = "";
$GLOBALS['default_daily_video_icon_url'] = "https://img.icons8.com/cotton/2x/cafe.png";
$GLOBALS['default_daily_video_url'] = "";
$GLOBALS['default_daily_video_title'] = "Mamá Diario: Cápsula de Hoy";
$GLOBALS['default_daily_video_description'] = "¡Aprende Nuevos Tips Cada Día!";
$GLOBALS['default_learning_video_id'] = -1;
$GLOBALS['default_learning_video_icon_url'] = "http://www.iconninja.com/files/275/334/495/book-blank-book-icon.png";
$GLOBALS['default_learning_video_url'] = "";
$GLOBALS['default_learning_video_title'] = "Tu Programa Mamá Diario";
$GLOBALS['default_learning_video_description'] = "¡Ve ahora al Siguiente Módulo de Aprendizaje!";

$GLOBALS['profile_url'] = 'https://mamadiario.com/profile.php';
$GLOBALS['salt'] = '$P$CaPwOfWd1';

?>
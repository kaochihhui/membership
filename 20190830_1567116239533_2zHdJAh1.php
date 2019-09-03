<?php
include('./api/conn.php');
session_start();

$Name = "Diego";
$Progress = 42;
$Percentage = 78;
$CommunityProgress = 30310;
$Goal = 100000;
$ReportedVideo = false;
$ReportedHabit = "";
$InARow = 10;

// FIRST WEEK
// $webinar_url_base = "https://webinar.mamadiario.com/registro31019613";
// $webinar_url_base = "https://webinar.mamadiario.com/inscribirme-con-descuento";
// $webinar_url_base = "https://webinar.mamadiario.com/programa-de-mama-diario31450231";
$webinar_url_base = "https://webinar.mamadiario.com/curso_montessori";

$only_subscribers_before = date('Y-m-d', strtotime("05-08-2019"));

$webpage = explode('.',basename($_SERVER['PHP_SELF']))[0];
$_SESSION['url'] = $_SERVER['REQUEST_URI'];
$d1 = date('Y-m-d', strtotime('0 days'));
$d2 = date('Y-m-d', strtotime('-1 days'));
$d3 = date('Y-m-d', strtotime('-2 days'));
$dt1 = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d1'"); // Safe
$dt2 = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d2'"); // Safe
$dt3 = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d3'"); // Safe

if ($dt1->num_rows==0) {
    $m1 = "d-none";
    if ($dt2->num_rows==0) {
        $m2 = "d-none";
        if ($dt3->num_rows==0) {
            $m3 = "d-none";
        } else {
            $m3 = "";
        }    
    } else {
        $m2 = "";
        if($dt3->num_rows==0) {
            $m3 = "d-none";         
        
        } else {
            $m3 = "";
        }
    }
} else {
    $m1 = "";
    if ($dt2->num_rows==0) {
        $m2 = "d-none";
        if ($dt3->num_rows==0) {
            $m3 = "d-none";
        } else {
            $m3 = "";
        }
    } else {
        $m2 = "";
        if ($dt3->num_rows==0) {
            $m3 = "d-none";
        } else {
            $m3 = "";
        }
    }
}

$today_webpage = "";
$sql = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d1'"); // Safe
while ($data = $sql->fetch_array()) {
    $mama_vframe_id = $data['mama_vframe_id'];
    $today_webpage = $data['webpage'] . ".php";
//    header("Location: /" . $yesterday_webpage . ".php");
}

$yesterday_webpage = "";
$sql = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d2'"); // Safe
while ($data = $sql->fetch_array()) {
    $mama_vframe_id = $data['mama_vframe_id'];
    $yesterday_webpage = $data['webpage'] . ".php";
//    header("Location: /" . $yesterday_webpage . ".php");
}

$two_days_ago_webpage = "";
$sql = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE date = '$d3'"); // Safe
while($data = $sql->fetch_array()) {
    $mama_vframe_id = $data['mama_vframe_id'];
    $two_days_ago_webpage = $data['webpage'] . ".php";
//    header("Location: /" . $webpage . ".php");
}

$webpage_path = $webpage . ".php";
$this_webpage = "";
$date = date('Y-m-d', strtotime('0 days'));
if ($webpage_path == $yesterday_webpage) {
//if (isset($_POST['ayer'])) {
//    $date = date('Y-m-d', strtotime('-1 days'));  
    $b1 = "btngreen";
    $b2 = "btngreen-outline";
    $b3 = "btngreen";
    $this_webpage = $yesterday_webpage;
}
else if ($webpage_path == $two_days_ago_webpage) {
//else if (isset($_POST['ant'])) {
//    $date = date('Y-m-d', strtotime('-2 days'));
    $b1 = "btngreen";
    $b2 = "btngreen";
    $b3 = "btngreen-outline";
    $this_webpage = $two_days_ago_webpage;
}
else {
//    $_POST['today'] = '';
    $b1 = "btngreen-outline";
    $b2 = "btngreen";
    $b3 = "btngreen";
    $this_webpage = $today_webpage;
    
    $rows = sqls_video_with_webpage($conn, $webpage);
//    $sql = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE webpage = '$webpage'");
    // while ($data = $sql->fetch_array()) {
    //     $mama_vframe_id = $data['mama_vframe_id'];
    //     $date = $data['date'];
    // }
    if (count($rows) > 0)
    {
        $mama_vframe_id = $rows[0]['mama_vframe_id'];
        $date = $rows[0]['date'];
    }
}

$watched = isset($_GET['watched']) ? $_GET['watched'] : false;
$user_email = isset($_GET['email']) ? str_replace(' ', '+', $_GET['email']) : false;
$show_offer = false;

if ($user_email)
{
    $rows = sqls_user_all($conn, $user_email);
    if (count($rows) > 0) 
    {
        $user_name = $rows[0]['name'];
 
        // For the Funnel
        $webinar_url = $webinar_url_base."?merge_name=".urlencode($user_name)."&merge_email=".$user_email;
        // setcookie("merge_name", $user_name, time() + (86400 * 15), "https://webinar.mamadiario.com/"); 
        // setcookie("merge_email", $user_email, time() + (86400 * 15), "https://webinar.mamadiario.com/");

        // For the Website
        setcookie("merge_name", urlencode($user_name), time() + (86400 * 15), '/'); 
        setcookie("merge_email", $user_email, time() + (86400 * 15), '/');

        if ($watched)
        {
            int_sqlu_mark_daily_video_as_accessed($conn, $user_email, $mama_vframe_id);
            $today_webpage = $today_webpage . '?email='.$user_email.'&watched=1';
            $yesterday_webpage = $yesterday_webpage . '?email='.$user_email.'&watched=1';
            $two_days_ago_webpage = $two_days_ago_webpage . '?email='.$user_email.'&watched=1';
        //    $update_query = $conn->query("UPDATE `tbl_user_score_detail` SET `earned` = '-1' WHERE `email` = '".$user_email."' AND `mama_diario_id` = '".$mama_vframe_id."' ");
        }

        $subscription_date = date('Y-m-d', strtotime($rows[0]['subscription_date']));

// FIRST WEEK        
//        $show_offer = (is_null($rows[0]['membership_date']) && ($subscription_date < $only_subscribers_before));

// SECOND WEEK
        // $watched_webinar = int_sqls_offer_201908_status($conn, $user_email);
        // if (count($watched_webinar) > 0) 
        // {
        //     $show_offer = is_null($rows[0]['membership_date']);
        // }
        //$show_offer = is_null($rows[0]['membership_date']);
        $show_offer = true;
    }
}

if (isset($_SESSION['email']))
{
    $loginEmail = $_SESSION['email'];
    $rows = sqls_user_resource_detail($conn, $loginEmail, $mama_vframe_id);
    // $sql1 = $conn->query("SELECT * FROM `tbl_user_resources` WHERE email = '" . $loginEmail . "' AND resource_id ='".$mama_vframe_id."'");
    // while ($data = $sql1->fetch_array()) {
    //     $is_member = 1;
    // }
    if (count($rows) > 0) {
        $is_member = 1;
    }
}
else if (isset($_GET['own']))
{
    header('location: login.php');
}

$is_video_unlocked = (time()-strtotime($date) <= 259200);

if ($is_video_unlocked || $is_member)
{
    $rows = sqls_video_with_webpage($conn, $webpage);
    if (count($rows) > 0) 
    {
        $url = $rows[0]['mama_url'];
        $des = $rows[0]['mama_description'];
        $thumb = $rows[0]['mama_vframe_url'];
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB">

<head>
     <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta property="og:title" content="TIPS de Mamá Diario">
    <meta property="og:description" content="<?php echo $des ?>">
    <meta property="og:image" content="<?php echo $thumb ?>">
    <meta property="og:url" content="https://mamadiario.com/<?php echo $this_webpage ?>">

	<title><?php echo $des ?></title>
    <link rel="icon" href="img/mama_fav.ico" type="image/x-icon" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5d13be434c33a40012996f4f&product=inline-share-buttons' async='async'></script>

	<style media="screen" type="text/css">
    html,
    <?php if ($show_offer) { ?>
    .topbar{
        height: 100px;
        margin-top: 0px;
        margin-bottom: 0px;
    }    
    <?php } ?>
	body {
		margin:0;
		padding:0;
		height:100%;
	}
	#container {
		min-height:100%;
		position:relative;
	}
	.context {
    width: 100%;
    position: absolute;
    top:50vh;
    
}

.context h1{
    text-align: center;
    color: #fff;
    font-size: 50px;
}


.area{
    background: #4e54c8;  
    background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);  
    width: 100%;
    z-index: -10;
   
}

.circles{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.circles li{
    position: absolute;
    display: block;
    list-style: none;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.2);
    animation: animate 25s linear infinite;
    bottom: -150px;
    
}

.circles li:nth-child(1){ left: 10%; width: 17px; height: 62px; animation-delay: 0s; animation-duration: 6s; }
.circles li:nth-child(2){ left: 13%; width: 133px; height: 31px; animation-delay: 0s; animation-duration: 10s; }
.circles li:nth-child(3){ left: 16%; width: 107px; height: 17px; animation-delay: 4s; animation-duration: 6s; }
.circles li:nth-child(4){ left: 19%; width: 75px; height: 138px; animation-delay: 3s; animation-duration: 5s; }
.circles li:nth-child(5){ left: 22%; width: 28px; height: 148px; animation-delay: 0s; animation-duration: 7s; }
.circles li:nth-child(6){ left: 25%; width: 88px; height: 132px; animation-delay: 0s; animation-duration: 12s; }
.circles li:nth-child(7){ left: 28%; width: 42px; height: 134px; animation-delay: 0s; animation-duration: 12s; }
.circles li:nth-child(8){ left: 31%; width: 119px; height: 119px; animation-delay: 2s; animation-duration: 11s; }
.circles li:nth-child(9){ left: 34%; width: 99px; height: 50px; animation-delay: 4s; animation-duration: 5s; }
.circles li:nth-child(10){ left: 37%; width: 15px; height: 94px; animation-delay: 5s; animation-duration: 6s; }
.circles li:nth-child(11){ left: 40%; width: 91px; height: 21px; animation-delay: 1s; animation-duration: 7s; }
.circles li:nth-child(12){ left: 43%; width: 149px; height: 143px; animation-delay: 3s; animation-duration: 13s; }
.circles li:nth-child(13){ left: 46%; width: 107px; height: 89px; animation-delay: 4s; animation-duration: 10s; }
.circles li:nth-child(14){ left: 49%; width: 133px; height: 70px; animation-delay: 0s; animation-duration: 8s; }
.circles li:nth-child(15){ left: 52%; width: 19px; height: 74px; animation-delay: 1s; animation-duration: 15s; }
.circles li:nth-child(16){ left: 55%; width: 20px; height: 49px; animation-delay: 0s; animation-duration: 12s; }
.circles li:nth-child(17){ left: 58%; width: 142px; height: 70px; animation-delay: 4s; animation-duration: 6s; }
.circles li:nth-child(18){ left: 61%; width: 98px; height: 42px; animation-delay: 3s; animation-duration: 12s; }
.circles li:nth-child(19){ left: 64%; width: 118px; height: 107px; animation-delay: 4s; animation-duration: 13s; }
.circles li:nth-child(20){ left: 67%; width: 108px; height: 36px; animation-delay: 4s; animation-duration: 9s; }
.circles li:nth-child(21){ left: 70%; width: 81px; height: 67px; animation-delay: 5s; animation-duration: 7s; }
.circles li:nth-child(22){ left: 73%; width: 128px; height: 77px; animation-delay: 0s; animation-duration: 13s; }
.circles li:nth-child(23){ left: 76%; width: 63px; height: 31px; animation-delay: 2s; animation-duration: 6s; }
.circles li:nth-child(24){ left: 79%; width: 38px; height: 88px; animation-delay: 3s; animation-duration: 8s; }
.circles li:nth-child(25){ left: 82%; width: 17px; height: 147px; animation-delay: 5s; animation-duration: 12s; }
.circles li:nth-child(26){ left: 85%; width: 46px; height: 57px; animation-delay: 2s; animation-duration: 15s; }
.circles li:nth-child(27){ left: 88%; width: 67px; height: 118px; animation-delay: 3s; animation-duration: 15s; }
.circles li:nth-child(28){ left: 91%; width: 60px; height: 124px; animation-delay: 3s; animation-duration: 14s; }
.circles li:nth-child(29){ left: 94%; width: 75px; height: 78px; animation-delay: 0s; animation-duration: 11s; }
.circles li:nth-child(30){ left: 97%; width: 60px; height: 129px; animation-delay: 1s; animation-duration: 7s; }

@keyframes animate {

    0%{
        transform: translateY(0) rotate(0deg);
        opacity: 1;
        border-radius: 0;
    }

    100%{
        transform: translateY(-1000px) rotate(720deg);
        opacity: 0;
        border-radius: 50%;
    }

}
	#body {
		padding:10px;
		padding-bottom:60px;
	}
	#footer {
       background: #69a1af;
		position:absolute;
		bottom:0;
		width:100%;
    }
    
	</style>
</head>
<body id="daily">

<div id="fb-root"></div>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.3"></script>

<!-- <script>
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=225531371657114&autoLogAppEvents=1';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script> -->

<div id="container">
    <?php if ($show_offer) { ?>
        <div id="header">
            <a target="_blank" href="<?php echo $webinar_url ?>" style="color: #FFF">
                <div class="topbar">
                    <div class="area" >
                        <ul class="circles">
                                <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
                                <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
                                <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
                        </ul>
                    </div >
                    <div class="align-items-center text-center text-white" data-href="https://mamadiario.com/<?php echo $this_webpage ?>">
                        <?php if ($is_video_unlocked) { ?>    
<!-- FIRST WEEK                         
                            <p class="d-block">Haz Clic AQUÍ para ir al <span class="underline">NUEVO WEBINAR</span> por Tiempo Limitado!<br>Logra cambios PERMANENTES en tus Hijos Que Hagan Caso, No Peleen, Hagan sus Rutinas...</p> 
-->                            
                            <p class="d-block">Haz Clic AQUÍ para saber más del Curso Montessori para aplicar en Casa</p> 
                        <?php } ?>
                    </div>
                </div>
            </a>
        </div>
    <?php } else { ?>                            
        <div id="header">
        <div class="topbar">
            <div class="row p-2">
                <div class="col-2">
                   <a href="/"><img class="ml-5" src="./img/mama_logo.png" alt="" width="70px"></a>
                </div>
                <div class="col-6 col-md-8 text-center text-white" data-href="https://mamadiario.com/<?php echo $this_webpage ?>">
                    <?php if ($is_video_unlocked) { ?>    
                        <p class="d-none d-md-block">Ayuda a otras Mamás... <span class="underline"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fmamadiario.com%2F<?php echo $this_webpage ?>&amp;src=sdkpreparse" style="color: #FFF">¡Comparte Mamá Diario!</a></span> </p>
                    <?php } ?>
                </div>
                <div class="col-2 mt-1">
                    <a class="btn  btn-top" href="./learningsection.php">Entrar</a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>                            
	<div id="body">
        <div class="objects mobileHide">
            <img class="obj1" src="./img/dv_1.png" alt="">
            <img class="obj2" src="./img/dv_2.png" alt="">
            <img class="obj3" src="./img/dv_3.png" alt="">
            <img class="obj4" src="./img/dv_4.png" alt="">
            <img class="obj5" src="./img/dv_5.png" alt="">
        </div>
        <div class="container content mt-5">
        <!-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
           <strong>Has cancelado tu subscripción exitosamente. Ya no recibirás los videos de Mamá Diario.</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> -->
            <div class="row">
                <div class="col-5 p-3">
                    <img class="content float-right mobileHide" src="./img/logo.png" alt="" width="150px">
                </div>
                <div class="col-7">
                    <div class="text-center float-left mobileCenter">
                        <p class="title">TIPS DIARIOS </p>
                        <p class="subtitle mer">con Diego de Palacio</p>
                    </div>
                </div>
            </div>
            
            <div class="video-height">
                <?php
                    // $sql = $conn->query("SELECT * FROM `tbl_mama_videos` WHERE webpage = '$webpage'");
                    // if ($sql->num_rows > 0) {
                    //     while($data = $sql->fetch_array()) {
                    //         $url = $data['mama_url'];
                    //         $des = $data['mama_description'];
                   if (count($rows) > 0) {
                        
                ?>
                <div id="loadingIcon">
                    <img style="position: absolute;z-index: 9999;top: 371px;left: 545px;" src="img/loader.gif" class="loadingSpinner">
                </div>

                <div style='position:relative;height:0;padding-bottom:56.25%'>
                    <?php if ($show_offer) { ?>
                        <iframe class='sproutvideo-player' src='https://videos.sproutvideo.com/embed/<?php echo $url ?>?playerColor=69a2b0&amp;playerTheme=light&amp;settingsButton=false&amp;postrollText=Haz%20clic%20aqu%C3%AD%20para%20saber%20m%C3%A1s%20del%20Curso%20Montessori&amp;postrollUrl=https%3A%2F%2Fwebinar.mamadiario.com%2Fcurso_montessori&amp;postrollFontSize=36&amp;postrollFontColor=FFFFFF&amp;postrollBgColor=666666&amp;postrollTarget=_top' style='position:absolute;width:100%;height:100%;left:0;top:0' frameborder='0' allowfullscreen></iframe>
                        <!-- <iframe class='sproutvideo-player' src='https://videos.sproutvideo.com/embed/<?php echo $url ?>?playerColor=69a2b0&amp;playerTheme=light&amp;settingsButton=false&amp;postrollText=Haz%20clic%20aqu%C3%AD%20para%20saber%20m%C3%A1s%20del%20Programa%20Mam%C3%A1%20Diario&amp;postrollUrl=https%3A%2F%2Fwebinar.mamadiario.com%2Fprograma-de-mama-diario31450231&amp;postrollFontSize=36&amp;postrollFontColor=FFFFFF&amp;postrollBgColor=666666&amp;postrollTarget=_top' style='position:absolute;width:100%;height:100%;left:0;top:0' frameborder='0' allowfullscreen>     -->
                    <?php } else { ?>
                        <iframe class='sproutvideo-player' src='https://videos.sproutvideo.com/embed/<?php echo $url ?>?playerColor=69a2b0&amp;playerTheme=light&amp;settingsButton=false&amp;postrollText=Haz%20clic%20aqu%C3%AD%20para%20recibir%20m%C3%A1s%20videos%20GRATIS&amp;postrollUrl=https%3A%2F%2Fmamadiario.com%2Fsubscribeme.php&amp;postrollFontSize=36&amp;postrollFontColor=FFFFFF&amp;postrollBgColor=666666&amp;postrollTarget=_blank' style='position:absolute;width:100%;height:100%;left:0;top:0' frameborder='0' allowfullscreen>
                    <?php } ?>
                </iframe></div>
                
                <!-- <?php 
//                    echo $data['mama_url']; 
//                    echo $url; 
                ?> -->
                <?php
//                }
                } else {
                    echo "<h5 class='text-center'>No se han encontrado datos para hoy!!</h5>";
                }

                ?>
                <!-- <div class="embed-responsive embed-responsive-16by9 p-5">
                    <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/66140585" allowfullscreen></iframe>
                </div> -->
            </div>

            <script type="text/javascript">
                function submitForm(action) {
                    var form = document.getElementById('days_buttons');
                    form.action = action;
                    form.submit();
                }
            </script>

            <?php if ($is_video_unlocked) { ?>
            <form id="days_buttons" method="POST">
            <div class="row pt-5">
                <div class="col-4 text-right">
                    <button type="submit" name="today" class="btn <?php echo $b1; ?> <?php echo $m1; ?> btn-size p-2 text-center" href="#" onclick="submitForm('<?php echo $today_webpage ?>')" <?php if ($this_webpage == $today_webpage) { echo 'disabled'; } ?>>HOY</button>
                </div>
                <div class="col-4 text-center">
                    <button type="submit" name="ayer" class="btn <?php echo $b2; ?> <?php echo $m2; ?>  btn-size p-2 text-center" href="#" onclick="submitForm('<?php echo $yesterday_webpage ?>')" <?php if ($this_webpage == $yesterday_webpage) { echo 'disabled'; } ?>>AYER</button>
                </div>
                <div class="col-4 text-left">
                    <button type="submit" name="ant" class="btn <?php echo $b3; ?> <?php echo $m3; ?> btn-size p-2 text-center" href="#" onclick="submitForm('<?php echo $two_days_ago_webpage ?>')" <?php if ($this_webpage == $two_days_ago_webpage) { echo 'disabled'; } ?>>ANTIER</button>
                </div>
            </div>
            </form>

            <h3 class="text-center f-20 mt-5">Si estás buscando el vídeo de otro día,<br>ya no está disponible.</h3>
            <p class="text-center">(Ve <a href="whydothevideosexpire.php">aquí</a> el porqué)</p>
            <?php } ?>

            <div class="content-body text-center mobileChange">
                <div class="row justify-content-center pl-3 pr-3">
                
                    <div class="col-6 d-md-block mobileDevice">
                        <p><?php echo '<h5 class="text-center">'.$des.'</h5>'; ?></p>
                    </div>
                    <div class="sharethis-inline-share-buttons"></div>
                    <?php if ($is_video_unlocked) { ?>
                    <div class="col-6 col-lg-4">

                        <div class="row text-center">
<!-- old -->
                            <!-- <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btnred text-center"><i class="fa fa-youtube-play" aria-hidden="true"></i>  Suscríbirme </a> -->
<!-- new -->
<a href="subscribeme.php" class="btn btnred text-center"><i class="fa fa-youtube-play" aria-hidden="true"></i>  Suscribirme </a>
                        </div>
                        
                    </div>
                    <?php } ?>
                </div>

                <div class="container text-center">
                    <div class="fb-comments" data-href="https://mamadiario.com/<?php echo $this_webpage ?>" data-numposts="10"></div>
                </div>
            </div>

        </div>

	</div>

	<div id="footer" class="text-center text-white p-4">
        <img src="./img/mama-footer.png" class="center mb-4" alt="" height="100px">
        <h5>Todos los derechos reservados 2019 – Mamá Diario </h5>
	</div>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="exampleModalLabel">Subscribe</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>

      <div class="modal-body">

            <form action="subscribe.php" id="myform" method="POST">

                <div class="form-group">
                    <label for="name">Full Name:<span style="color: red"><sup>*</sup></span></label>
                    <input type="text" id="Name" name="FullName" class="form-control full_name" placeholder="ex: John Doe">
                </div>

                <div class="form-group">
                    <label for="email">Email:<span style="color: red"><sup>*</sup></span></label>
                    <input type="email" id="useremail" name="EmailId" class="form-control email_id" placeholder="ex: john@doe.com">
                </div>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Dismiss</button>
                <input type="submit" id="submit" class="btn btn-primary" value="Subscribe">

            </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="subscribe.js"></script>

<script>
    $(document).ready(function() {
        // $("#loadingIcon").delay(1000).fadeOut();
        // $('#loadingIcon').delay(25000);
        // $('#loadingIcon').hide(25000);
        setTimeout('$("#loadingIcon").hide()',4000);
    });
</script>

<?php if ($show_offer) { ?>
<!--   Deadline Funnel 
    <script type="text/javascript" data-cfasync="false">function SendUrlToDeadlineFunnel(e){var r,t,c,a,h,n,o,A,i = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",d=0,l=0,s="",u=[];if(!e)return e;do r=e.charCodeAt(d++),t=e.charCodeAt(d++),c=e.charCodeAt(d++),A=r<<16|t<<8|c,a=A>>18&63,h=A>>12&63,n=A>>6&63,o=63&A,u[l++]=i.charAt(a)+i.charAt(h)+i.charAt(n)+i.charAt(o);while(d<e.length);s=u.join("");var C=e.length%3;var decoded = (C?s.slice(0,C-3):s)+"===".slice(C||3);decoded = decoded.replace("+", "-");decoded = decoded.replace("/", "_");return decoded;} var url = SendUrlToDeadlineFunnel(location.href); var parentUrlValue;try {parentUrlValue = window.parent.location.href;} catch(err) {if(err.name === "SecurityError") {parentUrlValue = document.referrer;}}var parentUrl = (parent !== window) ? ("/" + SendUrlToDeadlineFunnel(parentUrlValue)) : "";(function() {var s = document.createElement("script");s.type = "text/javascript";s.async = true;s.setAttribute("data-scriptid", "dfunifiedcode");s.src ="https://a.deadlinefunnel.com/unified/reactunified.bundle.js?userIdHash=eyJpdiI6Ik9Qb21hRFBKVno4WkE1cDY1WEsrbUE9PSIsInZhbHVlIjoib0trV0NmOVNKeUtkbGRHdER3cHpxZz09IiwibWFjIjoiYTA5OGM0MmI0NzE1MGEwNDI2NjIxZTZiNTUxMDAyYWY0ODMwMjA5N2QzZjk0YjdmMGM2NzUzN2RiNDVhNzkzNyJ9&pageFromUrl="+url+"&parentPageFromUrl="+parentUrl;var s2 = document.getElementsByTagName("script")[0];s2.parentNode.insertBefore(s, s2);})();</script>
-->    
<?php } ?>

</body>

</html>

<?php }
else{
    header("Location: /dailyvideomissed.php");
    exit();
}?>
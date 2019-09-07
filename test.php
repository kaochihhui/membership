<?php
    // 1.- Declare the variables (arguments) in PHP
    // 2.- Call a PHP method to update the variables values from the database before loading the webpage
    // 3.- Load the webpage with the updated values
    // 4.- Call a PHP method to update the watched video in the database (using the email as argument) if the user clicks the button
    // 5.- Call a PHP method to update the reported habit in the database (using the email as argument) if the user selects one of the radio options 
    // 6.- After the steps 5 or 6, do the step 2 and 3 without refreshing the webpage
    // 7.- Animate the labels "Progress", "Percentage" and "CommunityProgress" if they're changed
    // 8.- After step 5, hide the not selected radio buttons and show a label with the days in a row

    $Email = "diegodepalacio@gmail.com";
    $Name = "Diego";
    $Progress = 42;
    $Percentage = 19;
    $CommunityProgress = 30310;
    $Goal = 100000;
    $ReportedVideo = false;
    $ReportedHabit = 0;
    $InARow = 10;

    // function get_challenge_data(&$Progress, &$CommunityProgress, &$Percentage)
    // {
    //     $Progress++;
    //     $CommunityProgress++;

    //     if ($Progress > 50)
    //     {
    //         $Percentage = 80;
    //     }
    // }

    function add_challenge_watched_video($Email, &$Progress, $CommunityProgress, $Percentage)
    {
        $Progress++;
        $CommunityProgress++; // test if CommunityProgress updated
        $Percentage++; // test if Percentage updated
        return array ($Progress,$CommunityProgress,$Percentage);
    }

    function add_challenge_habit($Email, &$Progress, &$InARow, &$ReportedHabit, $CommunityProgress, $Percentage)
    {
        $Progress++;
        $InARow++;
        $CommunityProgress++; // test if CommunityProgress updated
        $Percentage++; // test if Percentage updated
        // update $ReportedHabit value after selecting from the radio button
        return array ($Progress,$InARow,$CommunityProgress,$Percentage);
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/section-style.css">
    <title>MembershipWebsite</title>
  </head>
  <body>
  <div class="container-fluid">
    <div class="section-head">
        <div class="row">
            <div class="colStyle col-6 col-lg-3 pl-0">
                <div class="grid p-3">
                    <div class="partTitle"><p><?php echo $Name ?></p></div>
                    <div class="progessNumber"><p><?php echo $Progress ?></p></div>
                    <div class="videoBtn animated pulse infinite faster"><button type="button" class="btn btn-light">Ya Vi el Video</button></div>
                    
                </div>
            </div>
            <div class="colStyle col-6 col-lg-3 p-0 pr-lg-3">            
                <div class="grid p-3">
                    <div class="partTitle"><p>TU HABITO</p></div>
                    <div class="radioBtn">
                        <div>
                        <!-- <div><input id="1a1" type="radio" name="habit" value="1a1"><label for="1a1">1 a 1</label></div> -->
                        <!-- <div><input id="Calma" type="radio" name="habit" value="Calma"><label for="Calma">Calma</label></div> -->
                        <!-- <div><input id="Eleccion" type="radio" name="habit" value="Eleccion"><label for="Eleccion">Eleccion</label> </div> -->
                        
                            <div class="radio-1a1 none">
                            <label><input type="radio" name="habit" value="1a1">1 a 1</label>
                            </div>
                            <div class="radio-Calma none">
                            <label><input type="radio" name="habit" value="Calma">Calma</label>
                            </div>
                            <div class="radio-Eleccion none">
                            <label><input type="radio" name="habit" value="Eleccion">Eleccion</label>
                            </div>
                        </div>
                    </div>
                    <div class="DaysInARow none"><p><span>DaysInARow</span><span class="DaysInARowNumber"><?php echo $InARow ?></span></p></div>
                </div>
            </div>

            <div class="w-100 d-lg-none row-space"></div>

            <div class="colStyle col-6 col-lg-3 pl-0">            
                <div class="grid p-3">
                    <div class="partTitle"><p>TU POSICION</p></div>
                    <div class="percNumber"><p><?php echo $Percentage, '%' ?></p></div>
                    <div class="smileGroup">
                        <div class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                        <div id="smile2" class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                        <div id="smile3" class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                    </div>
                </div>
            </div>
            <div class="colStyle col-6 col-lg-3 p-0">            
                <div class="grid p-3">
                    <div class="progHead">
                        <div class="progTitle"><p>NUESTRA META</p></div>
                        <div class="questionMark"><a href="" target="_blank"><img src="./img/section-img/question-circle-regular.svg"></a></div>
                    </div>
                    
                    <div class="row progBar">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="justify-content-center d-flex position-absolute w-100 progress-value"></div>
                            </div>
                            <div class="commNumber"><p><?php echo $CommunityProgress ?></p></div>
                        </div>
                        <div class="col-2 p-0 totalNumber"><?php echo $Goal/1000, 'k' ?></div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    </div>
    <!-- <h1>Hello, world!</h1>
    <p>
        heall <?php echo 'Hello World !'; ?>
    </p> -->
    <!-- Optional JavaScript -->
    <!-- <script src="js/scripts.js"></script> -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- <script src="./js/membership.js"></script> -->
    <script> 
    let Email = "<?php echo $Email ?>",
        name = "<?php echo $Name ?>",
        Progress = <?php echo $Progress ?>,
        Percentage = <?php echo $Percentage ?>,
        CommunityProgress = <?php echo $CommunityProgress ?>,
        Goal = <?php echo $Goal ?>,
        ReportedVideo = "<?php echo $ReportedVideo ?>",
        ReportedHabit = <?php echo $ReportedHabit ?>,
        InARow = <?php echo $InARow ?>;

    let ReportedHabitArray = ['1a1', 'Calma', 'Eleccion']


        ////////////////////////////// After page load //////////////////////////////
        
        if(ReportedHabit > 0){ // get ReportedHabit from DB
            let selectedRadioBtn = ReportedHabitArray[ReportedHabit-1]; 
            $('div[class^="radio-"]').removeClass("none");
            $('div[class^="radio-"]:not(.radio-'+selectedRadioBtn+')').addClass("none");
            $('.radio-'+selectedRadioBtn).find('input').prop( "checked", true );
            $('.radio-'+selectedRadioBtn).find('input').prop( "disabled", true );
            
            $('.DaysInARowNumber').html(InARow); // get DaysInARow from DB
            $(".DaysInARow").removeClass("none");
        }
        else $('div[class^="radio-"]').removeClass("none");        
        
        ///////////////////////////////////////////////////////////////////////////

        ////////////////////////////// "Tu Progreso" //////////////////////////////

        // video button only be shown when ReportedVideo is false 
        if (!ReportedVideo) $('.videoBtn').removeClass('none');
        else $('.videoBtn').addClass('none');

        // after video button being clicked
        $('.videoBtn').click(()=>{
            ReportedVideo = true;
            $('.videoBtn').addClass('none'); // hide video button

            // Progress value increases 1
            let tmpArray = <?php echo json_encode(add_challenge_watched_video($Email,$Progress, $CommunityProgress, $Percentage)) ?> 
            Progress = tmpArray[0];
            $('.progessNumber p').html(Progress); 
            $('.progessNumber p').addClass('animated heartBeat slower'); // animation

            // update CommunityProgress value from DB
            CommunityProgress = tmpArray[1];
            $('.commNumber p').html(CommunityProgress); 
            $('.commNumber p').addClass('animated heartBeat slower'); // animation

            // progress bar percentage 
            updateProgBarPerc(CommunityProgress,Goal)
            
            // update Percentage value from DB
            Percentage = tmpArray[2];
            $('.percNumber p').html(Percentage+'%'); 
            if (Percentage == 21 || Percentage == 81 ) $('.percNumber p').addClass('animated heartBeat slower'); // animation
            smilyFaces(Percentage)
        });

        ///////////////////////////////////////////////////////////////////////////


        ////////////////////////////// "Tu Hábito" //////////////////////////////


        $("input[name='habit']").click(()=>{
            
            // hide those unchecked radio buttons
            let selectedValue= $("input[name='habit']:checked").val();
            console.log(selectedValue)
            $('div[class^="radio-"]:not(.radio-'+selectedValue+')').addClass("none");

            // update ReportedHabit value
            if(selectedValue == '1a1') ReportedHabit = 1;
            else if(selectedValue == 'Calma') ReportedHabit = 2;
            else if(selectedValue == 'Eleccion') ReportedHabit = 3;

            let tmpArray = <?php echo json_encode(add_challenge_habit($Email, $Progress, $InARow, $ReportedHabit, $CommunityProgress, $Percentage)) ?> 
            
            // update Progress value
            Progress = tmpArray[0];
            $('.progessNumber p').html(Progress); 
            $('.progessNumber p').addClass('animated heartBeat slower'); // animation

            // update DaysInARow value
            InARow = tmpArray[1];
            $('.DaysInARowNumber').html(InARow);
            $(".DaysInARow").removeClass("none");

            // update CommunityProgress value from DB
            CommunityProgress = tmpArray[2];
            $('.commNumber p').html(CommunityProgress); 
            $('.commNumber p').addClass('animated heartBeat slower'); // animation

            // progress bar percentage 
            updateProgBarPerc(CommunityProgress,Goal)
            
            // update Percentage value from DB
            Percentage = tmpArray[3];
            $('.percNumber p').html(Percentage+'%'); 
            if (Percentage == 21 || Percentage == 81 ) $('.percNumber p').addClass('animated heartBeat slower'); // animation
            smilyFaces(Percentage)
        })

        ///////////////////////////////////////////////////////////////////////////

        ////////////////////////////// "Tu Posición" //////////////////////////////

        // smile face's opacity changed by the percentage
        smilyFaces(Percentage)

        ///////////////////////////////////////////////////////////////////////////

        ////////////////////////////// "La Meta" //////////////////////////////

        // progress bar percentage 
        updateProgBarPerc(CommunityProgress,Goal)

        ///////////////////////////////////////////////////////////////////////////

        function updateProgBarPerc(CommunityProgress,Goal){
            let progPerct = CommunityProgress/Goal*100;
            $(".progress-bar").width(progPerct+'%');
            $(".progress-value").html(progPerct.toFixed(2)+' %');
        }

        function smilyFaces(Percentage){ 
            // 1 to 20 => 1 smile face 
            // 21 to 80 => 2 smile faces
            // 81 to 100 => 3 smile faces

            if(Percentage > -1 && Percentage < 21) $("#smile2, #smile3").addClass("smileOpacity")
            else if(Percentage > 20 && Percentage < 81) {
                $("#smile2").removeClass("smileOpacity");
                $("#smile3").addClass("smileOpacity");
            }
            else $("#smile2, #smile3").removeClass("smileOpacity");

        }
    </script>
  </body>
</html>
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
    $Percentage = 78;
    $CommunityProgress = 30310;
    $Goal = 100000;
    $ReportedVideo = false;
    $ReportedHabit = 0;
    $InARow = 10;

    function get_challenge_data(&$Progress, &$CommunityProgress, &$Percentage)
    {
        $Progress++;
        $CommunityProgress++;

        if ($Progress > 50)
        {
            $Percentage = 80;
        }
    }

    function add_challenge_watched_video($Email, &$Progress)
    {
        $Progress++;
    }

    function add_challenge_habit($Email, &$Progress)
    {
        $Progress++;
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
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/section-style.css">
    <title>MembershipWebsite</title>
  </head>
  <body>
  <div class="container-fluid">
    <div class="section-head">
        <div class="row">
            <div class="colStyle col-6 col-lg-3 pl-0">
                <div class="grid p-2">
                    <div class="nameOfUser"><p><?php echo $Name ?></p></div>
                    <div class="progessNumber"><p><?php echo $Progress ?></p></div>
                    <div class="videoBtn none"><button type="button" class="btn btn-light">Ya Vi el Video</button></div>
                    
                </div>
            </div>
            <div class="colStyle col-6 col-lg-3 p-0 pr-lg-3">            
                <div class="grid p-2">
                    <div><p>TU HABITO</p></div>
                    <div class="radioBtn">
                        <div>
                        <div><input id="1a1" type="radio" name="habit" value="1a1"><label for="1a1">1 a 1</label></div>
                        <div><input id="Calma" type="radio" name="habit" value="Calma"><label for="Calma">Calma</label></div>
                        <div><input id="Eleccion" type="radio" name="habit" value="Eleccion"><label for="Eleccion">Eleccion</label> </div>
                        </div>
                    </div>
                    <div class="DaysInARow none"><p><span>DaysInARow</span><span class="DaysInARowNumber"><?php echo $InARow ?></span></p></div>
                </div>
            </div>

            <div class="w-100 d-lg-none row-space"></div>

            <div class="colStyle col-6 col-lg-3 pl-0">            
                <div class="grid p-2">
                    <div><p>TU POSICION</p></div>
                    <div class="percNumber"><p><?php echo $Percentage, '%' ?></p></div>
                    <div class="smileGroup">
                        <div class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                        <div id="smile2" class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                        <div id="smile3" class="smile"><img src="./img/section-img/smile-regular.svg"></div>
                    </div>
                </div>
            </div>
            <div class="colStyle col-6 col-lg-3 p-0">            
                <div class="grid p-2">
                    <div class="progHead">
                        <div class="progTitle"><p>NUESTRA META</p></div>
                        <div class="questionMark"><a href="" target="_blank"><img src="./img/section-img/question-circle-regular.svg"></a></div>
                    </div>
                    
                    <div class="row progBar">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-2 p-0 totalNumber"><?php echo $Goal/1000, 'k' ?></div>
                    </div>
                    <div class="commNumber"><p><?php echo $CommunityProgress ?></p></div>
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
    <script src="./js/membership.js"></script>
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

        ////////////////////////////// "Tu Progreso" //////////////////////////////

        // video button only be shown when ReportedVideo is false 
        if (ReportedVideo) $('.videoBtn').removeClass('none');

        // after video button being clicked
        $('.videoBtn').click(()=>{
            $ReportedVideo = true;
            $('.videoBtn').addClass('none'); // hide video button
            Progress = <?php echo add_challenge_watched_video($Email,$Progress);?> // progress number increase 1
            $('.progessNumber p').html(Progress); // animation
            // $(".progessNumber p").animate({zoom: '150%'}, "slow");
        });

        ///////////////////////////////////////////////////////////////////////////


        ////////////////////////////// "Tu Hábito" //////////////////////////////


        $("input[name='habit']").click(()=>{
            $(".DaysInARow").removeClass("none")
            // console.log($("input[name='habit']:checked").val())
            $("input[name='habit']").each(() =>{
                // console.log($(this))
            })
        })

        ///////////////////////////////////////////////////////////////////////////

        ////////////////////////////// "Tu Posición" //////////////////////////////

        // smile face's opacity changed by the percentage 

        if(Percentage > -1 && Percentage < 20) $("#smile2, #smile3").addClass("smileOpacity")
        else if(Percentage > 21 && Percentage < 80) {
            $("#smile2").removeClass("smileOpacity")
            $("#smile3").addClass("smileOpacity")
        }
        else $("#smile2, #smile3").removeClass("smileOpacity")

        ///////////////////////////////////////////////////////////////////////////

        ////////////////////////////// "La Meta" //////////////////////////////

        // progress bar percentage 
        let progPerct = CommunityProgress/Goal*100;
        $(".progress-bar").width(progPerct+'%')

        ///////////////////////////////////////////////////////////////////////////

    </script>
  </body>
</html>
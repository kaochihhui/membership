
$( document ).ready(function() {
let membership = [
    { Name: "name of user" },
    { Progress: 42},
    { Percentage: 78},
    { CommunityProgress: 30310},
    { Goal: 100000},
    { ReportedVideo: false},
    { ReportedHabit: 123},
    { DaysInARow: 3}
]

$('.nameOfUser p').html(membership[0].Name);
$('.progessNumber p').html(membership[1].Progress);
$('.percNumber p').html(membership[2].Percentage + '%');
$('.commNumber p').html(membership[3].CommunityProgress);
$('.totalNumber').html(membership[4].Goal/1000 + 'k');
$(".DaysInARowNumber").html(membership[7].DaysInARow);


////////////////////////////// "Tu Progreso" //////////////////////////////

// video button only be shown when ReportedVideo is false 
if (!membership[5].ReportedVideo) $('.videoBtn').removeClass('none');

// after video button being clicked
$('.videoBtn').click(()=>{
    membership[5].ReportedVideo = true;
    $('.videoBtn').addClass('none'); // hide video button
    membership[1].Progress++; //progress number increase 1
    $('.progessNumber p').html(membership[1].Progress); // animation
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

if(membership[2].Percentage > -1 && membership[2].Percentage < 20) $("#smile2, #smile3").addClass("smileOpacity")
else if(membership[2].Percentage > 21 && membership[2].Percentage < 80) {
    $("#smile2").removeClass("smileOpacity")
    $("#smile3").addClass("smileOpacity")
}
else $("#smile2, #smile3").removeClass("smileOpacity")

///////////////////////////////////////////////////////////////////////////

////////////////////////////// "La Meta" //////////////////////////////

// progress bar percentage 
let progPerct = membership[3].CommunityProgress/membership[4].Goal*100;
$(".progress-bar").width(progPerct+'%')

///////////////////////////////////////////////////////////////////////////

});
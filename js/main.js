$.fn.reset = function(){
    this.val(''); 
}

$.fn.showMessage = function(text){
    this.text(text); 
    this.fadeIn();
}


var msisdnValid = function(){
    return ! $('#msisdn').val() == ''; 
}

$('#findClient').click(function(e){

}); 

$('#msisdn').change(function(){
    if( msisdnValid() ) {
        $('#status1').fadeOut();
    } else {
        $('#status1').fadeIn();
    }
}); 

var Availible = 0; 
var isRandomUser = false; 

$('#findClient').click(function(e){
    e.preventDefault(); 

    if( ! msisdnValid() )
        return $('#status1').fadeIn(); 

    $('#step2').trigger('click'); 
    if( ! isRandomUser )
      $('status').fadeOut(); 


    $('#timeUsed').reset(); 
    Availible = Math.floor(Math.random() * 100) + 1; 
    $('#timeAvailible').val(Availible); 
}); 

$('#getRandomUser').click(function(e){
  e.preventDefault(); 
  $('#status').showMessage('random user'); 
  //console.log('rand'); 
}); 

var setTime = function(){
    var timeUsed = parseInt($('#timer').val()); 
    var timeAvailible = parseInt( $('#timeAvailible').val() || 0);

    timeUsed = timeUsed || 0; 
    timeUsed++; 

    $('#timer').val(timeUsed);     

    if(timeUsed >= timeAvailible){
    $('#status').showMessage('time limit');           
        return $('#stop').trigger('click'); 
    }

}

var timer = null; 

$('#start').click(function(){

    if( timer ) return; 

    var timeAvailible = parseInt( $('#timeAvailible').val() || 0);

    if(timeAvailible <= 0)
        return $('#status').showMessage('user account is empty'); 

    if( ! timer )
        timer =  setInterval(setTime, 1000); 

    $('#timer').val(0);
}); 

$('#stop').click(function(){
    if( timer ){
        clearInterval(timer); 
        timer = null; 
    }
    
    var timeUsed = parseInt( $('#timer').val() ) || 0; 
    var currentTime = parseInt( $('#timeUsed').val() ) || 0; 

    currentTime += timeUsed;
    var timeAvailible = Availible - currentTime; 

    $('#timeUsed').val(currentTime); 
    $('#timeAvailible').val(timeAvailible); 
        
    $('#timer').reset(); 
});
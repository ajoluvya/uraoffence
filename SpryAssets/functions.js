$(document).ready(function(){
  $("#ftl").click(function(){
    $("#month,#rep_month").prop("disabled",false);
    $("#date1").prop("disabled",true);
    $("#date2").prop("disabled",true);
  });
  $("#dtr").click(function(){
    $("#date2").prop("disabled",false);
    $("#date1").prop("disabled",false);
    $("#month,#rep_month").prop("disabled",true);
  });
  //dutable Items or not
  $("#dutable").change(function(){
		if($(this).prop("checked")==true)
		$("#dtbl").html("Dutable");
		else
		$("#dtbl").html("Non-dutable");
	});
});	
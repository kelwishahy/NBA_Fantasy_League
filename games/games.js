if (getCookie("teamid") == ""){
	window.location.replace("../profile/profile.html");
} else {
	$('#profile_name').html(getCookie("username") + "'s Profile");
}

//logout button functionality
$('#logout').click(function(e){
	document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	window.location.replace("../index.php");
});

//profile button functionality
$('#profile').click(function(e){
	window.location.replace("../profile/profile.html");
});

getAllGames();
getMyPlayers(getCookie("teamid"));

//function to get a cookie stored in the browser
//returns cookie as a string
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function getAllGames(){
	$.post('php/getAllGames.php', {}, function(json_result) {
	  console.log(json_result);
	  var index = 0;
	  json_result.FINALSCORE.forEach(function(element){
		drawGame(json_result.FINALSCORE[index], json_result.DATEPLAYED[index], json_result.COMPETINGTEAMS[index]);
		index++;
	  })
   }, 'json');
}

function drawGame(score, date, teams){
	HTMLcode = "<div class = \"game-box\">" +
				"<date>" + date + "</date>" +
				"<t1>" + score.trim().split('-')[0] + "</t1>" +
				"<t2>" + score.trim().split('-')[1] + "</t2>" +
				"<t1name>" + teams.trim().split(',')[0] + "</t1name>" +
				"<t2name>" + teams.trim().split(',')[1] + "</t2name>" +
				"<vs>vs</vs>" +
				"</div>";
				
	$('#main-panel').append(HTMLcode);
}

function getMyPlayers(teamid){
	$.post('php/getMyPlayers.php', {teamid: teamid}, function(json_result) {
	  console.log(json_result);
	  $('#main-panel').append("<div id = \"player-list\" class = \"player-box\">My Players</div>");
	  var index = 0;
	  json_result.PLAYERNUMBER.forEach(function(element){
		drawPlayer(json_result.PLAYERNUMBER[index], json_result.NBATEAM[index], json_result.PLAYERNAME[index]);
		index++;
	  })
   }, 'json');
}

function drawPlayer(num, team, name){
	HTMLcode = "<player-row id = \"" + num + "_" + team.trim() + "\">" + num + " " + name + " - " + team + "</player-row>";
	$('#player-list').append(HTMLcode);
	
	//give functionality to button
	$('#' + num + "_" + team.trim()).click(function(e){
			getPlayerGames(team, num);
	});
}

function getPlayerGames(team, num){
	$.post('php/getPlayerGames.php', {team: team, num: num}, function(json_result) {
	  console.log(json_result);
	  $('#main-panel').empty();
	  getMyPlayers(getCookie('teamid'));
	  var index = 0;
	  json_result.FINALSCORE.forEach(function(element){
		drawGame(json_result.FINALSCORE[index], json_result.DATEPLAYED[index], json_result.COMPETINGTEAMS[index]);
		index++;  
	  });
   }, 'json');
}
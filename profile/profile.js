if (getCookie("username") == ""){
	window.location.replace("https://www.students.cs.ubc.ca/~zachvav/NBA_Fantasy_League/index.php");
} else {
	$('#profile_name').html(getCookie("username") + "'s Profile");
}

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

getTeams();

// Get all of the profile information and display it on the page
function getTeams() {
    $.post('php/getTeams.php', {username: getCookie("username")}, function(json_result) {
	  console.log(json_result);
	  var index = 0;
	  json_result.TEAMNAME.forEach(function(element){
		  drawTeam(json_result.TEAMID[index], json_result.TEAMNAME[index], json_result.TOTALPOINTS[index], json_result.LEAGUENAME[index], json_result.LOGO[index], json_result.LEAGUEID[index]);
		  index ++;
	  })
   }, 'json');
}

//draw a team box
function drawTeam(id, name, points, leaguename, logo, leagueid){
	var HTMLcode = "<div class = \"team-box\">" +
						"<league-name>" + leaguename + "</league-name>" +
						"<league-icon> <img width=200 height=200 src = \"" + logo + "\"> </league-icon>"  +
						"<tb-line></tb-line>" +
						"<team-name>" + name + "</team-name>" +
						"<score>" + points + "</score>" +
						"<misc id = \"" + id + "\"></misc>"
					"</div>";
	$('#teams').append(HTMLcode);
	
	var BUTTONHTMLcode = "<div class = \"team-buttons\">" +
							"<buttons-1 id = \"" + "1_" + id + "\"></buttons-1>" +
							"<buttons-2 id = \"" + "2_" + id + "\">VIEW LEAGUE</buttons-2>" +
							"<buttons-3 id = \"" + "3_" + id + "\">MANAGE TEAM</buttons-3>" +
							"<buttons-4 id = \"" + "4_" + id + "\">VIEW HISTORY</buttons-4>" +
							"<buttons-5 id = \"" + "5_" + id + "\"></buttons-5>" +
						 "<\div>";
	$('#buttons').append(BUTTONHTMLcode);
	
	//give buttons functionality
	$('#2_' + id).click(function(e){
		console.log("view league");
	});
	
	$('#3_' + id).click(function(e){
		console.log("manage team");
	});
	
	$('#4_' + id).click(function(e){
		console.log("view history");
	});
	
	//calculate and draw the position of the team in the league
	getTeamPos(id, leagueid, points);
	
	//get and draw all of the trades for this team
	getTrades(id, leagueid);
}

// get the position of a team in a league
function getTeamPos(teamid, leagueid, points){
	$.post('php/getTeamPos.php', {teamid: teamid, leagueid: leagueid, points: points}, function(json_result) {
	  console.log(json_result);
	  $('#' + teamid).html("POS: " + json_result.TEAMPOS);
   }, 'json');
}

// get and draw all trades in notifications box for a team
function getTrades(teamid, leagueid){
	$.post('php/getTrades.php', {teamid: teamid}, function(json_result) {
	  console.log(json_result);
	  var index = 0;
	  json_result.STATUS.forEach(function(element){
		 drawTrade(json_result.TRADEID[index], json_result.STATUS[index], json_result.TRADEDATE[index], json_result.PLAYER1NUMBER[index], json_result.PLAYER2NUMBER[index], json_result.P1NAME[index], json_result.P2NAME[index], json_result.T1NAME[index], json_result.T2NAME[index]); 
		 index++;
	  });
   }, 'json');
}

function drawTrade(id, tradestatus, date, p1num, p2num, p1name, p2name, t1name, t2name){
	var HTMLcode = "<div class = \"trade-box\">" + 
						"<status>" + tradestatus + " trade</status>" +
						"<date>" + date + "</date>" +
						"<p1num>" + p1num + "</p1num>" +
						"<p2num>" + p2num + "</p2num>" +
						"<p1name>" + p1name + "</p1name>" +
						"<p2name>" + p2name + "</p2name>" +
						"<t1name>" + t1name + "</t1name>" +
						"<t2name>" + t2name + "</t2name>" +
						"<tline></tline>"
					"</div>";
					
	  $('#trades').append(HTMLcode);
}
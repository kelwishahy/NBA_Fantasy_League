if (getCookie("username") == ""){
	window.location.replace("../index.php");
} else {
	$('#profile_name').html(getCookie("username") + "'s Profile");
}

//logout button functionality
$('#logout').click(function(e){
	document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	window.location.replace("../index.php");
});

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

// Get all of the profile information and display it on the page
function getTeams() {
    $.post('php/getTeams.php', {username: getCookie("username")}, function(json_result) {
	  console.log(json_result);
	  var index = 0;
	  json_result.TEAMNAME.forEach(function(element){
		  drawTeam(json_result.TEAMID[index], json_result.TEAMNAME[index], json_result.TOTALPOINTS[index], json_result.LEAGUENAME[index], json_result.LOGO[index], json_result.LEAGUEID[index]);
		  index ++;
	  })
	  $('#teams').append("<div id = \"" + "join-league" + "\" class = \"join-league\">JOIN NEW LEAGUE</div>");
	  
	  //give functionality to join new league button
	  $('#join-league').click(function(e){
		  window.location.replace("../league/league.html");
	  });
	  
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
							"<buttons-1 id = \"" + "1_" + id + "\">VIEW PLAYERS</buttons-1>" +
							"<buttons-2 id = \"" + "2_" + id + "\">VIEW LEAGUE</buttons-2>" +
							"<buttons-3 id = \"" + "3_" + id + "\">MANAGE TEAM</buttons-3>" +
							"<buttons-4 id = \"" + "4_" + id + "\">VIEW GAMES</buttons-4>" +
							"<buttons-5 id = \"" + "5_" + id + "\">DELETE TEAM</buttons-5>" +
						 "<\div>";
	$('#buttons').append(BUTTONHTMLcode);
	
	//give buttons functionality
	$('#1_' + id).click(function(e){
		console.log("view players");
	});
	
	$('#2_' + id).click(function(e){
		console.log("view league");
	});
	
	$('#3_' + id).click(function(e){
		console.log("manage team");
		//document.cookie = "teamid=" + id;
		document.cookie = 'teamid=' + id + '; path=/; ;domain=.students.cs.ubc.ca'
		window.location.replace("../manageteam.php");
	});
	
	$('#4_' + id).click(function(e){
		console.log("view history");
		document.cookie = 'teamid=' + id + '; path=/; ;domain=.students.cs.ubc.ca'
		window.location.replace("../games/games.html");
	});
	
	$('#4_' + id).click(function(e){
		console.log("delete team");
	});
	
	//calculate and draw the position of the team in the league
	getTeamPos(id, leagueid, points);
	
	//get and draw all of the trades for this team
	getTrades(id, leagueid);
}

// get the position of a team in a league
function getTeamPos(teamid, leagueid, points){
	console.log(teamid + " " + leagueid + " " + points);
	$.post('php/getTeamPos.php', {teamid: teamid, leagueid: leagueid, points: points}, function(json_result) {
	  console.log(json_result);
	  $('#' + teamid).html("POS: " + json_result.TEAMPOS);
   }, 'json');
}

// get and draw all trades in notifications box for a team
function getTrades(teamid, leagueid){
	$.post('php/getTrades.php', {teamid: teamid}, function(json_result) {
	  console.log(json_result);
	  //if($('#trades').html() == "") $('#trades').append("<div class = \"title\">&nbsp&nbsp&nbspNOTIFICATIONS</div>");
	  var index = 0;
	  json_result.STATUS.forEach(function(element){
		 drawTrade(json_result.TRADEID[index], json_result.STATUS[index], json_result.TRADEDATE[index], json_result.PLAYER1NUMBER[index], json_result.PLAYER2NUMBER[index], json_result.P1NAME[index], json_result.P2NAME[index], json_result.T1NAME[index], json_result.T2NAME[index], json_result.T1ID[index], json_result.T2ID[index], teamid, json_result.P1TEAM[index], json_result.P2TEAM[index]); 
		 index++;
	  });
   }, 'json');
}

function drawTrade(id, tradestatus, date, p1num, p2num, p1name, p2name, t1name, t2name, t1id, t2id, myteamID, p1team, p2team){
	var HTMLcode = "<div class = \"trade-box\">" + 
						"<status>" + tradestatus + " trade</status>" +
						"<date>" + date + "</date>" +
						"<p1num>" + p1num + "</p1num>" +
						"<p2num>" + p2num + "</p2num>" +
						"<p1name>" + p1name + "</p1name>" +
						"<p2name>" + p2name + "</p2name>" +
						"<t1name>" + t1name + "</t1name>" +
						"<t2name>" + t2name + "</t2name>" +
						"<tline></tline>";
		if (tradestatus.trim() == "Pending" && myteamID == t2id){
			HTMLcode += "<accept-decline><div id = \"accept_" + id + "\"class = \"accept-icon\"><img src = \"../images/accept.png\"></div>" + 
						"<div id = \"deny_" + id + "\"class = \"deny-icon\"><img src = \"../images/deny.png\"></div>" + 
						"</accept-decline>";
		} else if (tradestatus.trim() == "Pending" && myteamID == t1id){
			HTMLcode += "<accept-decline>SENT</accept-decline>";
		}
		HTMLcode +=
					"</div>";
					
	  $('#trades').append(HTMLcode);
	  
	  //give accept/deny buttons functionality
	  if (tradestatus.trim() == "Pending" && myteamID == t2id){
		$('#accept_' + id).click(function(e){
			console.log("accept: " + id);
			acceptTrade(id, p1num, p2num, p1team, p2team, t1id, t2id);
		});
		
		$('#deny_' + id).click(function(e){
			console.log("deny: " + id);
			denyTrade(id);
		});
	  }
}

function acceptTrade(tradeid, p1num, p2num, p1team, p2team, t1id, t2id){
	$.post('php/acceptTrade.php', {tradeid: tradeid, p1num: p1num, p2num: p2num, p1team: p1team, p2team: p2team, t1id: t1id, t2id: t2id}, function(json_result) {
	  console.log(json_result);
	  if(json_result == true){
		$('#trades').empty();
		getTrades();
	  }
   }, 'json');
}

function denyTrade(tradeid){
	$.post('php/denyTrade.php', {tradeid: tradeid}, function(json_result) {
	  console.log(json_result);
	  if(json_result == true){
		$('#trades').empty();
		getTrades();
	  }
   }, 'json');
}

function getMaxTradeID(tradeid){
	$.post('php/getMaxTradeID.php', {}, function(json_result) {
	  console.log(json_result);
   }, 'json');
}

getTeams();
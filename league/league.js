//on load --------------------------

if (getCookie("username") == ""){
	window.location.replace("../index.php");
}

getLeagues(getCookie("username"));


//----------------------------------

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

//logout button functionality
$('#logout').click(function(e){
	document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	window.location.replace("../index.php");
});

//profile button functionality
$('#profile').click(function(e){
	window.location.replace("../profile/profile.html");
});

//get all leagues that you are not currently in
function getLeagues(username){
	$.post('php/getLeagues.php', {username: username}, function(json_result) {
	  console.log(json_result);
	  var index = 0;
	  json_result.LEAGUEID.forEach(function(e){
		  drawLeague(json_result.LEAGUEID[index], json_result.LEAGUENAME[index], json_result.LOGO[index], json_result.TEAMS[index]);
		  index++;
	  })
   }, 'json');
}

function drawLeague(id, name, logo, teams){
	var HTMLcode = "<div class = \"league-box\">" + 
		"<logo> <img width=150 height=150 src = \"" + logo + "\"> </logo>"  +
		"<name>" + name + "</name>" +
		"<team-count>Num of Competing Teams: " + teams + "</team-count>" +
		"<join id = \"" + id + "\">" + "JOIN" + "</join>" +
	"</div>";
	$('#main-panel').append(HTMLcode);
	
	//give functionality to join button
	$('#' + id).click(function(e){
		joinLeague(id, getCookie("username"));
	});
}

function joinLeague(id, username){
	$.post('php/joinLeague.php', {id: id, username: username}, function(json_result) {
	  console.log(json_result);
	  window.location.replace("../profile/profile.html");
   }, 'json');
}
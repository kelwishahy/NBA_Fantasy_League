drop table PlayersInTeam;
drop table LineupPlayers;
drop table StartingLineup;
drop table TeamOwnedBy;
drop table Trade;
drop table Team;
drop table LeagueLogo;
drop table League;
drop table PlayerInGame;
drop table RegularParticipant;
drop table LeagueCommissioner;
drop table NBAPlayer;
drop table NBAGame;
drop table TeamAbbreviation;

create table RegularParticipant
    (UserName char(20) not null,
    UserPassword char(20) not null,
    DateJoined char(20) not null,
    primary key (UserName));

grant select on RegularParticipant to public;

create table LeagueCommissioner
    (UserName char(20) not null,
    UserPassword char(20) not null,
    DateJoined char(20) not null,
    primary key (UserName));

grant select on LeagueCommissioner to public;

create table NBAPlayer
    (PlayerNumber int,
    NBATeam char(20),
    Position char(20) not null,
    Points int not null,
    PlayerName char(20) not null,
    primary key (NBATeam, PlayerNumber) );

grant select on NBAPlayer to public;

create table NBAGame
    (FinalScore char(20) not null,
    DatePlayed char(20) not null,
    CompetingTeams char(20) not null,
    primary key (DatePlayed, CompetingTeams));

grant select on NBAGame to public;

create table PlayerInGame
    (PlayerTeam char(20),
    PlayerNumber int,
    CompetingTeams char(20) not null,
    GameDate char(20) not null,
    foreign key (PlayerTeam,PlayerNumber) references NBAPlayer(NBATeam,PlayerNumber) ON DELETE CASCADE,
    foreign key (CompetingTeams,GameDate) references NBAGame(CompetingTeams,DatePlayed) ON DELETE CASCADE,
    primary key (PlayerTeam, PlayerNumber, CompetingTeams, GameDate));

grant select on PlayerInGame to public;

create table League
    (ManagedBy char(20) not null,
    LeagueID int not null,
    LeagueName char(20) not null,
    primary key (LeagueID),
    foreign key (ManagedBy) references LeagueCommissioner(UserName) ON DELETE CASCADE);

grant select on League to public;

create table Team
    (TeamName char(20) not null,
    TotalPoints int not null,
    TeamID int not null,
    League int not null,
    primary key (TeamID),
    foreign key (League) references League(LeagueID) ON DELETE CASCADE);

grant select on Team to public;

create table TeamAbbreviation
    (TeamName char(20) not null,
    AbbrevName char(20) not null,
    primary key (TeamName));

grant select on TeamAbbreviation to public;

create table Trade
    (TradeID int not null,
    TeamID1 int not null,
    TeamID2 int not null,
    Player1Number int,
    Player2Number int,
    Player1Team char(20),
    Player2Team char(20),
    Status char(20) not null,
    TradeDate char(20) not null,
    foreign key (Player1Team,Player1Number) references NBAPlayer(NBATeam,PlayerNumber) ON DELETE CASCADE,
    foreign key (Player2Team,Player2Number) references NBAPlayer(NBATeam,PlayerNumber) ON DELETE CASCADE,
    foreign key (TeamID1) references Team(TeamID) ON DELETE CASCADE,
    foreign key (TeamID2) references Team(TeamID) ON DELETE CASCADE,
    primary key (TradeID));

grant select on Trade to public;

create table TeamOwnedBy
    (TeamID int not null,
    LeagueParticipantID char(20),
    primary key (TeamId, LeagueParticipantID),
    foreign key (TeamID) references Team(TeamID) ON DELETE CASCADE,
    foreign key (LeagueParticipantID) references RegularParticipant(UserName) ON DELETE CASCADE);

grant select on TeamOwnedBy to public;

create table PlayersInTeam
    (PlayerNumber int,
    PlayerTeam char(20),
    TeamID int not null,
    foreign key (TeamID) references Team(TeamID) ON DELETE CASCADE,
    foreign key (PlayerNumber,PlayerTeam) references NBAPlayer(PlayerNumber,NBATeam) ON DELETE CASCADE,
    primary key (PlayerNumber, PlayerTeam, TeamID) );

grant select on PlayersInTeam to public;

create table LeagueLogo
    (ManagedBy char(20),
    Logo char(100),
    primary key (ManagedBy),
    foreign key (ManagedBy) references LeagueCommissioner(UserName) ON DELETE CASCADE);

grant select on LeagueLogo to public;

create table StartingLineup
    (LineupID int,
    TeamID int,
    primary key (LineupID),
    foreign key (TeamID) references Team ON DELETE CASCADE);

grant select on StartingLineup to public;

create table LineupPlayers
    (PlayerNumber int,
    PlayerTeam char(20),
    LineupID int,
    primary key (PlayerNumber, PlayerTeam, LineupID),
    foreign key (LineupID) references StartingLineup(LineupID) ON DELETE CASCADE,
    foreign key (PlayerNumber,PlayerTeam) references NBAPlayer(PlayerNumber,NBATeam) ON DELETE CASCADE);

grant select on LineupPlayers to public;

insert into RegularParticipant
values('Victor','hello123','2020-02-28');

insert into RegularParticipant
values('Zach','passwordlol','2020-02-20');

insert into RegularParticipant
values('Kareem','ilovecpen','2020-02-10');

insert into RegularParticipant
values('Jess','ilovedatabases','2020-02-26');

insert into RegularParticipant
values('Kevin','bruh100','2020-02-21');

insert into LeagueCommissioner
values('Jeremy','wootwoot20','2020-02-01');

insert into LeagueCommissioner
values('Melissa','letsgetit','2020-02-02');

insert into LeagueCommissioner
values('Grant','imthebest','2020-02-03');

insert into LeagueCommissioner
values('Hue','passpass','2020-02-04');

insert into LeagueCommissioner
values('Roger','wordpass','2020-02-05');

insert into NBAPlayer
values(7,'Raptors','Point Guard', 0, 'Kyle Lowry');

insert into NBAPlayer
values(23,'Raptors','Point Guard', 0, 'Fred VanVleet');

insert into NBAPlayer
values(3,'Raptors','Small Forward', 10, 'OG Anunoby');

insert into NBAPlayer
values(3,'Lakers','Power Forward', 50, 'Anthony Davis');

insert into NBAPlayer
values(9,'Lakers','Point Guard', 6, 'Rajon Rondo');

insert into NBAPlayer
values(2,'Clippers','Small Forward', 28, 'Kawhi Leonard');

insert into NBAPlayer
values(0,'Rockets','Point Guard', 40, 'Russell Westbrook');

insert into NBAPlayer
values(25,'76ers','Point Guard', 12, 'Ben Simmons');

insert into NBAPlayer
values(77,'Mavericks','Small Forward', 37, 'Luka Doncic');

insert into NBAPlayer
values(3,'Thunder','Point Guard', 15, 'Chris Paul');

insert into NBAPlayer
values(27,'Jazz','Center', 22, 'Rudy Gobert');

insert into NBAGame
values('1-0','2020-01-01', 'LAL,TOR');

insert into NBAGame
values('50-82','2020-01-02', 'LAL,TOR');

insert into NBAGame
values('1000-50','2020-01-03', 'LAL,TOR');

insert into NBAGame
values('50-51','2020-01-04', 'LAL,TOR');

insert into NBAGame
values('100-200','2020-01-05', 'LAL,TOR');

insert into TeamAbbreviation
values('UBC Thunderbirds', 'UBC');

insert into TeamAbbreviation
values('Fred VanGOAT', 'FVG');

insert into TeamAbbreviation
values('Kawhis Laugh', 'KWL');

insert into TeamAbbreviation
values('Gods Plan', 'GPL');

insert into TeamAbbreviation
values('RIP Kobe', 'RIP');

insert into TeamAbbreviation
values('2K20 Champ', '2KC');

insert into TeamAbbreviation
values('Cereal Bar', 'CBR');

insert into TeamAbbreviation
values('Russell WestBRICK', 'RWB');

insert into TeamAbbreviation
values('BenSimmonsCantShoot3', 'BS3');

insert into PlayerInGame
values('Raptors', 7, 'LAL,TOR', '2020-01-01');

insert into PlayerInGame
values('Lakers', 9, 'LAL,TOR', '2020-01-02');

insert into PlayerInGame
values('Lakers', 3, 'LAL,TOR', '2020-01-03');

insert into PlayerInGame
values('Raptors', 3, 'LAL,TOR', '2020-01-04');

insert into PlayerInGame
values('Raptors', 23, 'LAL,TOR', '2020-01-05');

insert into League
values('Jeremy', 1, 'basketball!');

insert into League
values('Melissa', 2, 'A fun league');

insert into League
values('Grant', 3, 'Not hockey');

insert into League
values('Hue', 4, '100DollarEntranceFee');

insert into League
values('Roger', 5, 'What it do babyyy');

insert into LeagueLogo
values('Jeremy', 'https://content.sportslogos.net/news/2017/12/2017-Creamer-Awards-Logo.png');

insert into LeagueLogo
values('Melissa', 'https://content.sportslogos.net/logos/34/858/full/2647.png');

insert into LeagueLogo
values('Grant', 'https://content.sportslogos.net/news/2017/12/2017-Creamer-Awards-Logo.png');

insert into LeagueLogo
values('Hue', 'https://content.sportslogos.net/logos/178/6000/full/4636_savannah_bananas-primary-2016.png');

insert into LeagueLogo
values('Roger', 'https://usatftw.files.wordpress.com/2014/07/atlanta-falcons.png?w=1000');

insert into Team
values('UBC Thunderbirds', 0, 100, 1);

insert into Team
values('Fred VanGOAT', 50, 200, 1);

insert into Team
values('Kawhis Laugh', 40, 300, 1);

insert into Team
values('Gods Plan', 12, 400, 2);

insert into Team
values('RIP Kobe', 22, 500, 2);

insert into Team
values('2K20 Champ', 0, 600, 3);

insert into Team
values('Cereal Bar', 0, 700, 5);

insert into Team
values('Russell WestBRICK', 0, 800, 5);

insert into Team
values('BenSimmonsCantShoot3', 28, 900, 2);

insert into Trade
values(1, 100, 200, 7, 3, 'Raptors', 'Lakers', 'Pending', '2020-03-09');

insert into Trade
values(2, 200, 300, 3, 0, 'Lakers', 'Rockets', 'Pending', '2020-03-10');

insert into Trade
values(3, 500, 400, 27, 25, 'Jazz', '76ers', 'Denied', '2020-03-11');

insert into Trade
values(4, 100, 300, 7, 0, 'Raptors', 'Rockets', 'Denied', '2020-03-12');

insert into Trade
values(5, 400, 900, 25, 2, '76ers', 'Clippers', 'Pending', '2020-03-13');

insert into TeamOwnedBy
values(100, 'Victor');

insert into TeamOwnedBy
values(200, 'Zach');

insert into TeamOwnedBy
values(300, 'Kareem');

insert into TeamOwnedBy
values(400, 'Jess');

insert into TeamOwnedBy
values(500, 'Kevin');

insert into PlayersInTeam
values(7, 'Raptors', 100);

insert into PlayersInTeam
values(3, 'Lakers', 200);

insert into PlayersInTeam
values(0, 'Rockets', 300);

insert into PlayersInTeam
values(25, '76ers', 400);

insert into PlayersInTeam
values(27, 'Jazz', 500);

insert into PlayersInTeam
values(2, 'Clippers', 900);

insert into StartingLineup
values(1001, 100);

insert into StartingLineup
values(1002, 200);

insert into StartingLineup
values(1003, 300);

insert into StartingLineup
values(1004, 400);

insert into StartingLineup
values(1005, 500);

insert into StartingLineup
values(1006, 600);

insert into StartingLineup
values(1007, 700);

insert into StartingLineup
values(1008, 800);

insert into StartingLineup
values(1009, 900);

insert into LineupPlayers
values(7, 'Raptors', 1001);

insert into LineupPlayers
values(3, 'Lakers', 1002);

insert into LineupPlayers
values(0, 'Rockets', 1003);

insert into LineupPlayers
values(25, '76ers', 1004);

insert into LineupPlayers
values(27, 'Jazz', 1005);

insert into LineupPlayers
values(2, 'Clippers', 1009);
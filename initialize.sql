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
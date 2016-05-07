
## SELECT real total points for each Pitcher
UPDATE `pitcher_stats` JOIN players ON players.player_id = pitcher_stats.player_id SET `total_score`= ((`innings_pitched`*2.25)+(`strikeouts`*2)+(`win_result`*4)+(`earned_runs`*-2)+(`hits`*-.6)+(`walks`*-.6)) WHERE players.position like '%P%'

## SELECT real total points for each non-pitcher
UPDATE `pitcher_stats` JOIN players ON players.player_id = pitcher_stats.player_id  SET `total_score`= (((`hits`-`double_hit`-`triple_hit`)*3)+(`double_hit`*5)+(`triple_hit`*8)+(`home_runs`*10)+(`rbi`*2.25)+(`runs`*2.25)+(`walks`*2)+(`rbi`*2.25)+(`stolen_bases`*5)) WHERE players.position NOT LIKE '%P%'

## SELECT average real total points for all players
SELECT player_name, position, round(a.total_sum/a.game_count) AS average_total_points FROM players, (SELECT COUNT(*) AS game_count, player_id, SUM(total_score) AS total_sum FROM pitcher_stats GROUP BY player_id) a WHERE players.player_id = a.player_id

## Select average total points for pitcher verse opponent
SELECT ((sum(total_score))/count(*)) as average_point, sum(total_score) AS points, count(*) as game_count, opponent FROM players, pitcher_stats WHERE players.player_id = pitcher_stats.player_id AND position LIKE '%P%' GROUP BY pitcher_stats.opponent ORDER BY `average_point` DESC

## SELECT total points per team FROM pitcher_stats
SELECT sum(total_score), player_name, position, team FROM pitcher_stats, players WHERE players.player_id = pitcher_stats.player_id GROUP BY players.player_id ORDER BY team

##Select pitching points accumulated against a certain team
UPDATE team JOIN (SELECT ((sum(total_score))/count(*)) as hitting_strength_against_pitchers, opponent FROM players, pitcher_stats WHERE players.player_id = pitcher_stats.player_id AND position LIKE '%P%' GROUP BY pitcher_stats.opponent) a
ON team.nickname = a.opponent
SET team.hitting_strength = a.hitting_strength_against_pitchers

##Select hitting points accumulated against a certain team
UPDATE team JOIN (SELECT ((sum(total_score))/count(*)) as pitching_strength_against_hitters, opponent FROM players, pitcher_stats WHERE players.player_id = pitcher_stats.player_id AND position NOT LIKE '%P%' GROUP BY pitcher_stats.opponent) a
ON team.nickname = a.opponent
SET team.pitching_strength = a.pitching_strength_against_hitters

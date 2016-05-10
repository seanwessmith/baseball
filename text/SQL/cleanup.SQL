## SELECT Players not in players but in dk_main, if so run find_unknown.php
SELECT a.* FROM dk_main a LEFT JOIN players b on a.name = b.player_name WHERE b.player_name IS NULL

## SELECT duplicate pitcher_stats
SELECT player_id, game_date, count(*) FROM `pitcher_stats` GROUP BY game_date, player_id ORDER BY count(*) DESC


                                    *********DELETION STATEMENTS*********

## Remove duplicate players
DELETE players.* from players left join (select max(player_name), player_id from players group by player_name) a on a.player_id = players.player_id WHERE a.player_id is NULL

## Remove duplicate FROM pitcher_stats
DELETE pitcher_stats.* FROM `pitcher_stats` left join (SELECT max(pitcher_id) AS pitcher_id FROM pitcher_stats GROUP BY game_date, player_id) a ON a.pitcher_id = pitcher_stats.pitcher_id WHERE a.pitcher_id IS NULL

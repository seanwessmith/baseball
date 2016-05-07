## SELECT Players FROM mASter databASe and connect to dk.csv || 263 Pitchers should be total
SELECT players.player_id FROM bASeball_1461521597, players WHERE players.player_name = bASeball_1461521597.name GROUP BY players.player_id

## SELECT Players not in mASter databASe but in dk.csv
SELECT a.* FROM dk_main a LEFT JOIN players b on a.name = b.player_name WHERE b.player_name IS NULL AND a.position like '%P%'

## SELECT duplicate pitcher_stats
SELECT player_id, game_date, count(*) FROM `pitcher_stats` GROUP BY game_date, player_id ORDER BY count(*) DESC


                                    *********DELETION STATEMENTS*********

## Remove duplicate players
DELETE players.* from players left join (select max(player_name), player_id from players group by player_name) a on a.player_id = players.player_id WHERE a.player_id is NULL

## Remove duplicate FROM pitcher_stats
DELETE pitcher_stats.* FROM `pitcher_stats` left join (SELECT max(pitcher_id) AS pitcher_id FROM pitcher_stats GROUP BY game_date, player_id) a ON a.pitcher_id = pitcher_stats.pitcher_id WHERE a.pitcher_id IS NULL

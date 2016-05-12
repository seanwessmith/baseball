How to Run:

index.php - Shows the best team given the salary cap

1. find_unknown      - Run this first to add new players from dk_main.
2. daily_refresh  - Updates players already in the players database. Also run after running


Accomplishments:

1. Currently have all pitchers in database that are included in DraftKings CSV sheet.

2. Currently have ESPN ID's for all pitchers, this allows refreshing of data. (need to build cron refresh)

3. Currently have game data for 166/263 pitchers in DraftKings CSV sheet.

4. Can run index.php to build the best team given a given salary and player points.

5. Calculated average hitting points and pitching points against a team


------------------------------------------------------------------------------------------------------------------------------------------------
Future Additions:

************IN PROGRESS************
x. Merge dk_main with players
***********************************

x. Don't update players.player_name after adding from dk_main

x. Find std deviation before and after applying opponent difficulty to total_score

x. Add options to index.php that toggle team building algorithms
    x. opponent team plus or minus
    x. home or away plus or minus

x. Navigate DraftKings and submit drafts automatically

x. Only show probable pitchers in index.php (http://mlb.mlb.com/news/probable_pitchers/)

x. Need to have cron job that runs p_update.php daily

x. Implement a queue to process large updates faster

x. Upload to AWS

x. Add cron job to refresh database from this link, needs to reference a schedule so it refreshes only updated teams.
   https://www.draftkings.com/lineup/getavailableplayerscsv?contestTypeId=21&draftGroupId=8129


543

How to Run:

index.php - Shows the best team given the salary cap

p_espn_id - grabs players in dk_main that aren't in players and adds them to players complete with their ESPN id
p_update  - updates players already in the players database
p_unknown - fetches the ESPN_ID of a player from the DraftKings CSV sheet


Accomplishments:

1. Currently have all pitchers in database that are included in DraftKings CSV sheet.

2. Currently have ESPN ID's for all pitchers, this allows refreshing of data. (need to build cron refresh)

3. Currently have game data for 166/263 pitchers in DraftKings CSV sheet.

4. Can run index.php to build the best team given a given salary and player points.

5. Calculated average hitting points and pitching points against a team

------------------------------------------------------------------------------------------------------------------------------------------------
Future Additions:

************IN PROGRESS************
x. https://www.draftkings.com/lineup/getavailableplayerscsv?contestTypeId=28&draftGroupId=9716 - Doesn't generate the best lineup???
***********************************

x.

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

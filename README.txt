How to Run:

index.php - Shows the best team given the salary cap

p_fetch   - fetches new players from ESPN's website
p_update  - updates players already in the players database
p_unknown - fetches the ESPN_ID of a player from the DraftKings CSV sheet


Accomplishments:

1. Currently have all pitchers in database that are included in DraftKings CSV sheet.

2. Currently have ESPN ID's for all pitchers, this allows refreshing of data. (need to build cron refresh)

3. Currently have game data for 166/263 pitchers in DraftKings CSV sheet.

4. Can run index.php to build the best team given a given salary and player points. Need to improve how players are rated, "The Master Algorithm".

------------------------------------------------------------------------------------------------------------------------------------------------
Future Additions:

x. Need to have cron job that runs p_update.php daily

x. Upload to AWS

x. Add cron job to refresh database from this link, needs to reference a schedule so it refreshes only updated teams.
   https://www.draftkings.com/lineup/getavailableplayerscsv?contestTypeId=21&draftGroupId=8129

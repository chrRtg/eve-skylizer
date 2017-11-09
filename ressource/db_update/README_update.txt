
## to be imported

invCategories
invGroups
invMarketGroups
invTypes
invTypeMaterials
mapDenormalize
mapLocationWormholeClasses
mapSolarSystemJumps
trnTranslations

## download them from Steve Ronuken
wget https://www.fuzzwork.co.uk/dump/latest/invCategories.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invGroups.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invMarketGroups.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invTypes.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invTypeMaterials.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapDenormalize.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapLocationWormholeClasses.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapSolarSystemJumps.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/trnTranslations.sql.bz2

## then unzip and import to your DB
bunzip *.bz2
cat *.sql | mysql -u root -p skylizer
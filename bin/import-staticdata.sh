#!/bin/bash

#
# Script to import latest EVE static data from Fuzzworks and import it to a mysql database
# It's part of skŸlizer but it might be usefull for other applications too.
# 
# @author: chrRtg 
# contact EVE ingame "Rtg Quack"
# @see: https://github.com/chrRtg/eve-skylizer
#
# some basic code from https://github.com/MagePsycho/mysql-user-db-creator-bash-script

export LC_CTYPE=C
export LANG=C

BIN_MYSQL=$(which mysql)

####################################
# application VARIABLES
####################################

# name, user and password input also via commandline parameters
DB_HOST='localhost'
DB_NAME=
DB_USER=
DB_PASS=

TMP_DIR='../data'

# which tables to import
FUZZWORK="
invCategories
invGroups
invMarketGroups
invTypes
invTypeMaterials
mapDenormalize
mapLocationWormholeClasses
mapSolarSystemJumps
trnTranslations
"

# SQL to be executed after a successful import
POSTSQL="
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE invCategories ADD INDEX idx_cat_name (categoryName ASC);
ALTER TABLE invGroups ADD INDEX idx_ig_cid (categoryID ASC), ADD INDEX idx_ig_gname (groupName ASC);
ALTER TABLE invMarketGroups ADD INDEX idx_mg_parent (parentGroupID ASC), ADD INDEX idx_mg_name (marketGroupName ASC);
ALTER TABLE invTypes ADD INDEX idx_it_group (groupID ASC), ADD INDEX idx_it_name (typeName ASC),ADD INDEX idx_it_mgroup (marketGroupID ASC);
ALTER TABLE invTypeMaterials ADD INDEX idx_typeid (typeID ASC), ADD INDEX idx_mtypeid (materialTypeID ASC);
ALTER TABLE trnTranslations ADD INDEX idx_tt_tcid (tcID ASC), ADD INDEX idx_tt_kid (keyID ASC), ADD INDEX idx_tt_text (text(20) ASC), ADD INDEX idx_tt_lang (languageID ASC);
ALTER TABLE mapDenormalize  ADD INDEX md_IX_name (itemName(15) ASC), ADD INDEX md_IX_groupid (groupID ASC), ADD INDEX md_IX_typeid (typeID ASC);
ALTER TABLE mapSolarSystemJumps ADD INDEX IDX_fromSolar (fromSolarSystemID ASC), ADD INDEX IDX_toSolar (toSolarSystemID ASC);
SET FOREIGN_KEY_CHECKS = 1;
"

####################################
# core VARIABLES
####################################
_lastimport=0
_currimport=0
_requireupdate=0
_forceupdate=0

_bold=$(tput bold)
_underline=$(tput sgr 0 1)
_reset=$(tput sgr0)

_purple=$(tput setaf 171)
_red=$(tput setaf 1)
_green=$(tput setaf 76)
_tan=$(tput setaf 3)
_blue=$(tput setaf 38)

#
# HEADERS & LOGGING
#
function _debug()
{
    [ "$DEBUG" -eq 1 ] && $@
}

function _header()
{
    printf "\n${_bold}${_purple}==========  %s  ==========${_reset}\n" "$@"
}

function _arrow()
{
    printf "➜ $@\n"
}

function _success()
{
    printf "${_green}✔ %s${_reset}\n" "$@"
}

function _error() {
    printf "${_red}✖ %s${_reset}\n" "$@"
}

function _warning()
{
    printf "${_tan}➜ %s${_reset}\n" "$@"
}

function _underline()
{
    printf "${_underline}${_bold}%s${_reset}\n" "$@"
}

function _bold()
{
    printf "${_bold}%s${_reset}\n" "$@"
}

function _note()
{
    printf "${_underline}${_bold}${_blue}Note:${_reset}  ${_blue}%s${_reset}\n" "$@"
}

function _die()
{
    _error "$@"
    _cleanUpOnExit
    exit 1
}

function _safeExit()
{
    _cleanUpOnExit
    exit 0
}

function _executeCommand() 
{
    eval $1;
    
    ERR=$?
    if [ $ERR -ne 0 ] ; then
        # I've got an error
        if [[ -z $2 ]]; then
            _die "The command '$1' failed with exit code $ERR"
        else
            _die "$2 with exit code $ERR"
        fi
    fi
}

function _printUsage()
{
    echo -n "$(basename $0) [OPTION]...
Import EVE static files from fuzzwork to MySql.

    Options:
        -h, --host        MySQL Host [default 'localhost']
        -d, --database    MySQL Database
        -u, --user        MySQL User
        -p, --pass        MySQL Password (If empty, script will ask you)
        -f, --force       Force update regardless if you have the most current data
        -h, --help        Display this help and exit

    Examples:
        $(basename $0) --help
        $(basename $0) [--host=\"<host-name>\"] --database=\"<db-name>\" [--user=\"<db-user>\"] [--pass=\"<user-password>\"]
"
    _safeExit
}

function processArgs()
{
    # Parse Arguments
    for arg in "$@"
    do
        case $arg in
            -h=*|--host=*)
                DB_HOST="${arg#*=}"
            ;;
            -d=*|--database=*)
                DB_NAME="${arg#*=}"
            ;;
            -u=*|--user=*)
                DB_USER="${arg#*=}"
            ;;
             -p=*|--pass=*)
                DB_PASS="${arg#*=}"
            ;;
            -f|--force)
                _forceupdate=1
            ;;
            -h|--help)
                _printUsage
            ;;
            *)
                _warning "Unknown parameter '$arg'"
                _printUsage
            ;;
        esac
    done

    if [[ -z $DB_PASS ]]; then
        _warning "please enter your database password:"
        read -s DB_PASS
    fi

    [[ -z $DB_PASS ]] && _error "The password cannot be empty." && exit 1
    [[ -z $DB_NAME ]] && _error "Database name cannot be empty." && exit 1
    [[ -z $DB_USER ]] && _error "Database user is required." && exit 1
}

function createTmpDir()
{
    if [ ! -d $TMP_DIR/fuzzwork_import ]; then
        _executeCommand "mkdir $TMP_DIR/fuzzwork_import" "can not create directory in '$TMP_DIR'"
    fi
}

function _cleanUpOnExit()
{
    if [ -d $TMP_DIR/fuzzwork_import ]; then
        rm -Rf $TMP_DIR/fuzzwork_import
    fi
}


function checkMysql
{
    if [[ -z $BIN_MYSQL ]]; then
        _die "no MySQL binary found"
    fi
}

function checkMysqlConnection()
{
    _executeCommand "$BIN_MYSQL --user=\"$DB_USER\" --password=\"$DB_PASS\" --host=\"$DB_HOST\" -e 'select version();'" "can not connect to mysql with the given credentials"
}

function checkMysqlDB()
{
    _executeCommand "$BIN_MYSQL --user=\"$DB_USER\" --password=\"$DB_PASS\" --host=\"$DB_HOST\" $DB_NAME -e 'use $DB_NAME;'" "can not find MySQL database '$DB_NAME' "
}

function printSuccessMessage()
{
    _success "congrats, your EVE static data is now up to date"
    _success "fly safe capsuleer"
}

# read a last modified date of a ressource and compare against a locally stored value
# if remove versin is newer a variable is set from 0 to 1
function checkUpdate()
{
    # read timestamp of some smaller file on fuzzworks into variable _lastimport
    _executeCommand "export _currimport=$(wget --server-response --spider https://www.fuzzwork.co.uk/dump/latest/invCategories.sql.bz2 2>&1 | grep -i Last-Modified | cut -c 18- | date -f  - +%s)" "can not fetch date of last update"

    if [ -s $TMP_DIR/fuzzwork_last.txt ]; then
        _lastimport=$(< $TMP_DIR/fuzzwork_last.txt)
    fi

    if [ "$_currimport" -gt "$_lastimport" ]; then
        _requireupdate=1
        _warning "Fuzzwork has newer files available"
    elif [ "$_forceupdate" -eq 1 ]; then
        _requireupdate=1
        _warning "FORCE update mode"
    fi
}

function wgetFuzzworks()
{
    _warning "download EVE tables from fuzzwork..."
    for TABLE in $FUZZWORK
    do
        _executeCommand "wget -nv -O $TMP_DIR/fuzzwork_import/$TABLE.sql.bz2 https://www.fuzzwork.co.uk/dump/latest/$TABLE.sql.bz2" "failed to download EVE data files from fuzzworks"
    done
}

function importFuzzworks()
{
    _warning "Import fuzzwork data to MySQL..."
    for filename in $TMP_DIR/fuzzwork_import/*.bz2; do
        _executeCommand "bzcat $filename | $BIN_MYSQL --user=\"$DB_USER\" --password=\"$DB_PASS\" --host=\"$DB_HOST\" $DB_NAME" "failed to import files from fuzzworks to db '$DB_NAME' "
        _success "   '$filename' imported"
    done
}

function postFixDatabase()
{
    _warning "postFixDatabase..."
    _executeCommand "$BIN_MYSQL --user=\"$DB_USER\" --password=\"$DB_PASS\" --host=\"$DB_HOST\" $DB_NAME --execute='$POSTSQL'" "can not find MySQL database '$DB_NAME' "

}

################################################################################
# Main
################################################################################

function main()
{
    cd "$(dirname "$0")"

   [[ $# -lt 1 ]] && _printUsage

    # MySQL binay existing / MySQL installed?
    checkMysql
    _success "MySQL binary found"

    # read commandline
    processArgs "$@"

    checkMysqlConnection
    _success "MySQL connection established"

    checkMysqlDB
    _success "MySQL database is existing"

    createTmpDir
    _success "temporary directory created"

    checkUpdate

    if [ $_requireupdate -eq 0 ]; then
        _warning "no update neccessary, you already have the most current data"
        _safeExit
    fi

    wgetFuzzworks
    _success "data from fuzzworks successful downloaded"

    importFuzzworks
    _success "data imported to your MySql database"

    postFixDatabase
    _success "MySQL POSTSQL database post processed"

    printSuccessMessage

    # store last modification date of fuzzwork data to file for later checks
    echo "$_currimport" > "$TMP_DIR/fuzzwork_last.txt"

    _safeExit
}

main "$@"
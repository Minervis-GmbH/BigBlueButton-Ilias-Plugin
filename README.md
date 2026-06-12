Forked from https://sourceforge.net/projects/bigbluebuttonil/

# About

This is a [ILIAS](https://www.ilias.de) Plugin which allows the creation of [BigBlueButton](https://bigbluebutton.org) Virtual Classrooms inside of ILIAS.

# Prerequisites

## BBB 

At first you need a working installation of BigBlueButton http://docs.bigbluebutton.org/.  
The following data from BBB is required to configure this ILIAS Plugin:

- public/private URL: e.g. `https://bbb.myserver.com:9000/bigbluebutton/`
- BBB security salt

**Note:** To use this plugin you need at least BigBlueButton version 2.3 or higher.

## ILIAS

It is assumed you already have a ILIAS Intallation [up and running](https://docu.ilias.de/go/pg/220443_367).

# Installation

### ILIAS  10.x

On your ILIAS Server:

**Note:** it is assumed that you'already in ILIAS web root directory

- `mkdir -p public/Customizing/global/plugins/Services/Repository/RepositoryObject`
- `cd public/Customizing/global/plugins/Services/Repository/RepositoryObject`
- `git clone https://github.com/Minervis-GmbH/BigBlueButton-Ilias-Plugin.git BigBlueButton/`
- log in to ILIAS as `administrator` and go to the administration page
- go to `Administration>Extending ILIAS>Plugins`
- look for BigBlueButton from the list of plugins and click a drop down button
- install and activate
- now hit the `configure` link and enter your 
    - public or private server URL : {PROTOCOL}://{SERVER_ADDRESS}{:PORT}/{BBB_SUBPATH}/. Where 
        - {PROTOCOL}: https
        - {SERVER_ADDRESS}: The Server adress
        - {:PORT}: the port Eg.: 9003
        - {BBB_SUBPATH}: The subpath of BBB Server. Eg.: /bigbluebutton/
        - E.g:  https://my.bbb-server.com:9003/bigbluebutton
        
    - BBB security salt
    - Choose, if record the session should be allowed, or not
- finally you can create "Bigbluebutton" Virtual Classrooms as regular repository object
**Note:** If the server is not reachable, during configuration a message will be displayed. After correcting the error, make sure to press the save button twice.
  





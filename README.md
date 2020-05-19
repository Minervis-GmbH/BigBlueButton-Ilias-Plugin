Forked from https://sourceforge.net/projects/bigbluebuttonil/

# About

This is a [ILIAS](https://www.ilias.de) Plugin which allows the creation of [BigBlueButton](https://bigbluebutton.org) Virtual Classrooms inside of ILIAS.

# Prerequisites

## BBB 

At first you need a working installation of BigBlueButton http://docs.bigbluebutton.org/.  
The following data from BBB is required to configure this ILIAS Plugin:

- public/private URL: e.g. `https://bbb.myserver.com:9000/bigbluebutton/`
- BBB security salt

## ILIAS

It is assumed you already have a ILIAS Intallation [up and running](https://docu.ilias.de/goto_docu_pg_116903_367.html).

# Installation

### ILIAS <= 5.2

On your ILIAS Server:

- copy the content of this folder into `<ILIAS_directory>/Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton`
- log in to ILIAS as `root` and go to the administration page
- select `Modules, Services and Plugins` in the menu on the right
- hits the `Services` tab and ILIAS will show a service list
- look for the `Repository` and hit the `Show Details` link that is located next to the title
- look for the BigBlueButton plugin on the table and hit the `update` link
- when ILIAS has updated the plugin, hit the `activate` link that will appear instead of the `update` link
- now hit the `configure` link and enter your 
    - public/private URL
    - public/private port 
    - BBB security salt
- finally you can create "Big Blue Button" Virtual Classrooms as regular repository object

### ILIAS 5.3, 5.4

On your ILIAS Server:

**Note:** it is assumed that you'already in ILIAS web root directory

- `mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject`
- `cd Customizing/global/plugins/Services/Repository/RepositoryObject`
- `git clone https://github.com/Minervis-GmbH/BigBlueButton-Ilias-Plugin.git `
- `mv BigBlueButton-Ilias-Plugin/ BigBlueButton/`
- `cd BigBlueButton`
- `rm -rf .git`
- log in to ILIAS as `root` and go to the administration page
- select `Plugins` in the menu on the right
- look for BigBlueButton from the table of plugins and click a drop down button
- install and activate
- now hit the `configure` link and enter your 
    - public/private URL
    - public/private port 
    - BBB security salt
- finally you can create "Big Blue Button" Virtual Classrooms as regular repository object


# Compatibility

Tested with ILIAS

- 4.3.1
- 5.0.x
- 5.1.2
- 5.2.1
- 5.3.16
- 5.4.6




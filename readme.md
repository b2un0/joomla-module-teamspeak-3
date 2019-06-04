## Joomla Teamspeak 3 Module

You must generate a query client login as ServerAdmin with your TS3 Client on "Extras" -> "ServerQuery Login" to use this module.

This module doesn't work on most free or low cost webspaces because they block all outgoing connections! Try www.tsviewer.com instead!

You can ask your Provider to open the following TeamSpeak3 ports

TCP: 10011 (Query Port to receive all information)
TCP: 30033 (File-Transfer for download of icons and avatars)
If you have any troubles with unapplied configurations clean the cache item "teamspeak3" on /administrator/index.php?option=com_cache

### Features
- displays full channel tree with icons
- displays all clients in each channel with icons
- displays selectable server statistics in own layout or under channel tree
- option to hide specified channel or client by id (or all clients)
- option to show client avatar in joomla tooltip
- option for join links on on all channels or only server name (with predefined nickname)
- option to replace module title with server name
- option to display only specified channel and all sub channels (for shared server)
- all custom icons and avatars are downloaded directly from your ts3 server

### Credits
Based on [Teamspeak 3 PHP Framework](https://github.com/planetteamspeak/ts3phpframework) - it was installed as native Joomla library - if you uninstall this module you should uninstall the library, too.
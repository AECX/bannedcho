# Bannedcho
Custom osu! Server

This Code is based on the <a href="https://github.com/osuripple/ripple/tree/master" target="_blank">Ripple</a> code!

## Getting started
### Recommends:
  We recommend using debian8 (or newer)
  We recommend using apache2 (especially because you can install it with debian installation)
  We recommend using phpmyadmin (just because you'll need to work with databases and this is way easier)
  We recommend using Bannedcho (Because we guarantee fast support and open source for lifetime!)

### Setting Up:
  Download this git as .zip and extract it to "/var/www/".
  Now enter "/var/www" and rename "bannedcho-master" to [YourServerName]
  using "mv bannedcho-master [YourServerName]".
  
  From now on "/bannedcho/" is equal to [YourServerName]
  
  Then change the rights of "/Bannedcho/"
  using "chmod -R 777 /var/www/Bannedcho/".
  This makes sure php etc. are allowed to write data (such as config.php or avatars [...]).
  
  Enter "/bannedcho" and use "./requirements.sh" to install (hopefully) everything nssecary
  in order to use bannedcho.
  
#### Apache2 configuration
  Go to "/etc/apache2/sites-available"
  Then create a file called "osu.ppy.sh*.conf*".
  Here is a sample for osu.ppy.sh.conf
> <VirtualHost *:80>
>   ServerName osu.ppy.sh
>   ServerAlias osu.bannedcho.ml
> </virtualServer>

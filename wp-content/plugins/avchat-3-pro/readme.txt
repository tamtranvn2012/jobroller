=== Video Chat Plugin ===
Tags: chat, video chat, chatroom, flash, flash video chat, flash chat, videochat, streaming, image, images, red5, fms, wowza, avchat
Requires at least: 3.0.0
Tested up to: 3.5
Stable tag: 1.3
Version : 1.3
Contributors: stefan.avchat
License: GPLv2 or later

The AVChat Video Chat Plugin for WordPress is designed to increase members engagement and web site revenues with a stunning video chat solution.

== Description ==

The Video Chat Plugin for WordPress handles the integration between AVChat and your WordPress website.

<a href='http://avchat.net/features' title='AVChat software'>AVChat</a> is an highly appreciated video chat script that can be hosted by you and installed on your website. You can find out <a href='http://avchat.net/features' title='AVChat software'>more about it</a> and purchase it from <a href='http://avchat.net/buy-now' title='Buy AVChat'>here</a>.

If you have AVChat than this Standard version of the plugin will take care of :

* username integration (users logged in the WordPress web site will not have to login again in the video chat )
* setting up the most important permissions/limits/features for each user role from WordPress admin area
* compatible with BuddyPress: BuddyPress avatars are automatically recognized and added to users profiles in the video chat (users profiles can be accessed directly from AVChat also)
* visitors are able to login with Facebook
* compatible with SEO friendly URL's in WordPress

The above features are for the Standard version which you can use to see the potential of the video chat solution on your site. You can download it for Free from this page.

> #### Get your  <a href='http://avchat.net/integrations/wordpress' title='AVChat plugin for WordPress'>PRO version </a>  now !!! .  
> Offering more advanced features, additional settings and permissions, free installation and 1 year of support and updates, the <a href='http://avchat.net/integrations/wordpress' title='AVChat Video Chat plugin for WordPress'>AVChat Video Chat plugin PRO</a> is the ultimate sollution for a perfect WordPress video chat. 

With the PRO version you get more than 20 new configuration options and permissions that you can customize for each user role. Also you get free installation and 1 year of full support and updates. And of course, our gratitude for helping us sustain this plugin and for the countless late-night hours that have been poured into development.

The AVChat Video Chat plugin for WordPress is licensed under GPL v2. See the full text of the GPL v2 license in license.txt.

> #***"Our users are very happy with the chat system and we see that more and more are using it every day... outstanding email support... quick to reply and always extremely helpful...easy to setup and reliable chat system"*** -
Mike Johnsen.  


== Installation ==
For this plugin to work, ***you first need to buy or get a trial of the AVChat Video Chat software*** from <a href='http://avchat.net/buy-now' title='Buy AVChat'>here</a>. This plugin only takes care of the integration with WordPress of the AVChat software, but does not contain the actual video chat.


After you obtain the AVChat archive from our site, follow these steps :

1. Connect to your web site hosting account using FTP
2. Upload the avchat3 folder (from avchat-3.zip) to your ***/wp-content/plugins*** folder
3. Upload the contents of the folder named Files to upload to your web site from the AVChat 3.0.zip archive to ***/wp-content/plugins/avchat-3/***
4. Chmod the ***/wp-content/plugins/avchat-3/uploadedFiles*** folder to 777 (otherwise the upload function might not work)
5. Create a new folder tokens (***/wp-content/plugins/avchat-3/tokens***) and CHMOD it to 777 (otherwise we might have token generation issues later on)
6.  Go to WordPress admin area and activate the plugin from the Plugins page
7.  Enter the rtmp connectionstring in Settings -> AVChat 3 (in the PRO version) . It should look like this:
	***rtmp://myFMSserver.com/avchat30/_definst_***
    where myFMSserver.com is the domain name or ip of the server where your media server is installed.
	Otherwise, got to the avc_settings.php file and look for the "connectionstring" property. There you should put the connection to your media server.
8.  Create a new page with this content: ***[chat]***
9.  Login the video chat, you will be asked for the license key (it's in your client/trial area on avchathq.com).
10. That's it you're logged in the AVChat video chat.

Get ***FREE*** installation with the <a href='http://avchat.net/integrations/wordpress' title='AVChat plugin for WordPress'> PRO version </a> of the plugin, plus much more features that you can customize to make the chat the way you want it.


For further information, see <a href='http://avchat.net/support/documentation/wordpress' title='Buy AVChat'>our detailed documentation</a>

== Frequently Asked Questions ==

= What do I need to use this plugin ? =

A WordPress web site and AVChat 3 ( http://avchat.net/ )

= Do I get support ? =

Yes, of course. If you've noticed a bug or a problem with the plugin, please report it to support@avchathq.com. 

Please take into consideration that ***PRO*** users receive priority support, but we will take into account all the emails. 

We also have a support forum: http://avchat.net/forum that is very useful in obtaining the information you desire. 


== Screenshots ==

1. Video chat with another person

2. Login Screen 

3. Create a new room

4. Video chat permissions 

5. Video chat settings

6. PRO vs Standard

7. PRO vs Standard

== Changelog ==

= 1.3 (14.12.2012 - build 1989) =
* ***!NEW***     HTML5 mobile version, now available for iOS and Android. 
 
= 1.2.1 (29.11.2012 - build 1941) =
* Added support to know when the AVChat files are not copied into the plugin directory.
* Fixed bug with javascripts missing.
* Removed padding in AVChat Settings in WordPress backend.
* More explicit texts in AVChat Settings in WordPress backend.
 
= 1.2 (27.11.2012 - build 1925) =
* Added Facebook integration.
* Added iPad detection.
* Added new "Visitors" column to better control what a visitor can have access to.
* Added lots of new features in WordPress backend.
* Now the changes made to the background are made from the style.css and not from the WordPress backend settings.
* Fixed the bug where guests can access the admin area of AVChat.
* Fixed the bug where the added user roles were not recognized by the AVChat and were not saved the changes made in WordPress backend.
 
= 1.1 (29.10.2012 - build 1850) =
* Fixed bug with "session already sent".
* Fixed the bug where IE didn't recognize the path to the video chat and couldn't log in.
* Updated the documentation.
 
= 1.0 (12.06.2012) =
* First release in WordPress plugin directory.
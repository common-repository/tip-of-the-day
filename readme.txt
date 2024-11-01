=== Tip of the Day ===
Contributors: G.Breant
Donate link: http://dev.pellicule.org/tip-of-the-day/#donate
Tags: BuddyPress,tip,tips,Random,polls,quotes,user tips
Requires at least: Wordpress 3
Tested up to: WP 3.0.1
Stable tag: 0.1

Tip of The Day is a plugin that display random tips, quotes, polls... for your users, in a widget.


== Description ==

Tip of The Day is a plugin that display random tips, quotes, polls... for your users, in a widget.

=Features=
* Widgetized
* You can make a poll of your tip.  The logged users will be able to answer to it and you'll see the results in the dashboard.  You can set custom answers.
* Uses custom post types and taxonomies, very easy to admin the tips.
* Ability for a logged user to hide a tip definitely.  It will no more appear to him.  When creating your tip, you can choose if it is hidable or not.
* Ajaxed.  You can refresh the tip, close it, answer to a poll or hide it definitely without leaving the page.

== Installation ==

= WordPress 3 and above = 

1. Check you have WordPress 3.0+
2. Download the plugin
3. Unzip and upload to plugins folder
4. Activate the plugin.

== Frequently Asked Questions ==
=How can I style my tips ?=
Use CSS !
There also are specific classes for your tips : 
-a poll tip gets the class "totd-question", 
-each tag given to a tip is used as class like "totd-tag-XXX" where XXX is the tag slug.
=How can I change how the tip query is made ?=
You can filter the query arguments with the filter "totd_the_tips_query_args" to modify the query arguments (show more than one tip, exclude some tips, ...)
=How can I display custom content (eg. PHP code) in the tip ?=
You can filter the content of the excerpt of a Wordpress post using the filters "the_content" and "the_excerpt".
Here's an example which uses the plugin BuddyPress Profile Progression to display a tip about how much the logged in user's profile is filled : http://pastie.org/1289904

== Screenshots ==
1. Example of a tip
2. Ajax links for a tip
3. Poll tip with the answer of the user checked

== Changelog ==

= 0.1 =
* First version

==Know Bugs ==
* Fatal error at the activation (function duplicated ?)
* Ajax functions not firing when using WP (it works with BP)

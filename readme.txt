=== Plugin Name ===
Contributors: webheadllc
Tags: developer, setup, plugins
Requires at least: 3.4.1
Tested up to: 3.5.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lets developers quickly set up a new Wordpress environment to their liking.

== Description ==

The goal of this plugin is to quickly setup a Wordpress site without having to go through all the normal Wordpress UI.  That means the UI is very limited and does not have a bunch of checkboxes and settings to go through.  Customization is done through coding actions and filters.

This plugin is meant for developers who set up Wordpress sites quite often.  After setting up so many sites, the same settings tend to be set and the same plugins tend to be used.  This plugin offers a way for developers to write some setup code once and reuse that code to setup all future Wordpress sites.

Some code from the "Developer" plugin developed in part by Automattic is used to activate and install plugins.  Simply enter some URLs and the plugin can be installed and activated.

Ideally you would place your actions and filters into a file that you can reuse and drop into themes.

= Usage =

* Write any actions or filters you want Quick WP Setup to run.

* Go to Tools->Quick WP Setup

* Enter any plugin urls you want installed and activated.

* Click "Proceed".


= Default Features =

* Default active widgets removed (ie recent-posts, recent-comments, archives, categories)

* Sample content (page and post) will be removed

* Default Wordpress settings will be geared towards a CMS instead of a blog.

* Empty menus will be created and set to the theme's registered menus

* Install plugins via urls.  

* Copy active plugin urls from one site and paste them into another site to quickly install the same plugins.

* A sample home page will be created and set as the front page.

= Things left to do =

* Build some simple checkboxes to quickly activate/deactivate features.

* Asynchronously download and install and activate plugins.

== Frequently Asked Questions ==

= Got a question, comment, request? =

Sorry, no.  If this plugin doesn't work on your first couple tries, deactivate it, delete it, and do whatever you want manually.  This is meant to be a "quick" plugin.


== Changelog ==

= 0.1 =
Initial release.


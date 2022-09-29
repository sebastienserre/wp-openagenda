=== OpenAgenda for WordPress ===
Contributors: sebastienserre
Tags: openagenda, agenda, events, calendar,
Requires at least: 5.5
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 2.6
Donate link: https://github.com/sponsors/sebastienserre/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

== Description ==
Display Events hosted in Openagenda.com easily in your WordPress Website by using shortcode, Gutenberg Blocks and Widget.

== Installation ==
* 1- unzip
* 2- upload to wp-content/plugin
* 3- Go to your dashboard to activate it
* 4- have fun!

== Frequently Asked Questions ==

= How to display an agenda in my website? =
On WP 5.0 Use the new Gutenberg Blocks!

* 1st get an Openagenda API Key in your Openagenda profile.
* 2nd use the [openwp_basic] with with params below to customize it.
* The param Agenda slug is mandatory. example: url='my-agenda-url(complete)'.
* The param nb is optional. Default value is 10. It will retrieve data for the \"nb\" events. example: nb=12
* The param lang is optional. Default value is en (english). It will retrieve data with the \"lang\" params (if exists). example: lang = \'fr\'

= Is this plugin created by Openagenda.com? =
* No! This plugin is developped by [Thivinfo.com](https://thivinfo.com)

= Do I need to be the owner of the Agenda?
* No! You can display all Agenda from [Openagenda.com](openagenda.com) by just copying the Agenda's url.

= May I display several Agenda?
* No!

= Where can I found help ?
*  You can use WordPress forum to find help.

= Where to report a bug ?
* Please open an isssue on my [Github](https://github.com/sebastienserre/wp-openagenda/issues)

= I'm using Elementor Page Builder, How to use OpenAgenda for Wordpress to display events?
* simply use the WordPress Widget, it will display your agenda on Elementor page Builder

= Is it possible to create an event from my website to the OpenAgenda?
* Yes!

= Is OpenAgenda for WordPress is working with WPBakery Page Builder (formerly Visual Composer) ?
* Yes!

= I use The Event Calendar from Modern Tribe, How to sync my events to OpenAgenda?
* Once the The Event Calendar option checked in OpenAgenda for WordPress, save your events, they will be created to
Openagenda.com
* If you already have events on OpenAgenda.com, please use the "Import" link in the Openagenda for WP settings. It will
create events in The Event Calendar.

== Screenshots ==
1. settings
2. display in front
3. Free WordPress Widget
4. Customer account embed.
5. Visual Composer Element
6. Display OpenAgenda widget in a great WP Widget
9. Masonry layout

== Changelog ==

= 2.6 -- 29 september 2022 =
- Remove use of ACF PRO (not allowed by wp.org).
- Recurrent events are no more available.

= 2.5 -- 27 February 2022 =
 - Support the OpenAgenda V2 API. Not really tested, please test on Staging / developement website.
 - Add a UID (field in the settings)
 - *STOP* The Event Calendar support.

= 2.1.7 -- 02 February 2022 =
 - New API by Openagenda not supported by this plugin. It will break. Please USE https://wordpress.org/plugins/openagenda/

= 2.1.5 & 2.1.6 -- 30 may 2021 =
 - Testing with latest WP version. All seems OK

= 2.1.4 -- 26 fev 2021 =
 - Fix an error (Thanks to Diego Curyk https://github.com/10goC)

= 2.1.3 -- 23 jan 2021 =
 - Correct file loading

= 2.1.1 & 2.1.2 -- 18 jan 2021 =
 - Correct 2 fatales errors :-/ (#9 & #10)

= 2.1.0 -- 17 jan 2021 =
 - Reorganize code.
 - Remove pro folders which has no reason to exist.

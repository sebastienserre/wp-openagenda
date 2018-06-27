=== OpenAgenda for WordPress ===
Contributors: sebastienthivinfocom
Tags: openagenda, events,
Donate link: https://www.paypal.me/sebastienserre
Requires at least: 4.6
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 1.2.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Display Events hosted in Openagenda.com easily in your WordPress Website.

== Description ==
Display Events hosted in Openagenda.com easily in your WordPress Website by using shortcode and Widget.
Shortly is planned to be produced Gutenberg Blocks and page builder elements.

== Installation ==
* 1- unzip
* 2- upload to wp-content/plugin
* 3- Go to your dashboard to activate it
* 4- have fun!

== Frequently Asked Questions ==
= How to display an agenda in my website? =
* 1st get an Openagenda API Key in your Openagenda profile.
* 2nd use the [openwp_basic] with with params below to customize it.
* The param Agenda slug is mandatory. example: slug=\'my-agenda-slug\'.
* The param nb is optional. Default value is 10. It will retrieve data for the \"nb\" events. example: nb=12
* The param lang is optional. Default value is en (english). It will retrieve data with the \"lang\" params (if exists). example: lang = \'fr\'
= Is this plugin created by Openagenda.com? =
* No! This plugin is developped by [Thivinfo.com](https://thivinfo.com)
= Do I need to be the owner of the Agenda?
* No! You can display all Agenda from [Openagenda.com](openagenda.com) by just copying the Agenda's slug.
= May I display several Agenda?
* Yes! (one by widget, shortcode)
= Is a Roadmap exist?
* No! No roadmap but Ideas:
    * Gutenberg Blocks,
    * Elementor Widget
    * Markdown parsing
= May I give Idea?
* Yes please use the [Github](https://github.com/sebastienserre/openagenda-wp/issues) issue


== Screenshots ==
1. settings
2. display in front

== Changelog ==

* 1.2.1 -- 27 june 2018
    Correct a bug if VC not activated

* 1.2.0 -- 27 june 2018
    Add a WPBakery Page Builder (Visual Composer) Element.
    Add Hooks
    Add CSS Class to allow custom style

* 1.1.0 -- 20 june 2018
    Add a Widget
    Correct text-domain

* 1.0.0 -- 18 june 2018
    initial commit
    Display Events with a Basic Shortcode
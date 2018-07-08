=== OpenAgenda for WordPress ===
Contributors: sebastienthivinfocom
Tags: openagenda, agenda, events, calendar, open data, event, organizer, dates, date, conference, workshop, concert, meeting, seminar, summit, class, shortcode, widget, visual composer
Donate link: https://www.paypal.me/sebastienserre
Requires at least: 4.6
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 1.4.0
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
    * yours!

= May I give Idea?
* Yes please use the [Github](https://github.com/sebastienserre/openagenda-wp/issues) issue

= I'm using Elementor Page Builder, How to use OpenAgenda for Wordpress to display events?
* simply use the WordPress Widget, it will display your agenda on Elementor page Builder

= Is it possible to create an event from my website to the OpenAgenda?
* Not yet but at this time, you have a link to your openagenda where visitors can add events (if allowed by the OpenAgenda settings).
* Planned to be added in the Pro Version

= Is OpenAgenda for WordPress is working with WPBakery Page Builder (formerly Visual Composer) ?
* Yes! I try to create Visual Composer Element in the same time I create WordPress Widget and WordPress Shortcode.

= May I test for free the Pro Version?
* Yes! 30 days free trial. Do not hesitate.

= How expensive is the Pro Version?
* [Check OpenAgenda for WordPress Pro Price](https://checkout.freemius.com/mode/dialog/plugin/2279/plan/3475/licenses/unlimited/)

== Screenshots ==
1. settings
2. display in front

== Changelog ==
* 1.4.0 -- 8 july 2018
    Free Version:
        Add CSS Class to all HTML markup. Better customization.
        Add a 30 days Free trial of the pro version.
    Pro Version:
        Add a Visual Composer element, a Widget WordPress and a Shortcode to display the embeds Widget from OpenAgenda.
        Simply choose the OpenAgenda URL, the widget (general, map, search, categories, tags, calendrier, preview) to display.

* 1.3.0 -- 4 july 2018
    introduce Pro Options

* 1.2.6 -- 03 july 2018
    Add a link to allow visitors to post event on OpenAgenda.com

* 1.2.5 -- 30 june 2018
    Correct a bug in Visual Composer lang param

* 1.2.4 -- 29 june 2018
    Improve basic layout
    add CSS to adapt some display

* 1.2.3 -- 29 june 2018
    Add support of markdown (md). Markdown can be used in OpenAgenda to  define the markup of descriptions.
    WordPress Widget can be used on Elementor Page Builder

* 1.2.1/2 -- 27 june 2018
    Correct a bug if VC not activated
    Cosmectic change

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
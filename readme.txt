=== OpenAgenda for WordPress ===
Contributors: sebastienserre
Tags: openagenda, agenda, events, calendar, opendata,
Requires at least: 4.6
Tested up to: 5.1
Requires PHP: 5.6
Stable tag: 1.7.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Display Events hosted in Openagenda.com easily in your WordPress Website.

== Description ==
Display Events hosted in Openagenda.com easily in your WordPress Website by using shortcode, Gutenberg Blocks and Widget.
Shortly is planned to be produced Gutenberg Blocks and page builder elements.

/!\ Manage your Openagenda events directly from your WordPress BackOffice (synced ones per hour)
/!\ New on OpenAgenda for WordPress 1.5.0: Gutenberg Blocks!

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
* The param Agenda slug is mandatory. example: slug=\'my-agenda-slug\'.
* The param nb is optional. Default value is 10. It will retrieve data for the \"nb\" events. example: nb=12
* The param lang is optional. Default value is en (english). It will retrieve data with the \"lang\" params (if exists). example: lang = \'fr\'

= Is this plugin created by Openagenda.com? =
* No! This plugin is developped by [Thivinfo.com](https://thivinfo.com)

= Do I need to be the owner of the Agenda?
* No! You can display all Agenda from [Openagenda.com](openagenda.com) by just copying the Agenda's slug.

= May I display several Agenda?
* Yes! (one by widget, shortcode, gutenberg Block)

= May I give Idea?
* Yes please use the [Github](https://github.com/sebastienserre/openagenda-wp/issues) issue

= Where can I found help ?
* I'm starting to write a Wiki on [Github](https://github.com/sebastienserre/openagenda-wp/wiki)

= I'm using Elementor Page Builder, How to use OpenAgenda for Wordpress to display events?
* simply use the WordPress Widget, it will display your agenda on Elementor page Builder

= Is it possible to create an event from my website to the OpenAgenda?
* Not yet but at this time, you have a link to your openagenda where visitors can add events (if allowed by the OpenAgenda settings).
* Planned to be added in the Pro Version

= Is OpenAgenda for WordPress is working with WPBakery Page Builder (formerly Visual Composer) ?
* Yes! The Pro version is coming with Visual Composer element. Free one provide only WP Shortcodes and WP Widgets.

= May I test for free the Pro Version?
* Yes! 30 days free trial. Do not hesitate.

= How expensive is the Pro Version?
* [Check OpenAgenda for WordPress Pro Price](https://checkout.freemius.com/mode/dialog/plugin/2279/plan/3475/licenses/unlimited/)

== Screenshots ==
1. settings
2. display in front
3. Free WordPress Widget
4. Customer account embed. Free Trial Tab
5. Visual Composer Element (PRO Version)
6. Display OpenAgenda widget in a great WP Widget (Pro Version)
7. Gutenberg Block (Front-Office and Preview)
8. Gutenberg Block (Back-Office and Settings)
9. Masonry layout (Pro Version)

== Changelog ==
= 1.7.1 == 19/04/2019
* BUGFIX -- add dots on slider Shortcode

= 1.7.0 == 17/03/2019
* FEATURES -- Manage your events directly in your WordPress Back-Office
* FEATURES -- Improve the Slider Shortcode -- Add the "display_description" params ( set to "true" to enable )

= 1.6.1 == 13/03/2019
* BUGFIX -- Fix error on parsedown call

= 1.6.0 == 11/03/2019
* FEATURE -- Add Masonry Layout on Pro Version.

= 1.5.9 == 11/03/2019
* BUGFIX - Prevent fatal error on slider

= 1.5.8 == 10/03/2019
* BUGFIX -- verification of OpenAgenda API KEY
* FEATURE -- Add Affiliation program. Recommend this plugin and earn money!

= 1.5.7 == 10/03/2019
* FEATURE -- Add [main-openagenda] shortcode [see docs](https://thivinfo.com/docs/openagenda-pour-wordpress/shortcodes/)

= 1.5.6 == 09/03/2019
* BUGFIX -- Add test on API Key (Thanks to wordpress.basiliquedevezelay.org)
* BUGFIX -- Correct Image Size on Main Gutenberg Block

= 1.5.5 == 05/03/2019
* Add Main Agenda Gutenberg Block ( it's easy to display your events)

= 1.5.4 = 01/03/2019
* Update Freemius SDK

= 1.5.3 = 15/02/2019
* Add Gutenberg Blocks

= 1.5.2 = 05/02/2019
* remove PHP Notices
* Code cleaning

= 1.5.0 = 26 december 2018 =
* Add Gutenberg Blocks

= 1.4.9 == 01 November 2018 =
* Add parameters (show image, show title, show description) in Basic shortcode and Basic Widget

= 1.4.8 == 30 octobre 2018 =
* Improvment add Pro shortcode => search bar.
* Refactore some part of code

= 1.4.7 == 29 octobre 2018 =
* Succesfully tested on WP.5.0 & New Editor ( Gutenberg with Shortcodes )
* Several php notices corrected
* Code reformat

= 1.4.6 == 28 octobre 2018
* Upgrade Freemius SDK
* Correct some bug in the shortcode
* Improve settings page (add link to documentaion)

= 1.4.4 == 20 juillet 2018
    Reformat code
    Minor bugfix
    Add consistency in translation
    Add help
* 1.4.3 -- 13 july 2018
    Add a slider (SlickJS) in Pro Version
    Add filters for dev
    Fix Minor bugs on free stuff

* 1.4.2 -- 10 july 2018
    Add cosmetic stuff (Style)
    Add Help on setting page

* 1.4.1 -- 9 july 2018
    Add i18n for Pro different from Free one
    fr_FR added

* 1.4.0 -- 8 july 2018
    Free Version:
        Add CSS Class to all HTML markup. Better customization.
        Add a 30 days Free trial of the pro version.
    Pro Version:
        Add a Visual Composer element, a Widget WordPress and a Shortcode to display the embeds Widget from OpenAgenda.
        Simply choose the OpenAgenda URL, the widget (general, map, search, categories, tags, calendrier, preview) to display.
        Display single event without going out of you website.

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

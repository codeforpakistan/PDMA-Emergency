=== ULTIMATE TABLES ===
Contributors: Extendyourweb.com
Donate link: http://www.extendyourweb.com/wordpress-plugins/
Tags: tables, table, responsive, excel, access, rows, columns, pages, post, widget, row, column, order, filter, search, feed, feeds, page, pages, Post, posts, related, rss
Requires at least: 2.8.0
Tested up to: 4.3.1
Stable tag: 1.6.3

Create, manage and design tables without writing html code. Paging, sorting, filtering, searching and more options.

== Description ==

With this plugin you can create and manage very easily your tables from the wordpress administration. You can insert tables into your pages, articles, posts or in the 'ultimate tables' widget.


<a href="http://www.extendyourweb.com/ultimate-tables/">Demo and plugin page</a>

Its main features are:

<ul>

<li> You can create different tables. Within a page or post can insert multiple tables at once.</li>

<li> Management of the tables created. Change the number of rows and columns, reorder and delete you.</li>

<li> You can enter text or html code inside the cells.</li>

<li> Choose from 3 designs or customize.</li>

<li> Responsive tables.</li>

<li> Optional manual class.</li>

<li> Search box.</li>

<li> Ordination alfabetic and numerically.</li>

<li> Information on number of rows and items.</li>

<li> Pagination with options.</li>

<li> Configurable width and height.</li>

<li> Texts manageable.</li>

<li> You can insert tables created insides of pages or post with the code [ultimatetables x /], where x would be the id of the table.</li>

<li> Along with the plugin is installed 'Ultimate tables' widget. With this widget you can insert tables created.</li>

<li> Plugin continually updated.</li>

</ul>

Video manual:
https://www.youtube.com/watch?v=yy42D9wsDXU>

Install and using the plugin is very easy:

<ul>
<li> Install "ultimate tables" plugin and activate.</li>

<li> In the settings section you will have a new button called "ultimate tables", click it.</li>

<li> After pressing you will access the plugin administration. Click "Create New Table" to create the tables you want.</li>

<li> For each table you have a shortcode for insert in pages and posts, and a "edit" button to manage the values and format table.</li>

<li>In section widgets you have a widget called "ultimate tables" that allow you to insert tables that you want in a widget.</li>

<li>You can enter HTML code in the cells. Use the sign " and not the sign ' to indicate attributes.</li>

</ul>


== Installation ==

This section describes how to install the plugin and get it working.

1. Install the plugin via the plugins menu in your administrator.
2. Activate it and you'll see a new menu option in "Settings" the "Ultimate tables". We also see a new ultimate tables widget ready for use.
3. You can display the slider as a plugin, widget:


- Plugin. In the settings menu of your wordpress administration will see a new button: "Ultimate tables". Once inside you can create the table you want. Click the "edit" to edit the table.


- Widget: Go to the widgets section of your administrator. You will see a new widget: Ultimate tables. Select the table to show.

== Frequently Asked Questions ==
= none =
none

== Screenshots ==


1. Table sample.
2. Settings -> Ultimate tables.
3. Table admin.


== Changelog ==

= 1.6.3 =

* Fixed: "Warning: Cannot reinitialise DataTable".

= 1.6.2 =

* Show html code and constructor method for WP_Widgets.

= 1.6.1 =

* The called constructor method for WP_Widget is deprecated since version 4.3.0. Plugin now use __construct() method. Admin changes.

= 1.6 =
* Security update. Close the database exploit. We used nonce(wp_create_nonce and wp_verify_nonce functions) in all forms, validating sanitizing, escaping user Data and protect queries against SQL injection attacks.

= 1.5 =
* New table option: Resolves conflicts with other jquery functions. Desactive two buttons pagination because produced one error.

= 1.4 =
* 4 new Design tables.
* Responsives tables.

= 1.3 =
* 2 new Design tables.
* Fixed bugs in the admin.

= 1.2 =
* New feautures: Table height, a new navigation style, ... .

= 1.1 =
* first release

== Upgrade Notice ==
there is no higher version.
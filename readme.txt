=== WordPress Ultimate Toolkit ===
Contributors: Charles, Leo, Snowblink
Donate link: http://wordpress-ultimate-toolkit.googlecode.com
Tags: comments, posts, widgets, template tags, sidebar, toolkit
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.0

This plugin extends functionalities of WordPress by many sidebar widgets and template tags. Recent comments, random posts, most commented posts, etc.

== Description ==

This project is a plugin for WordPress, which is a famous blogging platform. It extends output contents of WordPress by a set of database queries. It also provides a series of corresponding sidebar widgets for end users, template tags for theme designers, and filters and actions for secondary development.

Reasons to use it:
* Sidebar widgets supported, so you can make use of all its functionalities without code work
* Template tags supported, so you can also customize it as what you want it to be
* Three layers architecture, you can get rid of verbose parts of it
* Easily install and completely uninstall, leave nothing in your database
* Actions and Filters supported, you can extend it with plugin for plugin 

Features:
* Output recent posts
* Output random posts
* Output related posts
* Output posts in a certain category
* Output most commented posts
* Output recent comments with comment contents
* Output active commentators
* Output recent commentators
* Automatic excerpt post content
* Hide pages which you don¡¯t want them to be shown on your front page
* Feed enhancement (in the development plan)
* Content enhancement (in the development plan) 

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`

== Directory Hierarchy ==

There are a lot of sub directories in the root directory of this plugin. The functions
of these directories are:

* "dev" for test files or other files used in the development
* "doc" for documents of this plugin include developer documents and user documents
* "i10n" for multilanguage support
* "inc" all the code of this plugin include php, js and css
* "libs" libraries
* "media" binary files include images, logos, etc.

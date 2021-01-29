# WordPress Ultimate Toolkit
Contributors: charlestang  
Donate link: http://sexywp.com/wut  
Tags: related psots, recent posts, recent comments, popular posts, widgets, auto digest  
Requires at least: 5.3.0  
Requires PHP: 5.6  
Tested up to: 5.6  
Stable tag: 2.0.5  
License: GPL v3  
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Provide a variety of widgets with rich options, such as recent posts, related articles, latest comments, and popular posts, etc.

## Description
Provide a variety of widgets with rich options, such as recent posts, related articles, latest comments, and popular posts, etc.

Provides a variety of handy little features such as word count, inserting custom style sheets and Js code snippets on the page, automatic article summary, TDK settings (SEO), etc.

### Sidebar Widgets
 * Recent posts - Display a list of recently published articles with many options available for control. For example, you can control whether to show the date the article was posted, the format of the date, the number of comments the article has received, etc.
 * Popular posts(need WP-Postviews plugin) -  Display a list of the most popular articles (sorted by number of views), control whether to display the date the article was published, specify the date to filter articles that are too old, control whether to display the number of comments on the article, etc.
 * Related posts - Display a list of articles related to the current article on a single page, based on the article's category and tags. You can control whether to show the post date of the article, the number of comments the article has received, and other options.
 * Recent comments - You can show the latest comment list, different from the official widget, which can show the content of the comment, and you can freely control the number of words truncated, and whether to show the date of the comment, etc.

### Tools
 * Generate posts and pages excerpt automatically.
 * Show words count for every post(page) on *Eidt Posts(Pages)*.
 * Add custom css/js code snippets to your page without edit theme files.
 * At the end of each article, a list of related articles is generated and displayed on the single article page.

### Template Tags
 * `wut_recent_posts()`
 * `wut_random_posts()`
 * `wut_related_posts()`
 * `wut_posts_by_category()`
 * `wut_most_commented_posts()`
 * `wut_recent_comments()`
 * `wut_active_commentators()`
 * `wut_recent_commentators()`

### Other Features
 * Easy to use, few coding works
 * Secondary development support(actions and filters supported)
 * Easy to uninstall, leave nothing in your database

## Installation
 1. Upload the directory `wordpress-ultimate-toolkit` to `/wp-content/plugins`
 1. Go to `Appearances-->Widgets` to activate the plugin
 1. Done :)

## Changelog
### 2.0.5
 * Control options added for related posts list like list title, number of posts, etc.

### 2.0.3
 * Hide pages option removed from admin panel.
 * Old version of recent posts widget removed.
 * Old version of recent comments widget removed.
 * `Related Post` widget was completely rewritten.
 * Function switch add to auto excerption feature.
 * `Uninstall` feature was replaced by using plugin uninstall API.

### 2.0.2
 * Recent posts widget support date format.
 * `wut_recent_posts()` re-implemented with WP_Query API.

### 2.0.1
 * Minor change, some clean work.

### 2.0.0
 * Two widgets rewritten from ground up.
 * Most viewed posts wdiget is added.
 * It is compatible with new version of WordPress now.

### 1.0.3
 * A bug fixed. Spelling mistake cause auto expert not work.

### 1.0.2
 * Allow variables in excerpt options setting
 * Little change in the excerpt function, make it more extensible
 * Add screenshots.

### 1.0.1
 * Excerpt admin page complete.

## Upgrade Notice
### 2.0.3
 Upgrade recommended if you are using version >= 2.0.0.

### 2.0.2
 Upgrade recommended if you are using version >= 2.0.0.

### 2.0.1
 Upgrade recommended if you are using version >= 2.0.0.

### 2.0.0
 Please uninstall the old version mannually first if you want install this.

### 1.0.3
Upgrade recommended.

## Screenshots
1. Eight widgets in this plugin.
2. Excerpt settings example.
3. Hide pages you don't want to show on your homepage.
4. Word count column in *Eidt Posts*

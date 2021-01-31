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

提供多种功能丰富的小挂件，比如最近更新文章列表，相关文章列表，最新评论列表，热门文章列表等等，还有很多方便的小功能。

## Description
提供多种功能丰富的小挂件，比如最近更新文章列表，相关文章列表，最新评论列表，热门文章列表等等。

提供多种方便的小功能，比如字数统计，在页面插入自定义样式表和Js代码片段，文章自动摘要，TDK设置（SEO）等等。

### 侧边栏小挂件
 * 最近更新文章 —— 展示一个最近发布文章的列表，有许多可供控制的选项。比如，可以控制是否展示文章发布的日期，日期的格式，文章获得的评论数量等等。
 * 热门文章列表（需要 WP-Postviews 插件）——  展示最受欢迎的文章列表（按照查看次数进行排序），可以控制是否展示文章的发表日期，可以指定日期以过滤太老的文章，可以控制是否展示文章的评论数量等等。
 * 相关文章列表 —— 根据文章的分类和标签，在单篇文章页面，展示一个与当前文章相关的文章列表。可以控制是否展示文章的发布日期，文章获得的评论数量等选项。
 * 最新评论列表 —— 可以展示最新的评论列表，与官方提供的小挂件不同，该小挂件可以展示评论的内容，可以自由控制截断的字数，以及是否展示评论的日期等等。

### Tools
 * Generate posts and pages excerpt automatically
 * Show words count for every post(page) on *Eidt Posts(Pages)*
 * Add custom css/js code snippets to your page without edit theme files
 * 在每篇文章的末尾，生成一个相关文章的列表，展示在单篇文章页面上。

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
 * 文章后的“相关阅读”列表，增加了控制选项，可以设置列表标题，列表文章数量等。
 * “最近评论员” 和 “活跃评论员” 两个小挂件被删除。

### 2.0.3
 * 页面隐藏功能删除。
 * 老的`最新文章`和`最新评论`挂件删除。
 * `相关文章`挂件完全重写。
 * `自动摘要`功能增加开关，允许关闭。

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
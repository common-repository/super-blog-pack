=== Super Blog Pack: Like, Share, Review, Ratings all in one ===
Contributors: themestones,sohan5005
Donate link: http://themestones.net/
Tags: related, reviews, post, views, like, share, share counter
Requires at least: 3.6.0
Tested up to: 5.0
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

All in one solution that powers up your blog with likes, reviews, related posts, views counter and much more.

== Description ==

Finally here's the plugin you only need to power up your blog. With this plugin, you get the below features:

1. Post like button
2. Post views counter
3. Post reviews system with 5 star rating system (paginated)
4. Related posts
5. Smart columns for related posts
6. Post share links
7. Out of the box styling
8. Adapts with any theme you are using
9. Fully customizable
10. Take over controls with dedicated options page

[Click here for live demo!][1]

## Great news for theme developers!!

Super Blog Pack uses template functionality. That means you can integrate this plugin into your theme and override it’s plugin to customize anything you want! See "How can I customize it" under the FAQ section for more info.

### More features are coming soon!

[1]: http://themestones.net/demos/super-blog-pack/

== Installation ==

Installation is same as any other plugins :)

1. Upload the plugin files to the `/wp-content/plugins/super-blog-pack` directory, or install the plugin through the WordPress **Plugins** screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress
3. Now go to "Super Blog Pack" menu from dashboard to manage options!


== Frequently Asked Questions ==

= Why I see like, views, share, rating only on single post, not archive or search? =

Some themes dont apply WordPress's default content filter on archive or search pages. That doesn't allow any plugin to detect where the post is being shown. So the plugin can't hook with the content and can't add elements there. But on the single page, it's always available to hook and add buttons, links and ratings.

= How can I customize it? =

1. Copy the `templates` folder to the theme you are using.
2. Rename the folder you copied to your theme to `super-blog-pack`.
3. Customize each files that you want.
4. Not required but recommended that you only keep the files that you need to customize in your theme folder. So other files gets loaded from the plugin itself.

It's recommended that you create a child theme and use that. Because every time you update your theme, you'll lose your custom edits.

= I'm a theme developer, I want to integrate this to my theme =

Just add your theme support by this code: `add_theme_support('ts-super-blog-pack-advanced')`. Now use the functions below to show different elements:

`ts_sbp_meta()` - To show post meta

`ts_sbp_reviews()` - For post review section

`ts_sbp_related_posts()` - For related posts

`ts_sbp_share()` - For post share links

Now customize templates for your theme with the guide described on the question above.

Detailed documentation with hooks and functions are coming soon!

== Changelog ==

= 0.1.0 =
* Initial releast.
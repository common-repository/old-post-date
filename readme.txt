=== old_post_date ===
Contributors: westonruter
Tags: posts, date, redirect, location
Tested up to: 2.5.1
Requires at least: 2.3
Stable tag: trunk
Donate link: http://weston.ruter.net/donate/

If post_date changes, chronological permalinks which contain parts of the old date will automatically redirect to the
new permalink. Works along with and compliments _wp_old_slug in WordPres core.

== Description ==

*Notice: This plugin is not being actively maintained. Other priorities have arisen which have forced development to discontinue. Being open source and free, you are of course free to take the code and improve upon it; if you are a developer and would like to be added as a commiter to this plugin, please [contact me](http://weston.ruter.net/contact/).*

Just as the core WordPress functionality now keeps track of old post slugs and redirects visitors to proper permalink,
this plugin does the same thing for the <code>post_date</code>. Whenever a post is saved and the <code>post_date</code>
is changed, a new post meta entry with the key '<code>old_post_date</code>' is inserted with the value of the new <code>post_date</code>. 
When a visitor gets a 404 from a permalink that contains chronology, this plugin looks up the post meta for an <code>old_post_date</code>
that matches their request; if multiple matches are found, then it narrows down the results by looking at the requested post slug in
both <code>post_name</code> and then in <code>_old_post_slug</code>. Finally, the user is redirected to the permalink for the
found post, which should be the post they originally requested.

Upon activation, this plugin creates a new <code>old_post_date</code> post meta entry corresponding to each post's current <code>post_date</code>.

If you value this plugin, *please donate* to ensure that it may continue to be maintained and improved.

= Changelog =

*2008-06-06: 1.0.3*

* Not saving old post_date if the post has draft status

*2008-06-06: 1.0.2*

* Using permanent (302) redirects instead of temporary (301) ones

*2008-02-24: 1.0.1*

* Now accounting for when there are no matching old post dates
<h1>Shariff integration for Joomla 3.4+</h1>
This Joomla 3.4+ Plugin utilizes Heise Shariff Library to enable website users to share their favorite content without compromising their privacy.

<b>Required:</b>
PHP 5.4+ and Joomla 3.4+

<b>Features Plugin:</b>
* Joomla Update integration
* Language: English, German
* Restrict the execution to Menu Items or Content Categories
* Plugin settings for Themes, Orientation, Services and much more...

<b>Features Shariff Library:</b>
* Services: Twitter, Facebook, GooglePlus, LinkedIn, Pinterest, Xing, Whatsapp
* Themes: Color, Grey, White
* Orientation: Horizontal, Vertical
* Responsive: Yes
* Shariff Languages: bg, de, en, es, fi, hr, hu, ja, ko, no, pl, pt, ro, ru, sk, sl, sr, sv, tr, zh
* Counter: Shariff Backend PHP integration
 
<b>Future plans:</b>
* More languages maybe through contributors
* Integration for more Componentes


<h2>Update Instructions:</h2>
* Everything before V3.0.0 Stable
  * Delete the old Plugin and install the new one
  * Plugin Settings need to be revisited

<h2>Documentation:</h2>
<h4>Share Counter:</h4>
It's really important for the counter to use the url only with www or non-www.
<h6>To redirect www to non-www do the following steps:</h6>
<ol>
<li>Rename the htaccess.txt in your joomla root folder to .htaccess</li>
<li>Add following lines in the end of the .htaccess file</li>
<pre>
# www to non-www
RewriteBase /
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 [R=301,L]
</pre>
or
<pre>
# non-www to www
RewriteCond %{HTTP_HOST} !^www\.
RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]
</pre>
<li>At least you need to open this plugin and save it again!</li>
</code>
</ol>
</p>

<h4>Usage as Shortcode</h4>
You can put into your content or custom_html module the following shortcode: <code>{shariff}</code> . If you use a custom_html Module, please set the Option "Prepare Content" to yes.

<h2>Credits:</h2>
Developed by http://joomla-agentur.de

Thanks to Heise.de for this development https://github.com/heiseonline/shariff

Dedicated for Joomla! Deutschland Facebook Group https://www.facebook.com/groups/joomla.deutschland/

and for Joomla User Group Hamburg http://jug-hamburg.de/ (the main reason for this plugin :-)

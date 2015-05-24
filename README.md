<h1>Shariff integration for Joomla 3.4+</h1>

<h2>Attention:</h2>
<h4>Settings need to be revisited</h4>
After the Update from a previos Version before V3.0.0-RC5, please open the Plugin in "Joomla Administrator -> Extensions -> Plugin -> JooAg Shariff" and set the settings again. This is needed because some of the xml definitions are now more consistent to the shariff definitions and need to be changed.
<h4>Uninstall the Plugin before an Update:</h4>
It's important to delete the old Plugin, because of the new Plugin is a System Plugin, the old one was a Content Plugin.
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
<h2>Description:</h2>
A Joomla 3 Plugin for Social Media Icons as shariff integration

<b>Required:</b>
PHP 5.4+ and Joomla 3.4+

<b>Features:</b>
* Services: Twitter, Facebook, GooglePlus, LinkedIn, Pinterest, Xing, Whatsapp
* Themes: Color, Grey, White
* Orientation: Horizontal, Vertical
* Responsive: Yes
* Shariff Languages: bg, de, en, es, fi, hr, hu, ja, ko, no, pl, pt, ro, ru, sk, sl, sr, sv, tr, zh
* Counter: Shariff Backend PHP integration

Developed by http://joomla-agentur.de

Thanks to Heise.de for this development https://github.com/heiseonline/shariff

Dedicated for Joomla! Deutschland Facebook Group https://www.facebook.com/groups/joomla.deutschland/

and for Joomla User Group Hamburg http://jug-hamburg.de/ (the main reason for this plugin :-)

<h2>Usage as Shortcode</h2>
You can put into your content or custom_html module the following shortcode: <code>{shariff}</code> . If you use a custom_html Module, please set the Option "Prepare Content" to Yes.

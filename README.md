<h3>Shariff integration for Joomla 3.3+</h3>

<h4>Attention:</h4>
1. <p><b>Share Counter:</b><br/>
It's really important for the counter to use the url only with www or non-www. We advice to use non-www! The following explanation is to redirect www to non-www</p>
<p><b>To redirect www to non-www do the following steps:</b></p>
<ol>
<li>Rename the htaccess.txt in your joomla root folder to .htaccess</li>
<li>Add following lines in the end of the .htaccess file</li>
<code>RewriteBase /</code></br>
<code>RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]</code></br>
<code>RewriteRule ^(.*)$ http://%1/$1 [R=301,L]</code>
<li>At least you need to open this plugin and save it again!</li>
</code>
</ol>
</p>
2. Please check after every new release your Plugin settings. 


<b>Description:</b>
A Joomla 3 Plugin for Social Media Icons as shariff integration

<b>Required:</b>
PHP 5.4+ and Joomla 3.3+

<b>Comming soon & Problems:</b>
<ul>
<li>Global Position for the Buttons</li>
<li>Add the Buttons in Modules and Articles via this shortcode {shariff}</li>
<li>Better JS handling</li>
</ul>

Developed by http://joomla-agentur.de

Thanks to Heise.de for this development https://github.com/heiseonline/shariff

Dedicated for Joomla! Deutschland Facebook Group https://www.facebook.com/groups/joomla.deutschland/

and for Joomla User Group Hamburg http://jug-hamburg.de/ (the main reason for this plugin :-)

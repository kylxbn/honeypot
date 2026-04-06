<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>Dashboard &#8249; {{ config('honeypot.company.name') }} &#8212; WordPress</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:13px;color:#3c434a;background:#f0f0f1;}
#wpadminbar{background:#1d2327;height:32px;display:flex;align-items:center;padding:0 10px;position:sticky;top:0;z-index:999;}
#wpadminbar .ab-item{color:#a7aaad;text-decoration:none;font-size:13px;padding:0 10px;height:32px;line-height:32px;display:inline-block;}
#wpadminbar .ab-item:hover{color:#fff;background:#2c3338;}
#wpadminbar .howdy{margin-left:auto;color:#a7aaad;font-size:13px;}
#adminmenu-holder{display:flex;min-height:calc(100vh - 32px);}
#adminmenu{background:#1d2327;width:160px;min-height:100%;flex-shrink:0;}
#adminmenu li a{display:block;padding:8px 14px;color:#a7aaad;text-decoration:none;font-size:13px;}
#adminmenu li a:hover,#adminmenu li.current a{color:#fff;background:#2c3338;}
#adminmenu li.separator{height:1px;background:#2c3338;margin:4px 0;}
#wpcontent{flex:1;padding:20px;}
h1.wp-heading{font-size:23px;font-weight:400;margin-bottom:20px;color:#1d2327;}
.welcome-panel{background:#fff;border:1px solid #c3c4c7;padding:20px 26px;border-radius:2px;margin-bottom:20px;}
.welcome-panel h2{font-size:21px;margin-bottom:10px;}
.metabox-holder{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.postbox{background:#fff;border:1px solid #c3c4c7;border-radius:2px;}
.postbox .postbox-header{padding:10px 12px;border-bottom:1px solid #c3c4c7;display:flex;justify-content:space-between;align-items:center;}
.postbox .postbox-header h2{font-size:14px;font-weight:600;}
.postbox .inside{padding:12px;}
.at-a-glance ul{list-style:none;}
.at-a-glance li{padding:4px 0;border-bottom:1px solid #f0f0f1;}
.activity-block li{padding:5px 0;border-bottom:1px solid #f0f0f1;font-size:12px;}
</style>
</head>
<body>

<div id="wpadminbar">
  <a class="ab-item" href="/">&#8984; {{ config('honeypot.company.name') }}</a>
  <a class="ab-item" href="/wp-admin">Dashboard</a>
  <a class="ab-item" href="/wp-admin/edit.php">Posts</a>
  <a class="ab-item" href="/wp-admin/upload.php">Media</a>
  <span class="howdy">Howdy, <strong>admin</strong> &nbsp;|&nbsp; <a href="/wp-login.php?action=logout" style="color:#a7aaad;text-decoration:none;">Log Out</a></span>
</div>

<div id="adminmenu-holder">
  <ul id="adminmenu">
    <li class="current"><a href="/wp-admin"><b>Dashboard</b></a></li>
    <li class="separator"></li>
    <li><a href="/wp-admin/edit.php">Posts</a></li>
    <li><a href="/wp-admin/upload.php">Media</a></li>
    <li><a href="/wp-admin/edit.php?post_type=page">Pages</a></li>
    <li><a href="/wp-admin/edit-comments.php">Comments <span style="background:#d63638;color:#fff;padding:1px 6px;border-radius:10px;font-size:11px;">23</span></a></li>
    <li class="separator"></li>
    <li><a href="/wp-admin/themes.php">Appearance</a></li>
    <li><a href="/wp-admin/plugins.php">Plugins <span style="background:#d63638;color:#fff;padding:1px 6px;border-radius:10px;font-size:11px;">2</span></a></li>
    <li><a href="/wp-admin/users.php">Users</a></li>
    <li><a href="/wp-admin/tools.php">Tools</a></li>
    <li><a href="/wp-admin/options-general.php">Settings</a></li>
  </ul>

  <div id="wpcontent">
    <h1 class="wp-heading">Dashboard</h1>

    <div class="welcome-panel">
      <h2>Welcome to WordPress!</h2>
      <p>To get started, <a href="/wp-admin/post-new.php">create your first post</a> or <a href="/wp-admin/customize.php">customize your site's appearance</a>.</p>
    </div>

    <div class="metabox-holder">

      <div class="postbox at-a-glance">
        <div class="postbox-header"><h2>At a Glance</h2></div>
        <div class="inside">
          <ul>
            <li>✏️ <a href="/wp-admin/edit.php">5 Posts</a></li>
            <li>📄 <a href="/wp-admin/edit.php?post_type=page">3 Pages</a></li>
            <li>💬 <a href="/wp-admin/edit-comments.php">23 Comments</a></li>
          </ul>
          <p style="margin-top:10px;color:#646970;">WordPress {{ config('honeypot.company.wp_version') }} running <em>Twenty Twenty-Four</em> theme.</p>
          <p style="margin-top:6px;color:#d63638;">⚠ Search engines are not being blocked. <a href="/wp-admin/options-reading.php">Update settings</a></p>
        </div>
      </div>

      <div class="postbox">
        <div class="postbox-header"><h2>Activity</h2></div>
        <div class="inside">
          <ul class="activity-block">
            <li>🔑 <strong>admin</strong> logged in · just now</li>
            <li>✏️ <strong>admin</strong> edited <em>About Us</em> · 2h ago</li>
            <li>💬 New comment on <em>Hello World</em> · 5h ago</li>
            <li>🔌 Plugin <em>WooCommerce 8.3</em> updated · yesterday</li>
            <li>👤 New user <em>j.smith@{{ config('honeypot.company.domain') }}</em> · 2 days ago</li>
          </ul>
        </div>
      </div>

      <div class="postbox">
        <div class="postbox-header"><h2>Quick Draft</h2></div>
        <div class="inside">
          <p style="margin-bottom:8px;"><label>Title<br><input type="text" style="width:100%;padding:4px 8px;border:1px solid #8c8f94;border-radius:3px;" placeholder="Post title"></label></p>
          <p><label>Content<br><textarea style="width:100%;padding:4px 8px;border:1px solid #8c8f94;border-radius:3px;height:80px;" placeholder="What's on your mind?"></textarea></label></p>
          <button style="background:#2271b1;color:#fff;border:none;padding:5px 14px;border-radius:3px;cursor:pointer;">Save Draft</button>
        </div>
      </div>

      <div class="postbox">
        <div class="postbox-header"><h2>WordPress News</h2></div>
        <div class="inside">
          <p><strong>WordPress {{ config('honeypot.company.wp_version') }}</strong> — Latest stable release</p>
          <p style="margin-top:8px;color:#d63638;">⚠ 2 plugins have security updates available.</p>
          <p style="margin-top:8px;"><a href="/wp-admin/plugins.php">Update plugins now &rarr;</a></p>
        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html>

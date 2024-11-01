=== Plugin Name ===
Contributors: Dave Zaikos
Donate link: http://www.zaikos.com/donate/
Tags: images, pictures, photo, gallery, lightview
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.3.0

Enables Lightview for images in posts and pages, including the WordPress image gallery.

== Description ==

Allows WordPress to utilize Nick Stakenburg's [Lightview](http://www.nickstakenburg.com/projects/lightview/ "Lightview") for images in posts and pages, including the WordPress image gallery. Lightview provides a clean, simple JavaScript image and gallery viewer.

After installing, the plugin will automatically use Lightview for all links to images and when the WordPress gallery is used. Lightview's use can be configured in the settings page.

== Installation ==

1. Upload the `z-lightview` folder to the `wp-content/plugins/` directory.
2. [Download Lightview](http://www.nickstakenburg.com/projects/download/?project=lightview "Download Lightview") from Nick Stakenburg's web site.
3. Extract the Lightview zip file.
4. Upload the `css` and `images` directories to the `wp-content/plugins/z-lightview` directory.
5. Upload the `js/lightview.js` file to the `wp-content/plugins/z-lightview/js` directory
6. Activate the plugin through the 'Plugins' menu in WordPress.

That's it! Links to image files and uses of the gallery shortcode, `[gallery]`, will be updated to use Lightview. You can configure additional options in the Settings, z-Lightview page.

== Frequently Asked Questions ==

= What image types are supported? =

Creating a link to a file that ends with a JPEG, JPG, PNG, GIF or BMP extension will be handled by Lightview.

= Lightview isn't working when I click a link. =

Lightview doesn't intercept links until the page is completely loaded. If a link is clicked before the page finishes loading then it will be handled as per usual (i.e. the image will load in the browser screen instead of being handled by Lightview).

= Internet Explorer 8 doesn't work properly. =

No, it doesn't. But that's not any surprise either.

This is a result of the Protocol JavaScript library not working properly with Internet Explorer 8. To fix this add the following to your `header.php` file **immediately** after the `<head>` tag:

`<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />`

Alternatively, you can uncomment line 515 in the `z-lightview.php` file. Please read the comments on lines 329-331 first though.

= I get a fatal error when activating which says files are missing. =

Due to licensing issues, Lightview cannot be included with the download. You must [download](http://www.nickstakenburg.com/projects/download/?project=lightview) Lightview separately and upload the folders included there (css, images, and js) to this plugin's folder.

= How does this plugin backup Lightview files during a plugin update? =

When the plugin is deactivated it attempts to make a backup copy of Lightview's files in a directory named `wp-content/plugins/.z-lightview-backup`. This folder is prefixed with a period which will cause it to be hidden on most web sites so as not to appear out of place. You can prevent this folder and backup from being created by disabling it from the z-Lightview settings page.

= Does disabling the backup of Lightview files remove the hidden backup directory? =

No. If a backup was created at any point it will not be deleted by unchecking the backup option. To delete the backup folder use an FTP program, or similar. To prevent the backup folder from being created in the first place, disable backups from the settings page immediately after activating the plugin. This setting will be remembered for subsequent activations and deactivations, as well as during future updates to the plugin. It is recommended that you allow the plugin to make a backup to ensure updates are successful with minimal user intervention.

== Changelog ==

= 1.3.0 =

**Note:** As of version 1.3.0 the plugin will (by default) automatically backup installed Lightview files during updates; however, you'll need to install these files just once more--my apologies folks, I have addressed this issue going forward to ensure you are not inconvenienced again.

* Revised filters to better determine when Lightview should be used.
* Updated bundled Prototype library to version 1.6.1. This should provide better support for Internet Explorer 8.
* Revised Scriptaculous library to prevent it from trying to load additional, unnecessary add-ons (previously it would cause several 404 errors).
* Created a settings page (under the main Settings menu) to manage where and how the plugin should use Lightview.
* Added an option to prevent Lightview from being used on images linked to from external sites (configurable in the new settings page).
* Added an option to backup Lightview's JavaScript, CSS, and image files during updates to the plugin (this will ensure you do not have to re-download Lightview in future updates). This is configurable in the new settings page.

= 1.2.1 =

* Update to support PHP 4. This should fix the line 34 syntax error that would cause a fatal error when running in a PHP 4 environment.

= 1.2.0 =

* First public release on the WordPress Plugins directory.

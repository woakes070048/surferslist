# SurferList

SurferList is a website application written in PHP and JavaScript that lets users post listings of items to an online catalog to share information, start discussions, and offer items for sale. marketplace platform

## Features

A single platform that combines [features](https://www.surferslist.com/features) from popular sites like [Craigslist](https://www.craigslist.org/) (e.g. classified ads, multiple locations, anonymity), [eBay](https://www.craigslist.org/) (e.g. e-commerce marketplace, peer-to-peer, business accounts), and [Facebook](https://www.facebook.com/) (e.g. user profiles, topic discussion, social sharing). It is similar to [ShareTribe](https://github.com/sharetribe/sharetribe), except with PHP on the back-end rather than Ruby and more compact.  Learn more at [https://www.surferslist.com/about](https://www.surferslist.com/about).

## Usage

SurfersList was originally created to help enthusiasts of the surfing and other extreme watersports community connect online to discuss and sell sports equipment.  The code has been refactored and open-sourced so that it may be configured for use by any other community or market.

Users can search and browse the catalog of listings by category, brand/manufacturer, or user profile.  Results can be filtered by location, listing type, price/value, condition, and age.  More data fields and filters could be added with additional configuration and minimal code modifications.

## Listing Types

Listings can be [posted](https://www.surferslist.com/post) anonymously or connected to a user [profile](https://www.surferslist.com/about-profiles).

Anonymous listings can be posted by anyone.  They must include a link (url) to a web page where other users can learn more about the item.  Anonymous listings can not be offered for sale, but they are useful for learning and [discussion](https://www.surferslist.com/discuss).  

Users who [create an account](https://www.surferslist.com/join) and activate a profile can post listings linked to their profile.  These can be offered for sale as classifieds, which can not be purchased on the platform; or as "buy-now" listings, which can be [ordered](https://www.surferslist.com/order) and purchased by other users.

See [Listing Types](https://www.surferslist.com/about-listings#listing-types) for more info.

## History

This project began as a [multi-seller/multi-vendor extension](https://github.com/garudacrafts/opencart-customer-product-manager) for [OpenCart](https://github.com/opencart/opencart) (an e-commerce CMS) back in 2013.  As it grew in complexity, rather than continuing to update it to work with newer versions of the CMS, in 2015 work was started to integrate it more deeply into a forked copy of version 1.5 of the software ([OpenCart-CE](https://github.com/opencart-ce/opencart-ce), to be precise).  Since then, much of the code has been *substantially* updated, extended, refactored, and improved.

## Under the Hood

[SurfersList.com](https://www.surferslist.com/) is running on Ubuntu with PHP 7.0 (5.4 minimum required), Apache, and MySQL 5.7 (LAMP stack) on a VM hosted by [DigitalOcean](https://www.digitalocean.com/).  Code is organized in MVC+L pattern.  Multi-location and multi-currency enabled; multi-language ready and possible but disabled.

### Security

* Bcrypt is used to hash and store account passwords
* ReCaptcha and CSRF tokens are used to secure all web forms
* Login attempts are tracked and rate limited
* Form data is validated server-side
* Parameters in SQL statements are sanitized
* AJAX endpoints secured and data validated
* Intrusion detection to deter attacks and blacklist IPs
* SSL certificate (Let's Encrypt) is installed and enforced site-wide
* Sensitive parameters stored separately in config files

### Performance

* Database queries are raw SQL and performant
* Database calls are minimized and heavily cached
* Routes are cached in flat-files and in memory for quick lookup
* Images are resized, compressed, and cached
* JavaScript and CSS are combined and minimized
* Static resources are split between multiple domains
* Pagination and infinity loading of listings
* JavaScript loaded at the bottom of page
* Browser caching and gzip compression enabled

### Images

* Images are stored locally on the same server (CDN possible)
* Multiple sizes generated and optimally served
* Separate directories are used for each account
* Images are validated by MIME-type, extension, filesize, dimensions, and filename
* Images are compressed via nightly cron job

## Installation & Configuration

* No install script (yet); manual setup required
* Database schema coming soon...
* A complete list of possible configuration variables coming soon...
* Administration website yet to be published open-source (will be a separate repo)

## Credits

* OpenCart - [https://www.opencart.com/](https://www.opencart.com/)
* OpenCart Community Edition - [https://github.com/opencart-ce/opencart-ce](https://github.com/opencart-ce/opencart-ce)
* OpenCart ProjectStore theme - [http://themeforest.net/item/project-store-responsive-opencart-theme/5804697](http://themeforest.net/item/project-store-responsive-opencart-theme/5804697)
* OpenCart Security module - [http://exife.com/](http://exife.com/)
* OpenCart Per Product Shipping extension - [http://opencartaddons.com](http://opencartaddons.com)

### Third-Party Libraries

* Minify - [https://github.com/matthiasmullie/minify/](https://github.com/matthiasmullie/minify/)
* ReCaptcha - [http://www.google.com/recaptcha/](http://www.google.com/recaptcha/)
* SendGrid - [https://sendgrid.com/](https://sendgrid.com/)
* Font Awesome - [http://fontawesome.io](http://fontawesome.io)
* jQuery - [https://jquery.org/](https://jquery.org/)
* jQuery-UI - [http://jqueryui.com](http://jqueryui.com)
* Masonry - [http://masonry.desandro.com](http://masonry.desandro.com)
* Wookmark - [https://github.com/germanysbestkeptsecret/Wookmark-jQuery](https://github.com/germanysbestkeptsecret/Wookmark-jQuery)
* Colorbox - [http://www.jacklmoore.com/colorbox/](http://www.jacklmoore.com/colorbox/)
* jQuery Zoom - [http://www.jacklmoore.com/zoom/](http://www.jacklmoore.com/zoom/)
* Bootstrap Tooltip - [http://getbootstrap.com](http://getbootstrap.com)
* iCheck - [https://github.com/fronteed/iCheck/](https://github.com/fronteed/iCheck/)
* imagesLoaded - [https://imagesloaded.desandro.com/](https://imagesloaded.desandro.com/)
* Slick - [http://kenwheeler.github.io/slick/](http://kenwheeler.github.io/slick/)
* JQVMap - [http://jqvmap.com](http://jqvmap.com)
* Ajax Upload - [http://valums.com/ajax-upload/](http://valums.com/ajax-upload/)
* jQery Cycle Lite Plugin - [http://malsup.com/jquery/cycle/lite/](http://malsup.com/jquery/cycle/lite/)
* jCarousel - [http://sorgalla.com/jcarousel/](http://sorgalla.com/jcarousel/)
* Spectrum Colorpicker - https://github.com/bgrins/spectrum

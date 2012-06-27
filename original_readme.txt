=== Petfinder Listings ===
Contributors: bridgetwes
Tags: petfinder, adoptable pets
Requires at least: 3.0
Tested up to: 3.4
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Petfinder Listings plugin takes advantage of the Petfinder API and can be integrated into your site without coding.

== Description ==

Petfinder is a free site where shelters and rescues can post pets for adoption. The Petfinder Listings plugin takes advantage of the Petfinder API and can be integrated into your site without coding.  All you need is your Petfinder shelter id and free Petfinder API key.

The Petfinder Listings plugin allows you to:

1. Display all pets listed with your shelter on Petfinder
2. Display a featured or random pet in a widget

= Demos =

* [Greyhound Pets of America Wisconsin](http://www.gpawisconsin.org/adopt/petfinder-results/)

* [Midwest Italian Greyhound Rescue](http://www.midwestigrescue.com/adoption/available-igs/)

Example of different shelters listed on one site

* [Midwest Italian Greyhound Rescue - Missouri page](http://www.midwestigrescue.com/adoption/available-igs-in-missouri-and-kansas/)

Example of Featured Pet Widget

* [Greyhound Pets of America Homepage](http://www.gpawisconsin.org/)

= Shortcode =

[shelter_list] displays a list of pets from your shelter. You must enter your shelter ID and API key in Petfinder Listings' settings page for this shortcode to work. You may also need to edit your Petfinder settings to enable sharing your data with third parties. See Petfinder settings under Frequently Asked Questions below.
Optional attributes for shortcode are:

shelter_id  - Allows you to list adoptable pets from a shelter different from the shelter id defined in your Petfinder Listings Settings.

breed - If you wish to list only one breed on a page. You can also add ! before the breed if you wish to list all pets who do not match this breed. See the following examples:

[shelter_list breed="Italian Greyhound"] - mixed breeds are not matched

[shelter_list breed="!Italian Greyhound"] - mixed breed are matched

count - The maximum number of pets to return. Defaults to 75 and must be a number.  Note if you set the breed or animal attribute you need to set the count high enough to include all animals with your shelter. For example, if you have 50 animals made up of 25 cats and 25 dogs, and you have one page to list all dogs and one page to list all cats, the count should be set to at least 50 to get all cats or all dogs on both pages. Petfinder does not have a method to filter animals from a shelter so the plugin has to filter after retrieving all the results.

animal - Type of animal. Value should be one of the following or blank to get all: barnyard, bird, cat, dog, horse, pig, reptile, smallfurry.

include_info - Value should be set to "yes" or "no"; default is "yes". If set to "yes", Breeds, Spayed/Neutered, Up-to-date with routine shots, Housebroken, kid safe, cat safe, dog safe, special needs are displayed in list below photo thumbnails. Each list item has a different CSS class so you can hide any you do not want to show.

css_class - Set your own class name on div containing all pets. The default value is 'pets'. This allows you to control the styles separately on different pages.

= Widget =

Add the Petfinder Listings Featured Pet widget under Appearance -> Widgets.  After adding the widget to a sidebar you can set a featured pet id to display a featured pet, or leave blank to display a random pet from your shelter.
Featured Pet Widget Settings:

Featured Pet ID - This is the Petfinder System ID listed below the Pet's name on Petfinder's Pet Edit screen. (Optional - if left blank a random pet from your shelter will be displayed. Please note: Petfinder's random pet listing tends to be unstable.)

Your Listing Page URL - The page where your shortcode [shelter_list] can be found. If this is set, your featured pet will link directly to this pet on your shelter list page. (Optional)

Featured Pet Image Size - The size of the Featured Pet image. (Required)

Featured Pet Copy Size - How many characters from the Pet's description you want to display. You can also enter 0 to display full description or -1 for none. (Required)

Featured Pet PDF Link - If you would like to create a PDF with more information about your Featured Pet, link to the PDF uploaded separately through WordPress' Media here.

== Installation ==

1. Upload expanded petfinder-listings folder to the /wp-content/plugins/ directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Set your Petfinder API Key, Shelter Id, Thumbnail and Large image size under WordPress Settings -> Petfinder Listings. You will need to generate a free Petfinder API key on Petfinder here: http://www.petfinder.com/developers/api-key. Your shelter ID is your Petfinder username, usually your state abbreviation plus a number.
4. Place the shortcode [shelter_list] in a Page or Post content to display your pet list on a page. The lists includes all pets' name, description and photos uploaded to Petfinder.
5. Add the Petfinder Listings Featured Pet widget (Appearance -> Widgets) if desired.

== Frequently Asked Questions ==

= How do you get a Petfinder API key? =

You will need to generate a free Petfinder API key on Petfinder here: http://www.petfinder.com/developers/api-key.

= What to do if your page doesn't list any pets and says 'shelter opt-out' =

If the list is not working after correctly entering all your Petfinder Listings Settings and the page displays a status of 'shelter opt-out', make sure your Petfinder account is set up to share data through Petfinder's API. To do this, log into your Petfinder account and click on the Organization Info tab. Look for the box labeled 'Share Pet List' and make sure all checkboxes within this box are selected: Please share my pet list with all third parties, Partner sites & Petfinder API users.

= How do you change the Petfinder list styles? =

This plugin generates generic HTML and includes a stylesheet to position the elements. To style, override CSS in your theme's stylesheet. You might need to add !important to each style.

= I have a video in petfinder but it isn't showing on my site. = 

Petfinder does not return video information through their API, however if you have a video on YouTube you can paste the video embed code in your pet's description to display the video through the Petfinder plugin.

== Screenshots ==

1. Settings Page
2. Widget Settings
3. Page with shortcode [shelter_list]
4. Widget display

== Changelog ==

= 1.0.5 =
* Added shortcode attribute include_info
* Removed Setting Include Cat or Kid Safe and switched to shortcode attribute include_info
* Added different css classes to each info list item.
* Added a shortcode attribute css_class to set css class on div containing all pets.

= 1.0.4 =
* Added shortcode attributes animal and count
* Added note on how to display video within description
* Switched mixed breed dogs to not display when breed is set.

= 1.0.3 =
* Bug Fixes with repository set up

= 1.0 =
* This is the first version

== Upgrade Notice ==

= 1.0.5 =
* Added shortcode attribute include_info
* Removed Setting Include Cat or Kid Safe and switched to shortcode attribute include_info
* Added different css classes to each info list item.
* Added a shortcode attribute css_class to set css class on div containing all pets.

= 1.0.4 =
* Added shortcode attributes animal and count
* Switched mixed breed dogs to not display when breed is set. 

= 1.0.3 =
* Bug Fixes with repository set up

= 1.0 =
First version


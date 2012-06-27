<?php
/*
Plugin Name: Petfinder Listings
Plugin URI: http://www.unboxinteractive.com/wordpress/petfinder-listings-plugin/
Description: The Petfinder Listings plugin takes advantage of the Petfinder API and can be integrated into your site without coding.
Version: 1.0.6
Author: Bridget Wessel
Author URI: http://www.unboxinteractive.com/
License: GPLv2
*/

/*  Copyright 2012 Bridget Wessel  (email : bridget@unboxinteractive.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/********** Add default styles ************/

//ini_set("allow_url_fopen", true);

function petfinder_listings_styles(){
    wp_register_style('petfinder-listings-style', plugins_url( 'petfinder.css', __FILE__ ));
    wp_enqueue_style('petfinder-listings-style');
}
add_action('init', 'petfinder_listings_styles');


/********** Add js to switch out photos ***********/
function petfinder_listings_scripts() {
    if (!is_admin()){
        wp_register_script( 'petfinder_listings_scripts', plugins_url( '/petfinder.js', __FILE__ ));
        wp_enqueue_script( 'petfinder_listings_scripts' );
    }
}

add_action('wp_enqueue_scripts', 'petfinder_listings_scripts', 10, 1);

//add defaults to an array
$petf_options = array(
  'apikey' => 'default',
  'shelter_id' => 'default',
  'thumbnail' => 'pnt',
  'large_image' => 'pn'
);

include( dirname(__FILE__) . '/featuredpet-widget.php' );

//add settings to database if not already set

add_option('Petfinder-Listings', $petf_options);
$petf_options = get_option('Petfinder-Listings');

// create custom plugin settings menu

add_action('admin_menu', 'petf_admin_page');
add_action( 'widgets_init', create_function('', 'return register_widget("Petfinder_Listings_Featured_Pet");') );

function petf_admin_page() {
	add_options_page('Petfinder Listings Plugin Settings', 'Petfinder Listings', 'manage_options', 'petf', 'petf_options_page');
}

// Add Settings to Plugin Menu
$pluginName = plugin_basename( __FILE__ );

add_filter( 'plugin_action_links_' . $pluginName, 'petf_pluginActions' );

function petf_pluginActions( $links ) {
	$settings_link =
		'<a href="' . get_admin_url( null, 'options-general.php' ) . "?page=petf".'">' .
		__('Settings') . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}



//write settings page
function petf_options_page() {
   global $petf_options;
    if(isset($_POST['save_changes'])) {
        check_admin_referer('petfinder-listings-update_settings');

        $petf_options['apikey']     = $_POST['apikey'];
        $petf_options['shelter_id'] = $_POST['shelter_id'];
        $petf_options['thumbnail']  = $_POST['thumbnail'];
        $petf_options['large_image'] = $_POST['large_image'];

        update_option('Petfinder-Listings', $petf_options);

        echo "<div class=\"error\">Your changes have been saved successfully!</div>";
    }
    ?>
<div class="wrap">

<h2>Petfinder Settings</h2>

<form name="petfinder-options" action="options-general.php?page=petf" method="post">
    <?php
    if ( function_exists( 'wp_nonce_field' ) )
	    wp_nonce_field( 'petfinder-listings-update_settings' );  ?>

    <table class="form-table">
        <tr valign="top">
        <th scope="row">Your Petfinder API Key (go <a href="http://www.petfinder.com/developers/api-docs" target="_blank">here</a> to get one)</th>
        <td><input type="text" name="apikey" value="<?php echo $petf_options["apikey"] ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Shelter ID</th>
        <td><input type="text" name="shelter_id" value="<?php echo $petf_options["shelter_id"] ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Thumbnail Size (select fixed side size, other side varies depending on ratio of original photo)</th>
        <td><select name="thumbnail">
            <option value="t" <?php echo $petf_options["thumbnail"] == "t" ? "selected='selected'" : ""?>>scaled to 50 pixels tall</option>
            <option value="pnt" <?php echo $petf_options["thumbnail"] == "pnt" ? "selected='selected'" : ""?>>scaled to 60 pixels wide</option>
            <option value="fpm" <?php echo $petf_options["thumbnail"] == "fpm" ? "selected='selected'" : ""?>>scaled to 95 pixels wide</option>
        </select></td>
        </tr>

        <tr valign="top">
        <th scope="row">Large Image Size</th>
        <td><select name="large_image">
            <option value="x" <?php echo $petf_options["large_image"] == "x" ? "selected='selected'" : ""?>>original, up to 500x500</option>
            <option value="pn" <?php echo $petf_options["large_image"] == "pn" ? "selected='selected'" : ""?>>up to 320x250</option>
        </select></td>
        </tr>

        <tr>
            <th colspan="2"><p>After saving, create a page with the shortcode [shelter_list] in the content. View this page to see your listings.</p>
                <p>You can also add the following options to your shortcode<br />[shelter_list shelter_id="another-shelter-id" breed="Italian Greyhound" count=75 animal="dog" include_info="yes" css_class="greyhounds"] </p></th>
        </tr>

    </table>

    <p class="submit">
    <input type="hidden" name="save_changes" value="1" />
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<?php }    // end function petf_options_page


add_shortcode('shelter_list','petf_shelter_list');


/** Using shortcode shelter_list grab all animals for this shelter.
 * Available Options: shelter_id (if want to list animals from shelter not set in Petfinder Listings settings, breed, count, animal, include_info.
 * Breed can be inclusive or exclusive by adding ! before breed name.      ***/
function petf_shelter_list( $atts ) {

    global $petf_options;

    extract( shortcode_atts( array(
		'shelter_id' => $petf_options['shelter_id'],
		'breed' => '',
        'count' => 75,
		'animal' => '',
        'include_info' => 'yes',
        'css_class' => 'pets'
	), $atts ) );
	//get the xml
    $xml = simplexml_load_file( "http://api.petfinder.com/shelter.getPets?key=" . $petf_options["apikey"] . "&count=" . intval($count) . "&id=" . $shelter_id . "&output=full" );
    if( $xml->header->status->code == "100"){
        $output_buffer = "";
        if( count( $xml->pets->pet ) > 0 ){
            $output_buffer .= "<div class=\"" . $css_class . "\">";
            foreach( $xml->pets->pet as $dog ){
                $firsttime = true;
                $bigfile = "";
                //print_r($dog);
                $continue = false;
				if($animal == "" || (strtolower($dog->animal) == strtolower($animal))){
					if( $breed != "" ){
					   foreach( $dog->breeds->breed as $this_breed ){
						   if( strpos( $breed, "!" ) === false ){
							   if( strtolower($breed) == strtolower($this_breed) && $dog->mix == "no"){
									$continue = true;
									break; //looking for specific breed and it was found
							   }
						   }else{
							   if( strtolower(str_replace( "!", "", $breed )) == strtolower($this_breed) && $dog->mix == "no" ){
								   break; //looking for other breeds and this breed was found
							   }else{
								   $continue = true;
							   }
						   }
					   }
					}else{
						$continue = true;
					}
					if( $continue ){
						$output_buffer .= "<div class=\"dog\"><div class=\"name\"><a name=\"" . $dog->id . "\">". $dog->name . "</a></div>";
						$output_buffer .= "<div class=\"images\">";
						if(count($dog->media->photos) > 0){
							foreach( $dog->media->photos->photo as $photo ){
								//$output_buffer .= $photo["size"];
								if( $photo['size'] == $petf_options["large_image"] ){
									if( $firsttime ){
										$output_buffer .= "<img class=\"petfinder-big-img\" id=\"img_". $dog->id . "\"  src=\"" . $photo . "\">";
									}
									$bigfile = $photo;
								}
								if( $photo['size'] == $petf_options["thumbnail"] ){
									if( $firsttime ){
										$output_buffer .= "<div class=\"petfinder-thumbnails\">";
										$firsttime = false;
									}
									$output_buffer .= "<img class=\"petfinder-thumbmail\" onclick=\"switchbigimg('img_" . $dog->id . "', '" . $bigfile . "');return false;\" src=\"" . $photo . "\"></a>";
								}
							}
						}
						if( !$firsttime ){
							//not first time so there are thumbnails to wrap up in a div.  Closing petfinder-thumbnails
							$output_buffer .= "</div>";
						}
                        if($include_info == "yes"){

                            $output_buffer .= "<ul class=\"pet-options\">";

                            $firsttime = true;
                            foreach( $dog->breeds->breed as $this_breed ){
                                if($firsttime){
                                    $output_buffer .= "<li class=\"breeds\">";
                                    $firsttime = false;
                                }else{
                                    $output_buffer .= ", ";
                                }
                                $output_buffer .=  $this_breed;
                            }
                            if(!$firsttime){
                                $output_buffer .= "</li>";
                            }

                            $icons = "";
                            foreach( $dog->options->option as $option ){
                                switch($option){
                                    case "noCats":
                                        $icons .= "<img src=\"http://www.petfinder.com/images/search/no-cat.gif\" width=\"36\" height=\"21\" alt=\"Prefers home without cats\" title=\"Prefers home without cats\" />";
                                        break;
                                    case "noDogs":
                                        $icons .= "<img src=\"http://www.petfinder.com/images/search/no-dogs.gif\" width=\"41\" height=\"21\" alt=\"Prefers home without dogs\" title=\"Prefers home without dogs\" />";
                                        break;
                                    case "noKids":
                                        $icons .= "<img src=\"http://www.petfinder.com/images/search/no-kids.gif\" width=\"34\" height=\"21\" alt=\"Prefers home without small kids\" title=\"Prefers home without small kids\" />";
                                        break;
                                    case "specialNeeds":
                                        $icons .= "<img src=\"http://www.petfinder.com/images/search/spec_needs.gif\" width=\"18\" height=\"20\" alt=\"Special Needs\" title=\"Special Needs\" />";
                                    case "altered":
                                        $output_buffer .= "<li class=\"altered\">Spayed/Neutered</li>";
                                        break;
                                    case "hasShots":
                                        $output_buffer .= "<li class=\"hasShots\">Up-to-date with routine shots</li>";
                                        break;
                                    case "housebroken":
                                        $output_buffer .= "<li class=\"housebroken\">Housebroken</li>";
                                        break;
                                }
                            }
                            if($icons != ""){
                                $output_buffer .= "<li class=\"icon-options\">" . $icons . "</li>";
                            }
                            $output_buffer .= "</ul>";
                        }
						$output_buffer .= "</div>"; //close images
						switch ($dog->size){
							case "L":
								$size = "Large";
								break;
							case "M":
								$size = "Medium";
								break;
							case "S":
								$size = "Small";
								break;
							default:
								$size = "Not known";
								break;
						}
						$output_buffer .= "<div class=\"description\">" . $dog->description . "</div><div class=\"features\">" . $dog->age . ", " . (($dog->sex == "M") ? "Male" : "Female") . ", " . $size . "</div></div>";
						$output_buffer .= "<div style=\"clear: both; \"></div>";
					}
				} //animal does not match
            }
            $output_buffer .= "</div>";
        }else{
           $output_buffer .= "No dogs are listed for this shelter at this time.  Please check back soon.";
        }
    }else{
        $output_buffer = "Petfinder is down for the moment. Please check back shortly.";
    }

   return $output_buffer;
}

?>

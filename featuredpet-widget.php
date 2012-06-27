<?php


class Petfinder_Listings_Featured_Pet extends WP_Widget {


	public function __construct() {
		parent::__construct(
	 		'petfw', // Base ID
			'Petfinder_Listings_Featured_Pet', // Name
			array( 'description' => __( 'Petfinder Listings Featured Pet Widget' ), ) // Args
		);
	}

    /*** @see WP_Widget::widget()
     Front End Display **/
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        global $petf_options; //options from petfinder settings

        if( $instance["featured_pet_id"] != "" ){
            $xml = simplexml_load_file( "http://api.petfinder.com/pet.get?key=" . $petf_options["apikey"] . "&id=" . $instance["featured_pet_id"] );
            if( $xml->header->status->code != "100"){  //returns status code of 201 if not found
                $xml = simplexml_load_file("http://api.petfinder.com/pet.getRandom?key=" . $petf_options["apikey"] . "&shelterid=" . $petf_options["shelter_id"] . "&output=full");
            }
        }else{
            $xml = simplexml_load_file("http://api.petfinder.com/pet.getRandom?key=" . $petf_options["apikey"] . "&shelterid=" . $petf_options["shelter_id"] . "&output=full");
        } ?>
        <div id="featured_pet">
        <?php
        if( $xml->header->status->code == "100" ){
                $pet = $xml->pet;
                //print_r($pet);  ?>

                <div class="featured_pet_name">
                <?php
                if( $instance['full_list_page'] != "" ){
                    echo "<a href=\"" . $instance['full_list_page'] . "#" . $pet->id . "\">";
                }
                echo $pet->name;
                if($instance['full_list_page'] != ""){
                    echo "</a>";
                }    ?>
                </div> <!-- close featured pet name -->
                <?php
                foreach( $pet->media->photos->photo as $photo ){
                    if($photo['size'] == $instance["featured_pet_image"]){
                        if($instance['full_list_page'] != ""){
                            echo "<a href=\"" . $instance['full_list_page'] . "#" . $pet->id . "\">";
                        }

                        echo "<img class=\"petfinder-featured\" id=\"" . $pet->id . "\"  src=\"" . $photo . "\">";
                        if($instance['full_list_page'] != ""){
                            echo "</a>";
                        }
                        break; //only get one image
                    }
                }
                if(intval($instance["featured_pet_copy_size"]) > -1){
                    if(intval($instance["featured_pet_copy_size"]) == 0){    //full text
                        echo "<p>" . strip_tags($pet->description);
                    }else{
                        echo "<p>" . substr(strip_tags($pet->description), 0, intval($instance["featured_pet_copy_size"]));
                    }
                    if($instance['full_list_page'] != ""){
                       echo "<a href=\"" . $instance['full_list_page'] . "#" . $pet->id . "\">... More &gt;</a>";
                    }
                    echo "</p>";
                }else if($instance['full_list_page'] != ""){
                    //no copy, but display More link
                    echo "<p><a href=\"" . $instance['full_list_page'] . "#" . $pet->id . "\">... More &gt;</a></p>";
                }
                if($instance["featured_pdf_link"] != ""){
                    echo "<p><a href=\"" . $instance["featured_pdf_link"] . "\" target=\"_blank\">View Featured Hound PDF</a></p>";
                }

        }else{
               echo "Petfinder is down for the moment. Please check back shortly.";
        }
        ?>
        </div> <!-- close featured pet -->
    <?php
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
	    $instance = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['featured_pet_id']      = strip_tags( $new_instance['featured_pet_id'] );
        $instance['full_list_page'] = strip_tags( $new_instance['full_list_page'] );
        $instance['featured_pet_image'] = strip_tags( $new_instance['featured_pet_image'] );
        $instance['featured_pet_copy_size'] = intval( $new_instance['featured_pet_copy_size'] );
		$instance['featured_pdf_link'] = strip_tags( $new_instance['featured_pdf_link'] );
		return $instance;
    }

    /** @see WP_Widget::form */
    function form( $instance ) {
        // Defaults
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'featured_pet_id' => '',
            'full_list_page' => '',
            'featured_pet_image' => '',
            'featured_pet_copy_size' => 0,
            'featured_pdf_link' => ''
		));

        $title = esc_attr($instance['title']);
        $featured_pet_id = esc_attr($instance['featured_pet_id']);
        $full_list_page = esc_attr($instance['full_list_page']);
        $featured_pet_image = esc_attr($instance['featured_pet_image']);
        $featured_pet_copy_size = esc_attr($instance['featured_pet_copy_size']);
		$featured_pdf_link = esc_attr($instance['featured_pdf_link']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('featured_pet_id'); ?>"><?php _e('Featured Pet ID (If left blank, will select a random pet):'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('featured_pet_id'); ?>" name="<?php echo $this->get_field_name('featured_pet_id'); ?>" type="text" value="<?php echo $featured_pet_id; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('full_list_page'); ?>"><?php _e('Your Listing Page URL (Set this if you want the featured pet to link back to the pet on your list page):'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('full_list_page'); ?>" name="<?php echo $this->get_field_name('full_list_page'); ?>" type="text" value="<?php echo $full_list_page; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('featured_pet_image')?>"><?php _e('Featured Pet Image Size (select fixed side size, other side varies depending on ratio of original photo):');?></label>
            <select id="<?php echo $this->get_field_id('featured_pet_image'); ?>" name="<?php echo $this->get_field_name('featured_pet_image'); ?>">
            <option value="t" <?php echo $featured_pet_image == "t" ? "selected='selected'" : ""?>>scaled to 50 pixels tall</option>
            <option value="pnt" <?php echo $featured_pet_image == "pnt" ? "selected='selected'" : ""?>>scaled to 60 pixels wide</option>
            <option value="fpm" <?php echo $featured_pet_image == "fpm" ? "selected='selected'" : ""?>>scaled to 95 pixels wide</option>
            <option value="x" <?php echo $featured_pet_image == "x" ? "selected='selected'" : ""?>>original, up to 500x500</option>
            <option value="pn" <?php echo $featured_pet_image == "pn" ? "selected='selected'" : ""?>>up to 320x250</option>
        </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('featured_pet_copy_size')?>"><?php _e('Featured Pet Copy Size (enter number of characters, 0 to display full description, or -1 to not display description )');?></label>
            <input type="text" id="<?php echo $this->get_field_id('featured_pet_copy_size') ?>" name="<?php echo $this->get_field_name('featured_pet_copy_size') ?>" value="<?php echo $featured_pet_copy_size ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('featured_pdf_link'); ?>"><?php _e('Featured Pet PDF Link (Enter link to a separately uploaded PDF if you want to upload a PDF with more details about featured pet.):'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('featured_pdf_link') ?>" name="<?php echo $this->get_field_name('featured_pdf_link') ?>" value="<?php echo $featured_pdf_link ?>" />
		</p>
        <?php
    }

}

add_action('widgets_init', create_function('', 'return register_widget("Petfinder_Listings_Featured_Pet");'));

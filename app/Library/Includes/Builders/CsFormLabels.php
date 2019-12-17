<?php namespace CsSeoMegaBundlePack\Library\Includes\Builders;
/**
 * Library : Form Generator
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Helper;

class CsFormLabels {
    
    /**
     * 
     * @param type $og_type
     * @return string
     */
    public static function get_og_type_label( $og_type ){
        $label = '';
        switch( $og_type ){
            case 'article':
                $label = __( 'Article', SR_TEXTDOMAIN);
            break;
            case 'book':
                $label = __( 'Book', SR_TEXTDOMAIN);
            break;
            case 'books.author':
                $label = __( 'Book Author', SR_TEXTDOMAIN);
            break;
            case 'books.book':
                $label = __( 'Book Item', SR_TEXTDOMAIN);
            break;
            case 'business.business':
                $label = __( 'Business', SR_TEXTDOMAIN);
            break;
            case 'business.business':
                $label = __( 'Business', SR_TEXTDOMAIN);
            break;
            case 'fitness.course':
                $label = __( 'Fitness Course', SR_TEXTDOMAIN);
            break;
            case 'game.achievement':
                $label = __( 'Game Achievement', SR_TEXTDOMAIN);
            break;
            case 'music.album':
                $label = __( 'Music Album', SR_TEXTDOMAIN);
            break;
            case 'music.playlist':
                $label = __( 'Music Playlist', SR_TEXTDOMAIN);
            break;
            case 'music.radio_station':
                $label = __( 'Music Radio Station', SR_TEXTDOMAIN);
            break;
            case 'music.song':
                $label = __( 'Music Song', SR_TEXTDOMAIN);
            break;
            case 'place':
                $label = __( 'Place', SR_TEXTDOMAIN);
            break;
            case 'product':
                $label = __( 'Product', SR_TEXTDOMAIN);
            break;
            case 'product.group':
                $label = __( 'Product Group', SR_TEXTDOMAIN);
            break;
            case 'product.group':
                $label = __( 'Product Group', SR_TEXTDOMAIN);
            break;
            case 'product.item':
                $label = __( 'Product Item', SR_TEXTDOMAIN);
            break;
            case 'profile':
                $label = __( 'Profile', SR_TEXTDOMAIN);
            break;
            case 'restaurant.restaurant':
                $label = __( 'Restaurant', SR_TEXTDOMAIN);
            break;
            case 'restaurant.menu':
                $label = __( 'Restaurant Menu', SR_TEXTDOMAIN);
            break;
            case 'restaurant.menu_item':
                $label = __( 'Restaurant Menu Item', SR_TEXTDOMAIN);
            break;
            case 'restaurant.menu_section':
                $label = __( 'Restaurant Menu Section', SR_TEXTDOMAIN);
            break;
            case 'video.episode':
                $label = __( 'Video Episode', SR_TEXTDOMAIN);
            break;
            case 'video.movie':
                $label = __( 'Video Movie', SR_TEXTDOMAIN);
            break;
            case 'video.other':
                $label = __( 'Video Other', SR_TEXTDOMAIN);
            break;
            case 'video.tv_show':
                $label = __( 'Video Tv Show', SR_TEXTDOMAIN);
            break;
            case 'website':
                $label = __( 'Website', SR_TEXTDOMAIN);
            break;
        
            default:
                break;
        }
        return $label;
    }
    
    /**
     * Get og labels
     * 
     * @param type $label_id
     * @return stringForm og labels
     */
    public static function get_og_labels( $label_id ){
        $label = array();
        switch( $label_id ){
            
            case 'product:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a product. This includes both virtual and physical products, but it typically represents items that are available in an online store.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'product:availability':
                $label = array(
                    'label'    =>  __( 'Select Product Availability(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'Please Select Product availability.', SR_TEXTDOMAIN)
                );
            break;
            case 'product:brand':
                $label = array(
                    'label'    =>  __( 'Product Brand', SR_TEXTDOMAIN),
                    'helptext' => __( 'The brand of the product or its original manufacturer. Example: Rex', SR_TEXTDOMAIN)
                );
            break;
            case 'product:category':
                $label = array(
                    'label'    =>  __( 'Product Category', SR_TEXTDOMAIN),
                    'helptext' => __( 'A category for the item. Example: Tshirt', SR_TEXTDOMAIN)
                );
            break;
            case 'product:color':
                $label = array(
                    'label'    =>  __( 'Product Color', SR_TEXTDOMAIN),
                    'helptext' => __( 'The color of the item. Example: black, white, red', SR_TEXTDOMAIN)
                );
            break;
            case 'product:condition':
                $label = array(
                    'label'    =>  __( 'Product Condition(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'The condition of the item.', SR_TEXTDOMAIN)
                );
            break;
            case 'product:material':
                $label = array(
                    'label'    =>  __( 'Product Materials', SR_TEXTDOMAIN),
                    'helptext' => __( 'The material used to make the item.(if available)', SR_TEXTDOMAIN)
                );
            break;
            case 'product:retailer_item_id':
                $label = array(
                    'label'    =>  __( 'Product Retailer Item ID(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'The retailer\'s ID for the item', SR_TEXTDOMAIN)
                );
            break;
            case 'product:price:amount':
                $label = array(
                    'label'    =>  __( 'Product Price(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'The price of your product. Example: 99.99', SR_TEXTDOMAIN)
                );
            break;
            case 'product:price:currency':
                $label = array(
                    'label'    =>  __( 'Price Currency(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'The currency of the price of the item. Example: usd', SR_TEXTDOMAIN)
                );
            break;
            case 'product:sale_price:amount':
                $label = array(
                    'label'    =>  __( 'Product Sale Price', SR_TEXTDOMAIN),
                    'helptext' => __( 'The sale price of your product. Example: 95', SR_TEXTDOMAIN)
                );
            break;
            case 'product:sale_price:currency':
                $label = array(
                    'label'    =>  __( 'Sale Price Currency(*)', SR_TEXTDOMAIN),
                    'helptext' => __( 'The currency of the sale price of the item. Example: usd', SR_TEXTDOMAIN)
                );
            break;
            case 'product:sale_price_dates:start':
                $label = array(
                    'label'    =>  __( 'Product Sale Price Start', SR_TEXTDOMAIN),
                    'helptext' => __( "The starting date and time of the sale (if available).", SR_TEXTDOMAIN)
                );
            break;
            case 'product:sale_price_dates:end':
                $label = array(
                    'label'    =>  __( 'Product Sale Price End', SR_TEXTDOMAIN),
                    'helptext' => __( "The ending date and time of the sale (if available).", SR_TEXTDOMAIN)
                );
            break;
            case 'product:size':
                $label = array(
                    'label'    =>  __( 'Product Size', SR_TEXTDOMAIN),
                    'helptext' => __( "The size of the item (such as 'S', 'M', 'L') (if available).", SR_TEXTDOMAIN)
                );
            break;
            case 'product:weight:value':
                $label = array(
                    'label'    =>  __( 'Product Weight', SR_TEXTDOMAIN),
                    'helptext' => __( "A value representing the weight of the product (if available). Example: 5", SR_TEXTDOMAIN)
                );
            break;
            case 'product:weight:units':
                $label = array(
                    'label'    =>  __( 'Product Units', SR_TEXTDOMAIN),
                    'helptext' => __( "The units of the weight of the product (if available). Example: Kg", SR_TEXTDOMAIN)
                );
            break;
            
            case 'product.group:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a group of product items.', SR_TEXTDOMAIN)
                );
            break;
            case 'product:retailer_group_id':
                $label = array(
                    'label'    =>  __( 'Retailer Group ID', SR_TEXTDOMAIN),
                    'helptext' => __( "The retailer's ID for the product group.", SR_TEXTDOMAIN)
                );
            break;
            case 'product.item:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a product item.', SR_TEXTDOMAIN)
                );
            break;
        
            //og book start
            case 'book:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a book or publication. This is an appropriate type for ebooks, as well as traditional paperback or hardback books. Do not use this type to represent magazines.', SR_TEXTDOMAIN)
                );
            break;
            case 'book:author':  
                $label = array(
                    'label'    =>  __( 'Book Author', SR_TEXTDOMAIN),
                    'helptext' => __( "Facebook IDs of the users that authored the book. Example: Logan, Musk", SR_TEXTDOMAIN)
                );
            break;
            case 'book:isbn':
                $label = array(
                    'label'    =>  __( 'Book ISBN', SR_TEXTDOMAIN),
                    'helptext' => __( "The International Standard Book Number (ISBN) for the book. Example: 978-3-16-148410-0", SR_TEXTDOMAIN)
                );
            break;
            
            case 'book:release_date':
                $label = array(
                    'label'    =>  __( 'Book Release Date', SR_TEXTDOMAIN),
                    'helptext' => __( "The time when the book was released. Example: 04/15/2018", SR_TEXTDOMAIN)
                );
            break;
            
            case 'book:tag':
                $label = array(
                    'label'    =>  __( 'Book Tag', SR_TEXTDOMAIN),
                    'helptext' => __( "Keywords relevant to the book. Example: tech, seo, tutorial", SR_TEXTDOMAIN)
                );
            break;
            case 'rating:value':
                $label = array(
                    'label'    =>  __( 'Books Rating', SR_TEXTDOMAIN),
                    'helptext' => __( "The value of the rating given to the book. Example: 4.5", SR_TEXTDOMAIN)
                );
            break;
            case 'rating:scale':
                $label = array(
                    'label'    =>  __( 'Books Rating Scale', SR_TEXTDOMAIN),
                    'helptext' => __( "The highest value possible in the rating scale. Example: 5", SR_TEXTDOMAIN)
                );
            break;
            case 'books.author:main_description':
                $label = array(
                    'label'    =>  __( 'This Open Graph type represents a single author of a book.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'books:book':
                $label = array(
                    'label'    =>  __( 'Books Reference', SR_TEXTDOMAIN),
                    'helptext' => __( "References to the objects representing the books that the author has written", SR_TEXTDOMAIN)
                );
            break;
            case 'books:gender':
                $label = array(
                    'label'    =>  __( 'Author Gender', SR_TEXTDOMAIN),
                    'helptext' => __( "The author's gender. Example: 'female' or 'male'", SR_TEXTDOMAIN)
                );
            break;
            case 'books:genre':
                $label = array(
                    'label'    =>  __( 'Book\'s Genre', SR_TEXTDOMAIN),
                    'helptext' => __( "The genres of books that the author typically writes. Example: Horror, Funny", SR_TEXTDOMAIN)
                );
            break;
            case 'books:official_site':
                $label = array(
                    'label'    =>  __( 'Author Website', SR_TEXTDOMAIN),
                    'helptext' => __( "A URL for the author's official website. Example: http://example.com ", SR_TEXTDOMAIN)
                );
            break;
            case 'books:author':
                $label = array(
                    'label'    =>  __( 'Book\'s Author', SR_TEXTDOMAIN),
                    'helptext' => __( "The authors of the book. Example: Del Carnegie ", SR_TEXTDOMAIN)
                );
            break;
            case 'books:isbn':
                $label = array(
                    'label'    =>  __( 'Book\'s ISBN', SR_TEXTDOMAIN),
                    'helptext' => __( "The International Standard Book Number (ISBN) for the book. Example: 978-3-16-148410-0", SR_TEXTDOMAIN)
                );
            break;
            case 'books:language:locale':
                $label = array(
                    'label'    =>  __( 'Book\'s Language', SR_TEXTDOMAIN),
                    'helptext' => __( "The language of the book. Example: english", SR_TEXTDOMAIN)
                );
            break;
            case 'books:page_count':
                $label = array(
                    'label'    =>  __( 'Book\'s Page Count', SR_TEXTDOMAIN),
                    'helptext' => __( "Number of the page in the book. Example: 500", SR_TEXTDOMAIN)
                );
            break;
           case 'books:release_date':
                $label = array(
                    'label'    =>  __( 'Book\'s Release Date', SR_TEXTDOMAIN),
                    'helptext' => __( "The time when the book was released. Example: 04/15/2018", SR_TEXTDOMAIN)
                );
            break;
           case 'books:sample':
                $label = array(
                    'label'    =>  __( 'Book\'s Sample', SR_TEXTDOMAIN),
                    'helptext' => __( "A URL of a sample of the book. Example: http://example.com/book-sample", SR_TEXTDOMAIN)
                );
            break;
            case 'books:initial_release_date':
                $label = array(
                    'label'    =>  __( 'Book Release Date', SR_TEXTDOMAIN),
                    'helptext' => __( "The time when the book was released. Example: 04/15/2018", SR_TEXTDOMAIN)
                );
            break;
            case 'books.book:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a book or publication. This is an appropriate type for ebooks, as well as traditional paperback or hardback books.', SR_TEXTDOMAIN)
                );
            break;
            case 'books.book:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represents a place of business that has a location, operating hours and contact information.', SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:street_address':
                $label = array(
                    'label'    =>  __( 'Business Address(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "The number and street of the postal address for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:locality':
                $label = array(
                    'label'    =>  __( 'Business City(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "The city (or locality) line of the postal address for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:region':
                $label = array(
                    'label'    =>  __( 'Business Region', SR_TEXTDOMAIN),
                    'helptext' => __( "The state (or region) line of the postal address for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:postal_code':
                $label = array(
                    'label'    =>  __( 'Business Postcode(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "The postcode (or ZIP code) of the postal address for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:country_name':
                $label = array(
                    'label'    =>  __( 'Business Country(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "The country of the postal address for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:email':
                $label = array(
                    'label'    =>  __( 'Business Email', SR_TEXTDOMAIN),
                    'helptext' => __( "An email address to contact this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:phone_number':
                $label = array(
                    'label'    =>  __( 'Business Phone', SR_TEXTDOMAIN),
                    'helptext' => __( "	A telephone number to contact this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:fax_number':
                $label = array(
                    'label'    =>  __( 'Business FAX', SR_TEXTDOMAIN),
                    'helptext' => __( "A fax number to contact this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:contact_data:website':
                $label = array(
                    'label'    =>  __( 'Business Website', SR_TEXTDOMAIN),
                    'helptext' => __( "A website for this business.", SR_TEXTDOMAIN)
                );
            break;
            case 'business:hours:day':
                $label = array(
                    'label'    =>  __( 'A day in week', SR_TEXTDOMAIN),
                    'helptext' => __( "A day in week. Example: monday, tuesday, wednesday", SR_TEXTDOMAIN)
                );
            break;
            case 'business:hours:start':
                $label = array(
                    'label'    =>  __( 'Opening Hours', SR_TEXTDOMAIN),
                    'helptext' => __( "Business Opening time. ", SR_TEXTDOMAIN)
                );
            break;
            case 'business:hours:end':
                $label = array(
                    'label'    =>  __( 'Closing Hours', SR_TEXTDOMAIN),
                    'helptext' => __( "Business Closing time. ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness.course:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type the user\'s activity contributing to a particular run, walk, or bike course..', SR_TEXTDOMAIN)
                );
            break;
           
            case 'fitness:calories':
                $label = array(
                    'label'    =>  __( 'Calory Used', SR_TEXTDOMAIN),
                    'helptext' => __( "The number of calories used during the course. ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:custom_unit_energy:value':
                $label = array(
                    'label'    =>  __( 'Energy Used', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the energy used during the course, measured in a custom unit. Example: 300 ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:custom_unit_energy:units':
                $label = array(
                    'label'    =>  __( 'Energy Units Used', SR_TEXTDOMAIN),
                    'helptext' => __( "A custom unit of the energy used during the course. Example: kj ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:distance:value':
                $label = array(
                    'label'    =>  __( 'Distance', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the distance covered during the course. Example: 200 ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:distance:units':
                $label = array(
                    'label'    =>  __( 'Distance Units', SR_TEXTDOMAIN),
                    'helptext' => __( "The unit of the value representing the distance covered during the course. Example: km/s ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:duration:value':
                $label = array(
                    'label'    =>  __( 'Duration', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the duration of the course. Example: 50 ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:duration:units':
                $label = array(
                    'label'    =>  __( 'Duration Units', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the duration units of the course. Example: mins ", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:live_text':
                $label = array(
                    'label'    =>  __( 'Encouragement Text', SR_TEXTDOMAIN),
                    'helptext' => __( "A string value displayed in stories if the associated action's end_time has not passed, such as an encouragement to friends to cheer the user on. The value is not rendered once the course has been completed.", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:pace:value':
                $label = array(
                    'label'    =>  __( 'Pace', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the pace achieved during the course.", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:pace:units':
                $label = array(
                    'label'    =>  __( 'Pace', SR_TEXTDOMAIN),
                    'helptext' => __( "The unit of the value representing the pace achieved during the course.", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:speed:value':
                $label = array(
                    'label'    =>  __( 'Speed', SR_TEXTDOMAIN),
                    'helptext' => __( "A quantity representing the speed achieved during the course.", SR_TEXTDOMAIN)
                );
            break;
            case 'fitness:speed:units':
                $label = array(
                    'label'    =>  __( 'Speed Units', SR_TEXTDOMAIN),
                    'helptext' => __( "The unit of the value representing the speed achieved during the course.", SR_TEXTDOMAIN)
                );
            break;
             case 'game.achievement:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a specific achievement in a game. An app must be in the \'Games\' category in App Dashboard to be able to use this object type. Every achievement has a game:points value associate with it. This is not related to the points the user has scored in the game, but is a way for the app to indicate the relative importance and scarcity of different achievements: * Each game gets a total of 1,000 points to distribute across its achievements * Each game gets a maximum of 1,000 achievements * Achievements which are scarcer and have higher point values will receive more distribution in Facebook\'s social channels. For example, achievements which have point values of less than 10 will get almost no distribution. Apps should aim for between 50-100 achievements consisting of a mix of 50 (difficult), 25 (medium), and 10 (easy) point value achievements', SR_TEXTDOMAIN)
                );
            break;
            case 'game:points':
                $label = array(
                    'label'    =>  __( 'Game Points', SR_TEXTDOMAIN),
                    'helptext' => __( "The relative importance and scarcity of the achievement, as described above.", SR_TEXTDOMAIN)
                );
            break;
            case 'music.album:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a music album; in other words, an ordered collection of songs from an artist or a collection of artists. An album can comprise multiple discs.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'music:musician':
                $label = array(
                    'label'    =>  __( 'Musician Profile IDs/ Url', SR_TEXTDOMAIN),
                    'helptext' => __( "The Facebook IDs (or references to the profiles) of the artists responsible for the album.", SR_TEXTDOMAIN)
                );
            break;
            case 'music:release_date':
                $label = array(
                    'label'    =>  __( 'Album Released Date Time', SR_TEXTDOMAIN),
                    'helptext' => __( "A time representing when the album was released.", SR_TEXTDOMAIN)
                );
            break;
            case 'music:release_type':
                $label = array(
                    'label'    =>  __( 'Released Type', SR_TEXTDOMAIN),
                    'helptext' => __( "The type of the album's release; one of 'original_release', 're_release', or 'anthology'.", SR_TEXTDOMAIN)
                );
            break;
            case 'music:song:url':
                $label = array(
                    'label'    =>  __( 'Song Url(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "Each song url on the album. Example: http://example.com/song1, http://example.com/song2", SR_TEXTDOMAIN)
                );
            break;
            case 'music:song:disc':
                $label = array(
                    'label'    =>  __( 'Song Disk', SR_TEXTDOMAIN),
                    'helptext' => __( "The disc (within the album) that each of the songs are on, defaulting to to '1'", SR_TEXTDOMAIN)
                );
            break;
            case 'music:song:track':
                $label = array(
                    'label'    =>  __( 'Song Track', SR_TEXTDOMAIN),
                    'helptext' => __( "The position (within a given disc) of each of the songs", SR_TEXTDOMAIN)
                );
            break;
            case 'music.playlist:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a music playlist, an ordered collection of songs from a collection of artists.', SR_TEXTDOMAIN)
                );
            break;
            case 'music:creator':
                $label = array(
                    'label'    =>  __( 'Creator Profile', SR_TEXTDOMAIN),
                    'helptext' => __( "A Facebook ID (or reference to the profile) of the creator of the playlist", SR_TEXTDOMAIN)
                );
            break;
            case 'music:song:url':
                $label = array(
                    'label'    =>  __( 'Song Url(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "Url of each song on the playlist.Example: http://example.com/song1, http://example.com/song2", SR_TEXTDOMAIN)
                );
            break;
            case 'music:song_count':
                $label = array(
                    'label'    =>  __( 'Number of Song', SR_TEXTDOMAIN),
                    'helptext' => __( "The number of songs in the playlist. Example: 10", SR_TEXTDOMAIN)
                );
            break;
            case 'music.radio_station:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a \'radio\' station of a stream of audio. The audio properties should be used to identify the location of the stream itself.', SR_TEXTDOMAIN)
                );
            break;
            case 'music.song:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a single song.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'music:album:url':
                $label = array(
                    'label'    =>  __( 'Album Url(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "Url of the album that contains the song. Example: http://example.com/album", SR_TEXTDOMAIN)
                );
            break;
            case 'music:album:disc':
                $label = array(
                    'label'    =>  __( 'Disk No', SR_TEXTDOMAIN),
                    'helptext' => __( "The disc (within the album) that the song is on, defaulting to to '1'", SR_TEXTDOMAIN)
                );
            break;
            case 'music:album:track':
                $label = array(
                    'label'    =>  __( 'Track', SR_TEXTDOMAIN),
                    'helptext' => __( "The position (within the given disc) of the song", SR_TEXTDOMAIN)
                );
            break;
            case 'music:duration':
                $label = array(
                    'label'    =>  __( 'Song Duration', SR_TEXTDOMAIN),
                    'helptext' => __( "The length of the song in seconds. Example: 180", SR_TEXTDOMAIN)
                );
            break;
            case 'place:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a place - such as a venue, a business, a landmark, or any other location which can be identified by longitude and latitude.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'place:location:latitude':
                $label = array(
                    'label'    =>  __( 'Address GEO Latitue', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide GEO location of the place.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:location:longitude':
                $label = array(
                    'label'    =>  __( 'Address GEO Longtitude', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide GEO location of the place.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:location:altitude':
                $label = array(
                    'label'    =>  __( 'Location Altitude', SR_TEXTDOMAIN),
                    'helptext' => __( "The altitude of the place, in meters above sea level.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:street_address':
                $label = array(
                    'label'    =>  __( 'Location Street Address', SR_TEXTDOMAIN),
                    'helptext' => __( "The Location Street address.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:locality':
                $label = array(
                    'label'    =>  __( 'City', SR_TEXTDOMAIN),
                    'helptext' => __( "The city (or locality) line of the postal address for this place.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:region':
                $label = array(
                    'label'    =>  __( 'State', SR_TEXTDOMAIN),
                    'helptext' => __( "The state (or region) line of the postal address for this place.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:postal_code':
                $label = array(
                    'label'    =>  __( 'Postcode', SR_TEXTDOMAIN),
                    'helptext' => __( "The postcode (or ZIP code) of the postal address for this place.", SR_TEXTDOMAIN)
                );
            break;
            case 'place:country_name':
                $label = array(
                    'label'    =>  __( 'Country', SR_TEXTDOMAIN),
                    'helptext' => __( "The country of the postal address for this place.", SR_TEXTDOMAIN)
                );
            break;
            case 'profile:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a person. While appropriate for celebrities, artists, or musicians, this object type can be used for the profile of any individual. The <code>fb:profile_id</code> field associates the object with a Facebook user.', SR_TEXTDOMAIN)
                );
            break;
            case 'profile:first_name':
                $label = array(
                    'label'    =>  __( 'First name', SR_TEXTDOMAIN),
                    'helptext' => __( "The first name of the person that this profile represents.", SR_TEXTDOMAIN)
                );
            break;
            case 'profile:last_name':
                $label = array(
                    'label'    =>  __( 'Last name', SR_TEXTDOMAIN),
                    'helptext' => __( "The last name of the person that this profile represents.", SR_TEXTDOMAIN)
                );
            break;
            case 'profile:username':
                $label = array(
                    'label'    =>  __( 'Username', SR_TEXTDOMAIN),
                    'helptext' => __( "A username for the person that this profile represents.", SR_TEXTDOMAIN)
                );
            break;
            case 'profile:gender':
                $label = array(
                    'label'    =>  __( 'Gender', SR_TEXTDOMAIN),
                    'helptext' => __( "The gender ('female' or 'male') of the person that this profile represents.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant.menu:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a restaurant\'s menu. A restaurant can have multiple menus, and each menu has multiple sections..', SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:restaurant':
                $label = array(
                    'label'    =>  __( 'Restaurant url(*)', SR_TEXTDOMAIN),
                    'helptext' => __( "The url of restaurant that uses this menu. Example: http://restaurant.com", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:section':
                $label = array(
                    'label'    =>  __( 'Menu Section url', SR_TEXTDOMAIN),
                    'helptext' => __( "The sections ul within the menu. Example: http://restaurant.com/menu/section", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant.menu_item:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a single item on a restaurant\'s menu. Every item belongs within a menu section.', SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:variation:name':
                $label = array(
                    'label'    =>  __( 'Variation Name', SR_TEXTDOMAIN),
                    'helptext' => __( "Different variations of this item. Example: variations1, variations2", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:variation:price:amount':
                $label = array(
                    'label'    =>  __( 'Variation Price', SR_TEXTDOMAIN),
                    'helptext' => __( "Price Different variations of this item. Example: 50", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:variation:price:currency':
                $label = array(
                    'label'    =>  __( 'Price Currency', SR_TEXTDOMAIN),
                    'helptext' => __( "Currencies of different variations of this item. Example: usd", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant.menu_section:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a  section in a restaurant\'s menu. A section contains multiple menu items.', SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:menu':
                $label = array(
                    'label'    =>  __( 'Menu Url', SR_TEXTDOMAIN),
                    'helptext' => __( "Menu Url containing this section.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:item':
                $label = array(
                    'label'    =>  __( 'Menu items', SR_TEXTDOMAIN),
                    'helptext' => __( "The items within this section of the menu.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant.restaurant:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a restaurant at a specific location.', SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:menu':
                $label = array(
                    'label'    =>  __( 'Menu Url', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide a URL to a page with open graph data of type Restaurant Menu.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:street_address':
                $label = array(
                    'label'    =>  __( 'Street Address', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide the street address of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:locality':
                $label = array(
                    'label'    =>  __( 'City', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide the city of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:region':
                $label = array(
                    'label'    =>  __( 'State / Province / Region', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide State / Province / Region of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:postal_code':
                $label = array(
                    'label'    =>  __( 'Postal / Zip Code', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide Postal / Zip Code of the restaurant address.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:country_name':
                $label = array(
                    'label'    =>  __( 'Country', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide country of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:email':
                $label = array(
                    'label'    =>  __( 'Contact Email', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide contact email of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:phone_number':
                $label = array(
                    'label'    =>  __( 'Contact Phone', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide contact phone number of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:fax_number':
                $label = array(
                    'label'    =>  __( 'Contact Fax', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide contact fax number of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            case 'restaurant:contact_info:website':
                $label = array(
                    'label'    =>  __( 'Contact Website', SR_TEXTDOMAIN),
                    'helptext' => __( "Provide contact website address of the restaurant.", SR_TEXTDOMAIN)
                );
            break;
            
            case 'video.episode:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent an episode of a TV show and contains references to the actors and other professionals involved in its production. An episode is defined by us as a full-length episode that is part of a series. This type must reference the series this it is part of.', SR_TEXTDOMAIN)
                );
            break;
            
            case 'video:actor:id':
                $label = array(
                    'label'    =>  __( 'Actor Profile Urls', SR_TEXTDOMAIN),
                    'helptext' => __( "Facebook IDs (or references to the profiles url) of the actors in the episode. Example: http://example.com/actor1, http://example.com/actor2 ", SR_TEXTDOMAIN)
                );
            break;
            case 'video:actor:role':
                $label = array(
                    'label'    =>  __( 'Actor Roles', SR_TEXTDOMAIN),
                    'helptext' => __( "Roles played by the actors in the episode. Example: hero, heroin", SR_TEXTDOMAIN)
                );
            break;
            case 'video:director':
                $label = array(
                    'label'    =>  __( 'Director Profile Urls', SR_TEXTDOMAIN),
                    'helptext' => __( "The Facebook IDs (or references to the profiles url) of the directors of the episode. Example: http://example.com/director1, http://example.com/director2", SR_TEXTDOMAIN)
                );
            break;
            case 'video:duration':
                $label = array(
                    'label'    =>  __( 'Video Duration', SR_TEXTDOMAIN),
                    'helptext' => __( "The length of the episode in seconds. Example: 180", SR_TEXTDOMAIN)
                );
            break;
            case 'video:release_date':
                $label = array(
                    'label'    =>  __( 'Video Release Date', SR_TEXTDOMAIN),
                    'helptext' => __( "A time representing when the episode was released.", SR_TEXTDOMAIN)
                );
            break;
            case 'video:series':
                $label = array(
                    'label'    =>  __( 'Video Series Name', SR_TEXTDOMAIN),
                    'helptext' => __( "A reference to the video representing the TV show this episode is part of.", SR_TEXTDOMAIN)
                );
            break;
            case 'video:tag':
                $label = array(
                    'label'    =>  __( 'Video Tags', SR_TEXTDOMAIN),
                    'helptext' => __( "Keywords relevant to the episode. Example: Commic, funny", SR_TEXTDOMAIN)
                );
            break;
            case 'video:writer':
                $label = array(
                    'label'    =>  __( 'Video Writers', SR_TEXTDOMAIN),
                    'helptext' => __( "The Facebook IDs (or references to the profiles Url) of the writers of the episode.", SR_TEXTDOMAIN)
                );
            break;
            case 'video.movie:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a movie, and contains references to the actors and other professionals involved in its production. A movie is defined by us as a full-length feature or short film. Do not use this type to represent movie trailers, movie clips, user-generated video content, etc.', SR_TEXTDOMAIN)
                );
            break;
            case 'video.other:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a generic video, and contains references to the actors and other professionals involved in its production. For specific types of video content, use the video.movie or video.tv_show object types. This type is for any other type of video content not represented elsewhere (eg. trailers, music videos, clips, news segments etc.)', SR_TEXTDOMAIN)
                );
            break;
            case 'video.tv_show:main_description':
                $label = array(
                    'label'    =>  __( 'This open graph type represent a TV show, and contains references to the actors and other professionals involved in its production. For individual episodes of a series, use the video.episode object type. A TV show is defined by us as a series or set of episodes that are produced under the same title (eg. a television or online series).', SR_TEXTDOMAIN)
                );
            break;
        
        
            default: 
                break;
        };
        return $label;
    }
    
}

<?php namespace CsSeoMegaBundlePack\HelperFunctions;
/**
 * General Functions
 * 
 * @package All in One Seo 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

if ( ! defined( 'CSMBP_VERSION' ) ) {
	exit;
}

use CsSeoMegaBundlePack\Library\Backend\Messages\BackendMessages;

class GeneralHelpers{
    
    protected static $is_mobile;			// is_mobile cached value
    protected static $mobile_obj;			// SuextMobileDetect class object
    protected static $active_plugins;		// active site and network plugins
    protected static $active_site_plugins;
    protected static $active_network_plugins;
    protected static $crawler_name;			// saved crawler name from user-agent
    protected static $filter_values = array();	// saved filter values
    protected static $user_exists = array();	// saved user_exists() values
    protected static $locales = array();		// saved get_locale() values

    /**
     * Generate Random String
     * 
     * @param type $length
     * @return type
     */
    public static function Cs_Random_String( $length = 10 ) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }            
    
    /**
     * Cs Md5 Hash Generator
     * 
     * @param type $string
     * @return boolean
     */
    public static function Cs_Md5_Hash( $string ){
        if( empty($string) ) return false;
        return md5( $string );
    }

    /**
     * Encode Anything
     * 
     * @param type $value
     * @return String
     */
    public static function Cs_Encode($value) {
        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'tuhin65945$%df&!', $text, MCRYPT_MODE_ECB, $iv);
        return trim(self::makeIronMan($crypttext));
    }

    private static function makeIronMan($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }
    /**

     * Decode Anything
     * 
     * @param type $value
     * @return String
     */
    public static function Cs_Decode($value) {
        if (!$value) {
            return false;
        }
        $crypttext = self::makeNormalMan($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'tuhin65945$%df&!', $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    private static function makeNormalMan($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
    
    /**
     * Get Visitor Real IP Address
     * 
     * @return string
     */
    public static function Cs_Real_Ip_Addr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (!self::valid_ip($ip)) {
            $ip = '0.0.0.0';
        }
        return $ip;
    }

    private static function valid_ip($ip) {
        $ip_segments = explode('.', $ip);

        // Always 4 segments needed
        if (count($ip_segments) != 4) {
            return FALSE;
        }
        // IP can not start with 0
        if ($ip_segments[0][0] == '0') {
            return FALSE;
        }
        // Check each segment
        foreach ($ip_segments as $segment) {
            // IP segments must be digits and can not be
            // longer than 3 digits or greater then 255
            if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3) {
                return FALSE;
            }
        }

        return TRUE;
    }
    
    /**
     * Get User Agent
     * 
     * @return boolean
     */
    public static function Cs_User_Agent() {
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
                return $_SERVER['HTTP_USER_AGENT'];
        return false;
    }

    /**
     * Get User Referrer
     * 
     * @return boolean
     */
    public static  function Cs_User_Referrer() {
        if ( isset( $_SERVER['HTTP_REFERER'] ) )
                return $_SERVER['HTTP_REFERER'];
        return false;
    }
    
    /**
     * Color Code
     * 
     * @return array
     */
    public static function Cs_Color_Code(){
        return array("#6CF", "#EA8422", "#365EBF", "#51A351", "#000", "#644183","#3b5998","#dd4b39","#394B65","#F5900E", "#55a32d", "#174558", "#E24547","#C82D28","#3d999e","#C1D900","#5E5E5E","#af0606","#7BC0FF");
    }
    
    /**
     * Google Domains
     * 
     * @return type
     */
    public static function google_domains(){
        return array( 'www.google.com', 'www.google.ad', 'www.google.ae', 'www.google.com.af', 'www.google.com.ag', 'www.google.com.ai', 'www.google.am', 'www.google.co.ao', 'www.google.com.ar', 'www.google.as', 'www.google.at', 'www.google.com.au', 'www.google.az', 'www.google.ba', 'www.google.com.bd', 'www.google.be', 'www.google.bf', 'www.google.bg', 'www.google.com.bh', 'www.google.bi', 'www.google.bj', 'www.google.com.bn', 'www.google.com.bo', 'www.google.com.br', 'www.google.bs', 'www.google.co.bw', 'www.google.by', 'www.google.com.bz', 'www.google.ca', 'www.google.cd', 'www.google.cf', 'www.google.cg', 'www.google.ch', 'www.google.ci', 'www.google.co.ck', 'www.google.cl', 'www.google.cm', 'www.google.cn', 'www.google.com.co', 'www.google.co.cr', 'www.google.com.cu', 'www.google.cv', 'www.google.com.cy', 'www.google.cz', 'www.google.de', 'www.google.dj', 'www.google.dk', 'www.google.dm', 'www.google.com.do', 'www.google.dz', 'www.google.com.ec', 'www.google.ee', 'www.google.com.eg', 'www.google.es', 'www.google.com.et', 'www.google.fi', 'www.google.com.fj', 'www.google.fm', 'www.google.fr', 'www.google.ga', 'www.google.ge', 'www.google.gg', 'www.google.com.gh', 'www.google.com.gi', 'www.google.gl', 'www.google.gm', 'www.google.gp', 'www.google.gr', 'www.google.com.gt', 'www.google.gy', 'www.google.com.hk', 'www.google.hn', 'www.google.hr', 'www.google.ht', 'www.google.hu', 'www.google.co.id', 'www.google.ie', 'www.google.co.il', 'www.google.im', 'www.google.co.in', 'www.google.iq', 'www.google.is', 'www.google.it', 'www.google.je', 'www.google.com.jm', 'www.google.jo', 'www.google.co.jp', 'www.google.co.ke', 'www.google.com.kh', 'www.google.ki', 'www.google.kg', 'www.google.co.kr', 'www.google.com.kw', 'www.google.kz', 'www.google.la', 'www.google.com.lb', 'www.google.li', 'www.google.lk', 'www.google.co.ls', 'www.google.lt', 'www.google.lu', 'www.google.lv', 'www.google.com.ly', 'www.google.co.ma', 'www.google.md', 'www.google.me', 'www.google.mg', 'www.google.mk', 'www.google.ml', 'www.google.mn', 'www.google.ms', 'www.google.com.mt', 'www.google.mu', 'www.google.mv', 'www.google.mw', 'www.google.com.mx', 'www.google.com.my', 'www.google.co.mz', 'www.google.com.na', 'www.google.com.nf', 'www.google.com.ng', 'www.google.com.ni', 'www.google.ne', 'www.google.nl', 'www.google.no', 'www.google.com.np', 'www.google.nr', 'www.google.nu', 'www.google.co.nz', 'www.google.com.om', 'www.google.com.pa', 'www.google.com.pe', 'www.google.com.ph', 'www.google.com.pk', 'www.google.pl', 'www.google.pn', 'www.google.com.pr', 'www.google.ps', 'www.google.pt', 'www.google.com.py', 'www.google.com.qa', 'www.google.ro', 'www.google.ru', 'www.google.rw', 'www.google.com.sa', 'www.google.com.sb', 'www.google.sc', 'www.google.se', 'www.google.com.sg', 'www.google.sh', 'www.google.si', 'www.google.sk', 'www.google.com.sl', 'www.google.sn', 'www.google.so', 'www.google.sm', 'www.google.st', 'www.google.com.sv', 'www.google.td', 'www.google.tg', 'www.google.co.th', 'www.google.com.tj', 'www.google.tk', 'www.google.tl', 'www.google.tm', 'www.google.tn', 'www.google.to', 'www.google.com.tr', 'www.google.tt', 'www.google.com.tw', 'www.google.co.tz', 'www.google.com.ua', 'www.google.co.ug', 'www.google.co.uk', 'www.google.com.uy', 'www.google.co.uz', 'www.google.com.vc', 'www.google.co.ve', 'www.google.vg', 'www.google.co.vi', 'www.google.com.vn', 'www.google.vu', 'www.google.ws', 'www.google.rs', 'www.google.co.za', 'www.google.co.zm', 'www.google.co.zw', 'www.google.cat', 'www.google.xxx' );
    }
    
    /**
     * Google Place types
     * 
     * @return type
     */
    public static function google_place_types(){
        return array( 'airport', 'amusement_park', 'aquarium', 'art_gallery', 'atm', 'bakery', 'bank', 'bar', 'beauty_salon', 'bicycle_store', 'book_store', 'bowling_alley', 'bus_station', 'cafe', 'campground', 'car_dealer', 'car_rental', 'car_repair', 'car_wash', 'casino', 'cemetery', 'church', 'city_hall', 'clothing_store', 'convenience_store', 'courthouse', 'dentist', 'department_store', 'doctor', 'electrician', 'electronics_store', 'embassy', 'fire_station', 'florist', 'funeral_home', 'furniture_store', 'gas_station', 'gym', 'hair_care', 'hardware_store', 'hindu_temple', 'home_goods_store', 'hospital', 'insurance_agency', 'jewelry_store', 'laundry', 'lawyer', 'library', 'liquor_store', 'local_government_office', 'locksmith', 'lodging', 'meal_delivery', 'meal_takeaway', 'mosque', 'movie_rental', 'movie_theater', 'moving_company', 'museum', 'night_club', 'painter', 'park', 'parking', 'pet_store', 'pharmacy', 'physiotherapist', 'plumber', 'police', 'post_office', 'real_estate_agency', 'restaurant', 'roofing_contractor', 'rv_park', 'school', 'shoe_store', 'shopping_mall', 'spa', 'stadium', 'storage', 'store', 'subway_station', 'synagogue', 'taxi_stand', 'train_station', 'transit_station', 'travel_agency', 'university', 'veterinary_care', 'zoo', 'other');
    }
    
    /**
     * Default article topics
     * 
     * @return array
     */
    public static function Cs_Article_Topics(){
        return array( "Accounting", "Acting", "Action Movies", "Activism", "Adult Humor", "Advertising", "Africa", "African Americans", "Aging", "Agriculture", "A.I.", "AIDS", "Alcoholic Drinks", "Alternative Energy", "Alternative Health", "Alternative News", "Alternative Rock", "Amateur Radio", "Ambient Music", "American Football", "American History", "American Literature", "Anarchism", "Anatomy", "Ancient History", "Animals", "Animation", "Anime", "Anthropology", "Antiaging", "Antiques", "Archaeology", "Architecture", "Art", "Art History", "Arthritis", "Arts (The)", "Asia", "Asthma", "Astrology / Psychics", "Astronomy", "Atheist / Agnostic", "Audio Equipment", "Australia", "Automotive", "Aviation", "Aviation / Aerospace", "Babes", "Babies", "Badminton", "Ballet", "Banking", "Bargains / Coupons", "Baseball", "Basketball", "BDSM", "Beauty", "Beer", "Beverages", "Bicycling", "Billiards", "Biographies", "Biology", "Biomechanics", "Biotech", "Birds", "Bird Watching", "Bisexual Culture", "Bisexual Sex", "Bizarre / Oddities", "Blues music", "Board Games", "Boating", "Bodybuilding", "Books", "Botany", "Bowling", "Boxing", "Brain Disorders", "Brazil", "British Literature", "Britpop", "Buddhism", "Business", "C.A.D.", "Camping", "Canada", "Cancer", "Canoeing / Kayaking", "Capitalism", "Card Games", "Career planning", "Caribbean", "Car Parts", "Cars", "Cartoons", "Catholic", "Cats", "Celebrities", "Cell Phones", "Celtic Music", "Central America", "Chaos / Complexity", "Chat", "Cheerleading", "Chemical Engineering", "Chemistry", "Chess", "Children's Books", "Children's Issues", "China", "Christianity", "Christian Music", "Christmas", "Cigars", "Civil Engineering", "Classical Music", "Classical Studies", "Classic Films", "Classic Rock", "Climbing", "Clothing", "Coffee", "Cognitive Science", "Cold War", "Collecting", "Comedy Movies", "Comic Books", "Comics", "Commerce", "Communism", "Community", "Computer Graphics", "Computer Hardware", "Computers", "Computer Science", "Computer Security", "Conservative Politics", "Conspiracies", "Construction", "Consumer Info", "Continuing Education", "Counterculture", "Country music", "Crafts", "Cricket", "Crime", "Crochet", "Cult Films", "Culture / Ethnicity", "Cyberculture", "Dance", "Dance Music", "Dancing", "Databases", "Dating", "Dating Tips", "Daytrading", "Dentistry", "Design", "Desktop Publishing", "Diabetes", "Digital Media", "Disabilities", "Disco", "Divorce", "DJs / Mixing", "Doctors / Surgeons", "Documentary", "Dogs", "Dolls / Puppets", "Download", "Drama Movies", "Drawing", "Drugs", "Drum'n'Bass", "Eastern Studies", "Eating Disorders", "Ecology", "Ecommerce", "Economics", "Education", "Educational", "Electrical Engineering", "Electronica / IDM", "Electronic Devices", "Electronic Parts", "Embedded Systems", "Employment", "Encryption", "Energy Industry", "Entertaining Guests", "Entertainment", "Entrepreneurship", "Environment", "Environmental", "Equestrian / Horses", "Ergonomics", "Erotica and Pornography", "Erotic Literature", "Ethics", "Ethnic Music", "Europe", "Evolution", "Exotic Pets", "Extreme Sports", "Facebook", "Family", "Fantasy Books", "Fashion", "Feminism", "Fetish Sexuality", "Figure Skating", "File Sharing", "Filmmaking", "Film Noir", "Financial planning", "Fine Arts", "Firefox", "Fish", "Fishing", "Fitness", "Flyfishing", "Folk music", "Food and Drink", "Food / Cooking", "Foreign Films", "Forensics", "Forestry", "For Kids", "Forums", "France", "Fundraising", "Funk", "Futurism", "Gadgets", "Gambling", "Gardening", "Gay Culture", "Gay Sex", "Genealogy", "Genetics", "Geography", "Geoscience", "Germany", "Glaucoma", "Golf", "Gospel music", "Goth Culture", "Government", "Graphic Design", "Guitar", "Guns", "Gymnastics", "Hacking", "Health", "Heart Conditions", "Heavy metal", "Hedonism", "Hentai Anime", "Hiking", "Hinduism", "HipHop / Rap", "History", "Hockey", "Homebrewing", "Home Business", "Home Improvement", "Homemaking", "Homeschooling", "Horror Movies", "Hotels", "House music", "Humanitarianism", "Humanities", "Humor", "Hunting", "Independent Film", "India", "Indie Rock / Pop", "Industrial Design", "Industrial Music", "Instant Messaging", "Insurance", "Interior Design", "Internet", "Internet Tools", "International Development", "Investing", "Ipod", "Iraq", "Ireland", "Islam", "Israel", "IT", "Italy", "Japan", "Java", "Jazz", "Jewelry", "Journalism", "Judaism", "Karaoke", "Kids", "Kinesiology", "Knitting", "Korea", "Landscaping", "Latin Music", "Law", "Law Enforcement", "Learning Disorders", "Lefthanded", "Legal", "Lesbian Culture", "Lesbian Sex", "Liberal Politics", "Liberties / Rights", "Library Resources", "Lingerie", "Linguistics", "Linux / Unix", "Literature", "Live Theatre", "Logic", "Lounge Music", "Luxury", "Machinery", "MacOS", "Magic / Illusions", "Management / HR", "Manufacturing", "Marine Biology", "Marketing", "Married Life", "Martial Arts", "Matchmaking", "Mathematics", "Mechanical Engineering", "Medical", "Medical Science", "Medieval History", "Memorabilia", "Men's Issues", "Mental Health", "Meteorology", "Mexico", "Microbiology", "Middle East", "Military", "Mining / Metallurgy", "Mobile Computing", "Mormon", "Motorcycles", "Motor Sports", "Movies", "Multimedia", "Music", "Musicals", "Music Composition", "Musician Resources", "Music Instruments", "Music Theory", "Mutual Funds", "Mystery Novels", "Mythology", "Nanotech", "Native Americans", "Nature", "Netherlands", "Network Security", "Neuroscience", "New Age", "News", "News (General)", "New York", "Nightlife", "Nonprofit / Charity", "Nostalgia", "Nuclear Science", "Nude Art", "Nursing", "Nutrition", "Oceania", "Oldies Music", "Online Games", "Open Source", "Opera", "Operating Systems", "Options / Futures", "Orthodox", "Outdoors", "P2P", "Paganism", "Painting", "Paleontology", "Paranormal", "Parenting", "Percussion", "Performing Arts", "Peripheral Devices", "Perl", "Personal Sites", "Petroleum", "Pets", "Pharmacology", "Philosophy", "Photo Gear", "Photography", "Photoshop", "PHP", "Physical Therapy", "Physics", "Physiology", "Poetry", "Poker", "Political", "Political Science", "Politics", "Pop music", "Pornography", "Postmodernism", "Pregnancy / Birth", "Programming", "Protestant", "Proxy", "Psychiatry", "Psychology", "Punk Rock", "Puzzles", "Quilting", "Quizzes", "Quotes", "Racquetball", "Radio Broadcasts", "Rave Culture", "Real Estate", "Recording Gear", "Reggae", "Relationships", "Religion", "Religious", "Research", "Restaurants", "Restoration", "Review", "Reward", "Robotics", "Rock music", "Rodeo", "Roleplaying Games", "Romance Novels", "Route Planning", "Rugby", "Running", "Russia", "Sailing", "Satire", "Satirical", "Science", "Science Fiction", "Scientology", "Scouting", "Scrapbooking", "Scuba Diving", "Sculpting", "Search", "Self Improvement", "Semiconductors", "Senior Citizens", "SEO", "Sewing", "Sex Industry", "Sex Toys", "Sexual Health", "Sexuality", "Shakespeare", "Shareware", "Shock", "Shopping", "Skateboarding", "Skiing", "Skydiving", "Snowboarding", "Soap Operas", "Soccer", "Socialism", "Social Networking", "Sociology", "Software", "Songwriting", "Soul / R&B", "Soundtracks", "South America", "Space Exploration", "Spain", "Spas", "Spiritual", "Spirituality", "Sport", "Sports (General)", "Squash", "Statistics", "StumbleUpon", "Subculture", "Substance Abuse", "Sufism", "Sunni", "Supercomputing", "Surfing", "Survivalist", "Swimming", "Swingers", "Tattoos / Piercing", "Taxation", "Tea", "Techno", "Technology", "Teen Life", "Teen Parenting", "Telecom", "Television", "Tennis", "Terrorism", "Toys", "Track / Field", "Trains / Railroads", "Trance", "Transexual Sex", "Transportation", "Travel", "TripHop / Downtempo", "UFOs", "UK", "University / College", "USA", "Vegetarian", "Video Equipment", "Video Games", "Vintage Cars", "Virtual Reality", "Vocal Music", "Volleyball", "Water Sports", "Web Development", "Webhosting", "Weblogs", "Webmail", "Weddings", "Weight Loss", "Wicca", "Windows", "Windows Dev", "Windsurfing", "Wine", "Women's Issues", "Woodworking", "WordPress", "Wrestling", "Writing", "Yoga", "Zoology" );
    }

    /**
     * Check online of offline
     * 
     * @return boolean
     */
    public static function check_internet_status(){
        if(!$sock = @fsockopen('www.google.com', 80)){
            return false;
        }else {
            return true;
        }
    }
    
    /**
     * Get Internet connection status
     * 
     * @return boolean | array
     */
    public static function Cs_Get_Inet_Status(){
        if( self::check_internet_status() === true ){
            return array();
        }else{
            return BackendMessages::get_instance()->Cs_Get_Notices( 'is_inet_down' );
        }
    }

        
    /**
     * Standardize whitespace in a string
     *
     * Replace line breaks, carriage returns, tabs with a space, then remove double spaces.
     *
     * @param string $string String input to standardize.
     *
     * @return string
     */
    public static function standardize_whitespace( $string ) {
            return trim( str_replace( '  ', ' ', str_replace( array( "\t", "\n", "\r", "\f" ), ' ', $string ) ) );
    }
    
    /**
     * Get Home url
     * 
     * @param type $path
     * @param type $scheme
     * @return type
     */
    public static function home_url( $path = '', $scheme = null ) {
        $home_url = home_url( $path, $scheme );

        if ( ! empty( $path ) ) {
                return $home_url;
        }

        $home_path = parse_url( $home_url, PHP_URL_PATH );

        if ( '/' === $home_path ) { // Home at site root, already slashed.
                return $home_url;
        }

        if ( is_null( $home_path ) ) { // Home at site root, always slash.
                return trailingslashit( $home_url );
        }

        if ( is_string( $home_path ) ) { // Home in subdirectory, slash if permalink structure has slash.
                return user_trailingslashit( $home_url );
        }

        return $home_url;
    }
    
    /**
     * Check whether a url is relative
     *
     * @param string $url URL string to check.
     * @return bool
     */
    public static function is_url_relative( $url ) {
            return ( strpos( $url, 'http' ) !== 0 && strpos( $url, '//' ) !== 0 );
    }
    
    /**
     * Base Url
     * 
     * @param type $path
     * @return type
     */
    public static function base_url( $path = null ) {
            $url = get_option( 'home' );

            $parts = wp_parse_url( $url );

            $base_url = trailingslashit( $parts['scheme'] . '://' . $parts['host'] );

            if ( ! is_null( $path ) ) {
                    $base_url .= ltrim( $path, '/' );
            }

            return $base_url;
    }

    
    /**
     * Array sorting
     * 
     * @param type $arr
     * @param type $col
     * @param type $dir
     */
    static function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }
    
    /**
     * Number Increment
     * 
     * @param type $min
     * @param type $max
     * @return type
     */
    public static function  numbers_array( $min, $max ){
        $number = array();
        for( $i=$min; $i<=$max; $i++){
            $number[] = $i;
        }
        return $number;
    }
    
    /**
     * Check https
     * 
     * @param type $url
     * @return boolean
     */
    public static function is_https( $url = '' ) {
        if ( ! empty( $url ) ) {
                if ( strpos( $url, '://' ) &&	// just in case
                        parse_url( $url, PHP_URL_SCHEME ) === 'https' ) {
                        return true;
                } else {
                        return false;
                }
        } elseif ( is_ssl() ) {		// since wp 2.6.0
                return true;
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
                strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https' ) {
                return true;
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_SSL'] ) &&
                strtolower( $_SERVER['HTTP_X_FORWARDED_SSL'] ) === 'on' ) {
                return true;
        } else {
                return false;
        }
    }

    /**
     * Get prot
     * 
     * @param type $url
     * @return string
     */
    public static function Cs_GetPort( $url = '' ) {
        if ( self::is_https( $url ) ) {
                return 'https';
        } elseif ( is_admin() )  {
                if ( self::get_const( 'FORCE_SSL_ADMIN' ) ) {
                        return 'https';
                }
        } elseif ( self::get_const( 'FORCE_SSL' ) ) {
                return 'https';
        }
        return 'http';
    }

    /**
     * Add prot
     * 
     * @param type $url
     * @return type
     */
    public static function add_prot( $url = '' ) {
        return self::Cs_GetPort( $url ).'://'.preg_replace( '/^(.*://|//)/', '', $url );
    }

    /**
     * Get const
     * 
     * @param type $const
     * @param type $not_found
     * @return type
     */
    public static function get_const( $const, $not_found = null ) {
        if ( defined( $const ) ) {
                return constant( $const );
        } else {
                return $not_found;
        }
    }
    
    /**
     * Sanitize has tags
     * 
     * @param type $tags
     * @return type
     */
    public static function sanitize_hashtags( $tags = array() ) {
        // truncate tags that start with a number (not allowed)
        return preg_replace( array( '/^[0-9].*/', '/[ \[\]#!\$\?\\\\\/\*\+\.\-\^]/', '/^.+/' ),
                array( '', '', '#$0' ), $tags );
    }
    
    /**
     * Array to hastags
     * 
     * @param type $tags
     * @return type
     */
    public static function array_to_hashtags( $tags = array() ) {
        // array_filter() removes empty array values
        return trim( implode( ' ', array_filter( self::sanitize_hashtags( $tags ) ) ) );
    }
    
    /**
     * Sanitize class name
     * 
     * @param type $name
     * @return type
     */
    public static function Cs_Sanitize_Class_Name( $name ){
        $name = ucwords($name);
        $name = sanitize_title_with_dashes( $name );
        return str_replace( array('_'), '', $name);
    }

    /**
     * Explode CSV
     * 
     * @param type $str
     * @return type
     */
    public static function explode_csv( $str ) {
        if ( empty( $str ) ) {
                return array();
        } else {
                return array_map( array( __CLASS__, 'trim_csv_val' ), explode( ',', $str ) );
        }
    }

    /**
     * Trim CSV Val 
     * 
     * @param type $val
     * @return type
     */
    private static function trim_csv_val( $val ) {
        return trim( $val, '\'" ' );	// remove quotes and spaces
    }

    /**
     * Titleize
     * 
     * @param type $str
     * @return type
     */
    public static function titleize( $str ) {
        return ucwords( preg_replace( '/[:\/\-\._]+/', ' ', self::decamelize( $str ) ) );
    }

    /**
     * Decamelize
     * 
     * @param type $str
     * @return type
     */
    public static function decamelize( $str ) {
        return ltrim( strtolower( preg_replace('/[A-Z]/', '_$0', $str ) ), '_' );
    }
    
    /**
     * Check Crawler name
     * 
     * @param type $crawler_name
     * @return type
     */
    public static function is_crawler_name( $crawler_name ) {
        return $crawler_name === self::get_crawler_name() ? true : false;
    }

    /**
     * Get Crawler name
     * 
     * @return type
     */
    public static function get_crawler_name() {
        if ( ! isset( self::$crawler_name ) ) {
                $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ?
                        strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

                switch ( true ) {
                        // "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)"
                        case ( strpos( $ua, 'facebookexternalhit/' ) === 0 ):
                                self::$crawler_name = 'facebook';
                                break;

                        // "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)"
                        case ( strpos( $ua, 'compatible; bingbot/' ) !== false ):
                                self::$crawler_name = 'bing';
                                break;

                        // "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)"
                        case ( strpos( $ua, 'compatible; googlebot/' ) !== false ):
                                self::$crawler_name = 'google';
                                break;

                        // Mozilla/5.0 (compatible; Google-Structured-Data-Testing-Tool +https://search.google.com/structured-data/testing-tool)"
                        case ( strpos( $ua, 'compatible; google-structured-data-testing-tool' ) !== false ):
                                self::$crawler_name = 'google';
                                break;

                        // "Pinterest/0.2 (+http://www.pinterest.com/bot.html)"
                        case ( strpos( $ua, 'pinterest/' ) === 0 ):
                                self::$crawler_name = 'pinterest';
                                break;

                        // "Twitterbot/1.0"
                        case ( strpos( $ua, 'twitterbot/' ) === 0 ):
                                self::$crawler_name = 'twitter';
                                break;

                        // "W3C_Validator/1.3 http://validator.w3.org/services"
                        case ( strpos( $ua, 'w3c_validator/' ) === 0 ):
                                self::$crawler_name = 'w3c';
                                break;

                        // "Validator.nu/LV http://validator.w3.org/services"
                        case ( strpos( $ua, 'validator.nu/' ) === 0 ):
                                self::$crawler_name = 'w3c';
                                break;

                        // "Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MTC19V) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36 (compatible; validator.ampproject.org) AppEngine-Google; (+http://code.google.com/appengine; appid: s~amp-validator)"
                        case ( strpos( $ua, 'validator.ampproject.org' ) === 0 ):
                                self::$crawler_name = 'amp';
                                break;

                        default:
                                self::$crawler_name = 'none';
                                break;
                }
                self::$crawler_name = apply_filters( 'codesolz_crawler_name', self::$crawler_name, $ua );
        }

        return self::$crawler_name;
    }
    
    /**
     * A2aa
     * 
     * @param type $a
     * @return type
     */
    public static function a2aa( $a ) {
        $aa = array();
        foreach ( $a as $i )
                $aa[][] = $i;
        return $aa;
    }

    /**
     * is assoc
     * 
     * @param type $arr
     * @return boolean
     */
    public static function is_assoc( $arr ) {
        if ( ! is_array( $arr ) ) {
                return false;
        } else {
                return is_numeric( implode( array_keys( $arr ) ) ) ? false : true;
        }
    }

    /**
     * Get array value by string
     * 
     * @param type $str
     * @param array $arr
     * @return array
     */
    public static function keys_start_with( $str, array $arr ) {
        $found = array();
        foreach ( $arr as $key => $value ) {
                if ( strpos( $key, $str ) === 0 ) {
                        $found[$key] = $value;
                }
        }
        return $found;
    }
    
    /**
     * Preg Grep Keys
     * 
     * @param type $pattern
     * @param array $input
     * @param type $invert
     * @param type $replace
     * @param type $remove
     * @return array
     */
    public static function preg_grep_keys( $pattern, array &$input, $invert = false, $replace = false, $remove = false ) {
        $invert = $invert == false ? null : PREG_GREP_INVERT;
        $match = preg_grep( $pattern, array_keys( $input ), $invert );
        $found = array();
        foreach ( $match as $key ) {
                if ( $replace !== false ) {
                        $fixed = preg_replace( $pattern, $replace, $key );
                        $found[$fixed] = $input[$key];
                } else {
                        $found[$key] = $input[$key];
                }
                if ( $remove !== false ) {
                        unset( $input[$key] );
                }
        }
        return $found;
    }

    /**
     * Rename Keys
     * 
     * @param type $opts
     * @param type $key_names
     * @param type $modifiers
     */
    public static function rename_keys( &$opts = array(), $key_names = array(), $modifiers = true ) {
        foreach ( $key_names as $old_name => $new_name ) {
                if ( empty( $old_name ) ) {	// just in case
                        continue;
                }
                $old_name_preg = $modifiers ? '/^'.$old_name.'(:is|:use|#.*|_[0-9]+)?$/' : '/^'.$old_name.'$/';

                foreach ( preg_grep( $old_name_preg, array_keys ( $opts ) ) as $old_name_local ) {
                        if ( ! empty( $new_name ) ) {	// can be empty to remove option
                                $new_name_local = preg_replace( $old_name_preg, $new_name.'$1', $old_name_local );
                                $opts[$new_name_local] = $opts[$old_name_local];
                        }
                        unset( $opts[$old_name_local] );
                }
        }
    }

    /**
     * Get Next Key
     * 
     * @param type $needle
     * @param array $input
     * @param type $loop
     * @return boolean
     */
    public static function next_key( $needle, array &$input, $loop = true ) {
        $keys = array_keys( $input );
        $pos = array_search( $needle, $keys );
        if ( $pos !== false ) {
                if ( isset( $keys[ $pos + 1 ] ) )
                        return $keys[ $pos + 1 ];
                elseif ( $loop === true )
                        return $keys[0];
        }
        return false;
    }
    
    /**
     * Move to End of Array
     * 
     * @param array $array
     * @param type $key
     * @return array
     */
    public static function move_to_end( array &$array, $key ) {
        $val = $array[$key];
        unset( $array[$key] );
        $array[$key] = $val;
        return $array;
    }
    
    /**
     * Array merge   |  PHP's array_merge_recursive() merges arrays, but it converts values with duplicate keys to arrays rather than overwriting the value in the first array with the duplicate value in the second array, as array_merge does. The following method does not change the datatypes of the values in the arrays. Matching key values in the second array overwrite those in the first array, as is the case with array_merge().
     * 
     * @param array $array1
     * @param array $array2
     * @return type
     */
    public static function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
            $merged = $array1;
            foreach ( $array2 as $key => &$value ) {
                    if ( is_array( $value ) && isset( $merged[$key] ) && is_array( $merged[$key] ) ) {
                            $merged[$key] = self::array_merge_recursive_distinct( $merged[$key], $value );
                    } else {
                            $merged[$key] = $value;
                    }
            }
            return $merged;
    }

    /**
     * Array Flatten
     * 
     * @param array $array
     * @return array
     * @param array $array
     * @return array
     * @param array $array
     * @return array
     */
    public static function Cs_Array_Flatten( array $array ) {
            $return = array();
            foreach ( $array as $key => $value ) {
                    if ( is_array( $value ) ) {
                            $return = array_merge( $return, self::Cs_Array_Flatten( $value ) );
                    } else {
                            $return[$key] = $value;
                    }
            }
            return $return;
    }

    /**
     * Array implode
     * 
     * @param array $array
     * @param type $glue
     * @return type
     */
    public static function array_implode( array $array, $glue = ' ' ) {
            $return = '';
            foreach ( $array as $value ) {
                    if ( is_array( $value ) ) {
                            $return .= self::array_implode( $value, $glue ).$glue;
                    } else {
                            $return .= $value.$glue;
                    }
            }
            return strlen( $glue ) ?
                    rtrim( $return, $glue ) : $glue;
    }

    /**
     * Array parent index | array must use unique associative / string keys
     * 
     * @param array $array
     * @param type $parent_key
     * @param type $gparent_key
     * @param type $index
     * @return type
     */
    public static function Cs_Array_Parent_Index( array $array, $parent_key = '', $gparent_key = '', &$index = array() ) {
            foreach ( $array as $child_key => $value ) {
                    if ( isset( $index[$child_key] ) ) {
                            error_log( __METHOD__.' error: duplicate key '.$child_key.' = '.$index[$child_key] );
                    } elseif ( is_array( $value ) ) {
                            self::Cs_Array_Parent_Index( $value, $child_key, $parent_key, $index );
                    } elseif ( $parent_key && $child_key !== $parent_key ) {
                            $index[$child_key] = $parent_key;
                    } elseif ( $gparent_key && $child_key === $parent_key ) {
                            $index[$child_key] = $gparent_key;
                    }
            }
            return $index;
    }

    /**
     * cheak element in array
     * 
     * @param type $needle
     * @param array $array
     * @param type $strict
     * @return boolean
     */
    public static function has_array_element( $needle, array $array, $strict = false ) {
        foreach ( $array as $key => $element ) {
                if ( ( $strict ? $element === $needle : $element == $needle ) ||
                        ( is_array( $element ) && self::has_array_element( $needle, $element, $strict ) ) ) {
                        return true;
                }
        }
        return false;
    }

    /**
     * get numbers
     * 
     * @param array $input
     * @return type
     */
    public static function get_first_last_next_nums( array $input ) {
            $count = count( $input );
            $keys = array_keys( $input );
            if ( $count && ! is_numeric( implode( $keys ) ) )	// array cannot be associative
                    return array( 0, 0, 0 );
            sort( $keys );
            $first = (int) reset( $keys );
            $last = (int) end( $keys );
            $next = $count ? $last + 1 : $last;	// next is 0 for an empty array
            return array( $first, $last, $next );
    }
    
    /**
     * Array Unset by Key
     * 
     * @param array $array
     * @param array $unset_keys
     * @return boolean|array
     */
    public static function Cs_Array_Unset( array $array, array $unset_keys ){
        if( !is_array($unset_keys ) ) return false;
        foreach ($unset_keys as $key){
            unset($array[$key]);
        }
        return $array;
    }
 
    /**
     * Check valid url
     * 
     * @param type $url
     */
    public static function Cs_Is_Url( $url ){
        if (filter_var( $url, FILTER_VALIDATE_URL) === FALSE) {
            return false;
        }
        return true;
    }
    
    /**
     * CSV Download Header
     * 
     * @param type $filename
     */
    public static function Cs_Download_Send_Headers( $filename ) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    
    /**
     * 
     * @param array $array
     * @return type
     */
    public static function Cs_Array2csv(array $array ){
       if ( !is_array($array) || count($array) === 0 ) {
         return false;
       }
//       ob_start();
       ob_end_clean();
       
       $df = fopen("php://output", 'w');
       fputcsv($df, $array['col_head'] );
       unset( $array['col_head'] );
       if($array['col_data']){
           foreach ($array['col_data'] as $item) {
               if( self::Cs_Clean_Data($item) != ''){
                  fputcsv($df, $item);
               }
           }
       }
       fclose($df);
//       return ob_get_clean();
    }
    
    /**
     * Clean data
     */
    public static function Cs_Clean_Data( $data ){
        if( is_array($data)){
            $data = array_map( 'trim', $data);
            $data = array_map( 'htmlspecialchars', $data);
            $data = array_map( 'stripcslashes', $data);
            $data = wp_kses( $data, array());
        }else{
            $data = trim( $data );
            $data = htmlspecialchars( $data );
            $data = stripcslashes( $data );
            $data = wp_kses( $data, array());
        }
        return $data;
    }
    
    /**
     * Multi string position detection. Returns the first position of $check found in 
     * $str or an associative array of all found positions if $getResults is enabled. 
     * 
     * Always returns boolean false if no matches are found.
     *
     * @param   string         $str         The string to search
     * @param   string|array   $check       String literal / array of strings to check 
     * @param   boolean        $getResults  Return associative array of positions?
     * @return  boolean|int|array           False if no matches, int|array otherwise
     */
    public static function Cs_Multi_Strpos($string, $check, $getResults = false) {
        $result = array();
        $checks = (array) $check;
        foreach ($check as $s){
            $pos = strpos($string, $s);
            if ($pos !== false) {
              if ( $getResults ) {
                $result[$s] = $pos;
              }
              else {
                return $pos;          
              }
            }
        }
        return empty($result) ? false : $result;
    }
    
    /**
     * Replace string at first occurrence 
     * 
     * @param type $needle
     * @param type $replace
     * @param type $haystack
     * @return type
     */
    public static function Cs_Replace_First_Occur( $needle, $replace, $haystack ){
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            return substr_replace($haystack, $replace, $pos, strlen($needle));
        }
        return $haystack;
    }
   
    /**
     * Filter array by matching key
     * 
     * @param type $array | array( 'match_to_me' => 'values') 
     * @param String $callback
     * @return array
     */
    public static function Cs_Array_Filter_Key( $array, $callback ){
        $matchedKeys = array_filter( array_keys( $array ), array( __CLASS__, $callback) );
        return array_intersect_key( $array, array_flip( $matchedKeys ) );
    }
    
    /**
     * Callback function to filter meta key
     * 
     * @param type $key
     * @return boolean
     */
    public static function meta_key_filter( $array, $prefix ){
        if( !is_array( $array )) return false;
        $return = array();
        foreach($array as $key => $value ){
            if( strpos( $key, $prefix ) !== false ){
                $return += array( str_replace( $prefix, '', $key) => isset($value[0]) ? maybe_unserialize( $value[0] ) : '' ); 
            }
        }
        return $return;
    }
    
    /**
     * Get Request Value from URL
     * 
     * @param type $key
     * @param type $method
     * @param type $default
     * @return type
     */
    public static function Cs_GetRequestValue( $key, $method = 'ANY', $default = '' ) {
            if ( $method === 'ANY' ) {
                    $method = $_SERVER['REQUEST_METHOD'];
            }
            switch( $method ) {
                    case 'POST':
                            if ( isset( $_POST[$key] ) ) {
                                    return self::Cs_Clean_Data( $_POST[$key] );
                            }
                            break;
                    case 'GET':
                            if ( isset( $_GET[$key] ) ) {
                                    return self::Cs_Clean_Data( $_GET[$key] );
                            }
                            break;
            }
            return $default;
    }
    
    /**
     * Sanitize meta tags
     * 
     * @param type $tagName
     * @return type
     */
    public static function Cs_SanitizeMetaTag( $tagName ){
        return str_replace( array( ':') , '_', $tagName );
    }
    
    /**
     * Get image size info
     * 
     * @global type $_wp_additional_image_sizes
     * @param type $size_name
     * @return type
     */
    public static function Cs_GetSizeInfo( $size_name = '' ) {
        if ( is_integer( $size_name ) || is_array( $size_name ) || empty( $size_name ) ) {
            return;
        }

        global $_wp_additional_image_sizes;

//        pre_print( $_wp_additional_image_sizes );
        
        if ( isset( $_wp_additional_image_sizes[$size_name]['width'] ) ) {
                $width = intval( $_wp_additional_image_sizes[$size_name]['width'] );
        } else {
                $width = get_option( $size_name . '_size_w' );
        }

        if ( isset( $_wp_additional_image_sizes[$size_name]['height'] ) ) {
                $height = intval( $_wp_additional_image_sizes[$size_name]['height'] );
        } else {
                $height = get_option( $size_name . '_size_h' );
        }

        if ( isset( $_wp_additional_image_sizes[$size_name]['crop'] ) ) {
                $crop = $_wp_additional_image_sizes[$size_name]['crop'];
        } else {
                $crop = get_option( $size_name . '_crop' );
        }

        if ( ! is_array( $crop ) ) {
                $crop = empty( $crop ) ? false : true;
        }

        return array( 'width' => $width, 'height' => $height, 'crop' => $crop );
    }
    
    /**
     * Add image url size
     * 
     * @param type $opt_keys
     * @param array $opts
     * @return array
     */
    public static function Cs_AddImageUrlSize( $opt_keys, array &$opts ) {
        if ( ! is_array( $opt_keys ) ) {
            $opt_keys = array( $opt_keys );
        }

        foreach ( $opt_keys as $opt_prefix ) {
            $opt_suffix = '';
            if ( preg_match( '/^(.*)(#.*)$/', $opt_prefix, $matches ) ) {	// language
                $opt_prefix = $matches[1];
                $opt_suffix = $matches[2].$opt_suffix;
            }
            if ( preg_match( '/^(.*)(_[0-9]+)$/', $opt_prefix, $matches ) ) {	// multi-option
                    $opt_prefix = $matches[1];
                    $opt_suffix = $matches[2].$opt_suffix;
            }
            $media_url = self::Cs_GetMtMediaUrl( $opts, $opt_prefix.$opt_suffix );
            if ( ! empty( $media_url ) ) {
                $image_info = self::Cs_GetImageUrlInfo( $media_url );
                list(
                        $opts[$opt_prefix.':width'.$opt_suffix],	// example: place_addr_img_url:width_1
                        $opts[$opt_prefix.':height'.$opt_suffix],	// example: place_addr_img_url:height_1
                        $image_type,
                        $image_attr
                ) = $image_info;
            }
        }
        return $opts;
    }
    
    /**
     * Get media url
     * 
     * @param array $assoc
     * @param type $mt_prefix
     * @param array $mt_suffixes
     * @return string|array
     */
    public static function Cs_GetMtMediaUrl( array $assoc, $mt_prefix = 'og:image', 
			array $mt_suffixes = array( ':secure_url', ':url', '', ':embed_url' ) ) {

        if ( isset( $assoc[$mt_prefix] ) && is_array( $assoc[$mt_prefix] ) ) {
                $first_element = reset( $assoc[$mt_prefix] );
        } else {
                $first_element = reset( $assoc );
        }

        if ( is_array( $first_element ) ) {
            return self::Cs_GetMtMediaUrl( $first_element, $mt_prefix );
        }

        foreach ( $mt_suffixes as $mt_suffix ) {
                if ( ! empty( $assoc[$mt_prefix . $mt_suffix] ) ) {
                    return $assoc[$mt_prefix . $mt_suffix]; // Return first match.
                }
        }
        return ''; // Empty string.
    }

    /**
     * Get image url info
     * 
     * @param type $image_url
     * @return string
     */
    public static function Cs_GetImageUrlInfo( $image_url ) {
        $def_image_info = array( '', '', '', '' );
        $image_info = false;

        $image_info = self::Cs_GetImageSize( $image_url );	// wrapper for PHP's getimagesize()

        if ( ! is_array( $image_info ) ) {
            $image_info = $def_image_info;
        }
        return $image_info;
    }
    
    /**
     * Get image size
     * 
     * @param type $url
     * @param type $cache_exp_secs
     * @param array $curl_opts
     * @return boolean
     */
    public function Cs_GetImageSize( $url ) {
        if ( ! empty( $url ) ) {	// false on error
            if ( file_exists( $url ) ) {
                return @getimagesize( $url );
            } 
        } 
        return false;
    }

    

    
}
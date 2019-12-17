<?php namespace CsSeoMegaBundlePack\Library;
/**
 * Schema
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
class CsSchema {
    protected $types_cache = null;			// schema types array cache
    protected $types_exp = MONTH_IN_SECONDS;	// schema types array expi
    
    
    public function get_schema_types( $schema_types = null, $add_none = true, $exclude_intangible = true ) {
        if ( ! is_array( $schema_types ) ) {
            $schema_types = $this->get_schema_types_array( false );	// $flatten = false
        }

        if ( $exclude_intangible ) {
            unset( $schema_types['thing']['action'] );
            unset( $schema_types['thing']['intangible'] );
        }

        return GeneralHelpers::Cs_Array_Flatten( $schema_types );
    }
    
    public function get_schema_types_array( $flatten = true ) {
        if ( ! isset( $this->types_cache['filtered'] ) ) {	// check class property cache
            $cache_salt = 'AIOS_Head_Schema';
            $cache_id = GeneralHelpers::Cs_Md5_Hash( $cache_salt );

            if ( $this->types_exp > 0 ) {
                $this->types_cache = get_transient( $cache_id );	// returns false when not found
            }

            if ( ! isset( $this->types_cache['filtered'] ) ) {	// from transient cache or not, check if filtered
                    $this->types_cache['filtered'] = MetaTagAssets::$MTA['head']['schema_type'];
                    $this->types_cache['flattened'] = GeneralHelpers::Cs_Array_Flatten( $this->types_cache['filtered'] );
                    ksort( $this->types_cache['flattened'] );
                    $this->types_cache['parents'] = GeneralHelpers::Cs_Array_Parent_Index( $this->types_cache['filtered'] );
                    ksort( $this->types_cache['parents'] );
                    $this->add_schema_type_xrefs( $this->types_cache['filtered'] );
                    if ( $this->types_exp > 0 ) {
                        set_transient( $cache_id, $this->types_cache, $this->types_exp );
                    }
            } 
        }

        if ( $flatten ) {
                return $this->types_cache['flattened'];
        } else {
                return $this->types_cache['filtered'];
        }
    }
    
    /**
     * type xrefs
     * 
     * @param type $schema_types
     */
    protected function add_schema_type_xrefs( &$schema_types ) {
        $t =& $schema_types['thing'];

        /*
         * Intangible > Enumeration
         */
        $t['intangible']['enumeration']['medical.enumeration']['medical.specialty'] =&
                $t['intangible']['enumeration']['specialty']['medical.specialty'];

        /*
         * Organization > Local Business
         */
        $t['organization']['local.business'] =& 
                $t['place']['local.business'];

        /*
         * Organization > Medical Organization
         */
        $t['organization']['medical.organization']['dentist'] =& 
                $t['place']['local.business']['dentist'];

        $t['organization']['medical.organization']['hospital'] =& 
                $t['place']['local.business']['emergency.service']['hospital'];

        /*
         * Place > Civic Structure
         */
        $t['place']['civic.structure']['campground'] =&
                $t['place']['local.business']['lodging.business']['campground'];

        $t['place']['civic.structure']['fire.station'] =&
                $t['place']['local.business']['emergency.service']['fire.station'];

        $t['place']['civic.structure']['hospital'] =&
                $t['place']['local.business']['emergency.service']['hospital'];

        $t['place']['civic.structure']['movie.theatre'] =&
                $t['place']['local.business']['entertainment.business']['movie.theatre'];

        $t['place']['civic.structure']['police.station'] =&
                $t['place']['local.business']['emergency.service']['police.station'];

        $t['place']['civic.structure']['stadium.or.arena'] =&
                $t['place']['local.business']['sports.activity.location']['stadium.or.arena'];

        /*
         * Place > Local Business
         */
        $t['place']['local.business']['store']['auto.parts.store'] =& 
                $t['place']['local.business']['automotive.business']['auto.parts.store'];

}


    
}

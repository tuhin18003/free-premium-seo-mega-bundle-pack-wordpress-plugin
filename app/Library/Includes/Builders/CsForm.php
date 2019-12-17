<?php namespace CsSeoMegaBundlePack\Library\Includes\Builders;
/**
 * Library : Form Generator
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsFormLabels;

class CsForm {
    
    /**
     * Hold data prefix
     *
     * @var type 
     */
    private $dataPrefix;
    
    public function __construct() {
        $this->dataPrefix = Helper::get('PLUGIN_DATA_PREFIX');
    }
    
    /**
     * Get Og Inputs
     * 
     * @param type $data
     */
    public function getOgInputs( $data, $html_return = 'true', $group_of_values = '' ){
        $og_type = trim($data['type']);
        $properties = MetaTagAssets::$MTA['head']['og_type_mt'][ $og_type ];
        if( empty( $properties ) ){ 
            return false;
        }
        $main_des = CsFormLabels::get_og_labels( $og_type.':main_description' );
        $fields = array(
            'group_start' => array(
                'class' => 'csmbp_custom_og_tags'
            ),
            'st_product' => array(
                'helptext' => '%s '.sprintf( __( 'Meta Tags for Open Graph type "%s".', SR_TEXTDOMAIN ), CsFormLabels::get_og_type_label($og_type) ),
                'type' => 'section_title',
                'description' => isset( $main_des['label'] ) ? $main_des['label'] : ''
            ),

        ); $i = 0;
        foreach( $properties as $item_id => $val ){

            $field_type = array();
            $main_label = CsFormLabels::get_og_labels( $item_id );
            $default_value = MetaTagAssets::$MTA['head']['og_content_map'][ $item_id ];
            if( $default_value ){
                $field_val = array();
                if( is_array( $default_value ) ){
                    foreach( $default_value as $key => $sub_val ){
                        if( $label = CsFormLabels::get_og_labels( $key ) ){
                            $field_val += array( $sub_val => $label['label'] );
                        }else{
                            $field_val += array(  $key => $sub_val );
                        }
                    }

                    //select field
                    $field_type = array(
                        'type' => 'select',
                        'options' => $field_val,
                        'options_flip' => true,
                        'select_value' => $this->__get_database_value( $group_of_values, $item_id )
                    );
                }

            }else{
                if( $val == 'textarea' ){

                    //field type textarea
                    $field_type = array(
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'value' => $this->__get_database_value( $group_of_values, $item_id )
                    );

                }else{

                    //field type text
                    $field_type = array(
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'value' => $this->__get_database_value( $group_of_values, $item_id )
                    );

                }
            }

            $fields += array(
                "{$item_id}" => array(
                    'label' => $main_label['label'],
                    'helptext' => $main_label['helptext'],
                    'placeholder' => $main_label['helptext'],
                    'wrapper' => true
                ) + $field_type
            );

            $i++;
        }
        
        $fields += array(
            'group_end' => array(
                'group_tag_end'
            )
        );
        
        if( $html_return ){
            return $output_input =  $this->get_input_fields( $fields );
        }else{
            return $fields;
        }
        
        echo "<pre>";
        print_r( $fields );
//        return $fields;
        
    }
    
    /**
     * Get Values from database
     * 
     * @param type $options
     * @param type $id
     * @param type $wp_id
     * @return type
     */
    private function __get_database_value( $options, $id, $wp_id = '' ){
        if( empty( $options ) ){
            return false;
        }
        
        if( isset( $options[ 'args' ]->post->wp_options->$id ) && !empty( $value = $options[ 'args' ]->post->wp_options->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) && !empty( $value = $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_graph_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_graph_options[ $id ] )){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_pub_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_pub_options[ $id ] )){
            return $value;
        }
    }
    
    /**
     * Get labels
     * 
     * @param type $fields
     * @return type
     */
    public static function get_labels( $fields ){
        $field_val = array();
        if( is_array( $fields ) ){
            //return select options labels
            foreach( $fields as $key => $sub_val ){
                if( ! empty ( $label = CsFormLabels::get_og_type_label( $sub_val ) )){
                    $field_val += array( $sub_val => $label );
                }
            }
        }else{
            return CsFormLabels::get_og_labels( $fields );
        }
        
        return $field_val;
    }

    /**
     * Generate inputs
     * 
     * @param type $inputs
     * @return boolean
     */
    public function get_input_fields( $inputs ){
        if( !is_array( $inputs ) || empty($inputs) ) return false;
        
        $form_fields = '';
        $r = 0;
        
        foreach( $inputs as $id => $input ){
            if( $r == count( $inputs) - 1 ){
                $input['no-bottom-border'] = true;
            }
            
            //check group
            if( $id == 'group_start' ){
                $form_fields .= '<div id="'.$input['id'].'" class="'.$input['class'].'" >';
                continue;
            }
            
            //group end
            if( $id == 'group_end' ){
                $form_fields .= '</div>';
                continue;
            }
            
            if( method_exists( $this, 'form_field_'.$input['type'] ) ){
                $input['id'] = $id;
                $form_fields .= $this->{'form_field_'.$input['type']}( $input );
            }
            $r++;
        }
        
        return $form_fields;
    }
    
    /**
     * Get Form Input Text
     * 
     * @param array $args
     * @return boolean|string
     */
    public function form_field_input( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $input = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $input .= '<input ';
        foreach( $args as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $input .= $this->{'_'.$key}( $args );
            }
        }
        $input .= isset( $args[ 'class' ] ) ? '' : $this->_class( $args );
        $input .= ' />';
        $input .= $this->__helpBlock( $args );
        $input .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $input;
    }
    
    /**
     * Generate checkbox
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_checkbox( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $input = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $input .= '<input ';
        foreach( $args as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $input .= $this->{'_'.$key}( $args );
            }
        }
        $input .= 'type ="checkbox"';
        $input .= isset( $args[ 'class' ] ) ? '' : $this->_class( $args );
        $input .= ' />';
        $input .= $this->__helpBlock( $args );
        $input .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $input;
    }
    
    /**
     * Generate radiokbox
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_radio( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $input = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $input .= '<input ';
        foreach( $args as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $input .= $this->{'_'.$key}( $args );
            }
        }
        $input .= 'type ="radio"';
        $input .= isset( $args[ 'class' ] ) ? '' : $this->_class( $args );
        $input .= ' />';
        $input .= $this->__helpBlock( $args );
        $input .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $input;
    }
    
    /**
     * Generate textarea
     * 
     * @param array $args
     * @return boolean|String
     */
    public function form_field_textarea( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $textarea = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $textarea .= '<textarea ';
        foreach( $args as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $textarea .= $this->{'_'.$key}( $args );
            }
        }
        $textarea .= isset( $args[ 'class' ] ) ? '' : $this->_class( $args );
        $textarea .= ! isset( $args[ 'cols' ] ) ? '' : $this->_width( $args );
        $textarea .= '>';
        $textarea .= $this->__textarea_value( $args );
        $textarea .= '</textarea>';
        $textarea .= $this->__helpBlock( $args );
        $textarea .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $textarea;
    }
    
    /**
     * Generate input select
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_select( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $select = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $select .= '<select ';
        foreach( $args as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $select .= $this->{'_'.$key}( $args );
            }
        }
        $select .= '>';
        $select .= $this->__options( $args );
        $select .= '</select>';
        $select .= $this->__helpBlock( $args );
        $select .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $select;
    }
    
    /**
     * Get validator fields
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_validator( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $input = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        $input .= '<a target = "_blank"';
        foreach( $args['anchor'] as $key => $val ){
            if(method_exists( $this, '_'.$key) ){
                $input .= $this->{'_'.$key}( $args['anchor'] );
            }
        }
        $input .= '>';
        $input .= $this->__getText( $args );
        $input .= '</a>';
        $input .= $this->__helpBlock( $args );
        $input .= $this->__copyUrl( $args );
        $input .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $input;
    }
    
    /**
     * Generate mixed type of inputs
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_miscellaneous( $args ){
        if( !is_array( $args ) || empty($args) ) return false;
        $input = empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 0 ] ;
        foreach( $args['options'] as $id => $subOptns){
            $subOptns[ 'id' ] = $id;
            if( !empty( $subOptns['concat_text'] ) ){
                $input .= '<span class="input-concat-text">' . $subOptns['concat_text'] . '</span>';
            }
            if( !empty( $subOptns['line_break'] ) ){
                $line_break_class = isset($subOptns['line_break_class']) ? $subOptns['line_break_class'] : '';
                $input .= '<hr class="input-line-break '.$line_break_class.' ">';
            }
            if( !empty( $subOptns[ 'before_text' ])){
                $input .= '<span class="input-before-text">' . $subOptns[ 'before_text' ] . '</span>';
            }
            
            if( $subOptns['type'] == 'input'){
                $input .= $this->form_field_input( $subOptns );
            }
            else if( $subOptns['type'] == 'checkbox'){
                $input .= $this->form_field_checkbox( $subOptns );
            }
            else if( $subOptns['type'] == 'radio'){
                $input .= $this->form_field_radio( $subOptns );
            }
            else if( $subOptns['type'] == 'select'){
                $input .= $this->form_field_select( $subOptns );
            }
            
            if( !empty( $subOptns[ 'after_text' ])){
                $input .= '<span class="input-before-text">' . $subOptns[ 'after_text' ] . '</span>';
            }
            
        }
        $input .= $this->__helpBlock( $args );
        $input .= empty( $wrap = $this->__wrapper( $args ) ) ? '' : $wrap[ 1 ] ;
        
        return $input;
    }
    
    /**
     * Generate section title
     * 
     * @param type $args
     * @return boolean
     */
    public function form_field_section_title( $args ){
        if( !is_array( $args ) || empty( $args ) ) return false;
        $section_title = '<div class="section-title">' . sprintf( $args['helptext'], '<i class="fa fa-edit"></i> ')  . '</div>';
        if( isset( $args['description'] ) && !empty( $args['description'] ) ){
            $section_title .= '<p class="section-description">' . $args['description']  . '</p>';
        }
        return $section_title;
    }

    /**
     * Generate Wrapper
     * 
     * @param type $args
     * @return boolean
     */
    private function __wrapper( $args ){
        $wrap = '';
        if( isset( $args[ 'wrapper' ] ) && $args[ 'wrapper' ] !== false ){
            $border = !isset( $args[ 'no-bottom-border' ] ) ? 'form-group-border' : '';
            $wrap = '<div class="'.Helper::get('PLUGIN_PREFIX').'-form-group '.$border.'">';
            $wrap .= isset( $args[ 'label' ] ) ? '<label for="'.$args[ 'label' ].'"> ' . $args[ 'label' ] .'</label> <div class="input-group">' : '';
            return array( $wrap,'</div></div>');
        }
        return false;
    }
    
    /**
     * Generate Help Block
     * 
     * @param type $args
     * @return boolean
     */
    private function __helpBlock( $args ){
        if( isset( $args[ 'helptext' ])){
            return '<p class="description">' . $args[ 'helptext' ] . '</p>';
        }
        return false;
    }
    
    /**
     * Get anchor
     * 
     * @return boolean
     */
    private function __getText( $args ){
        if( isset( $args[ 'anchor' ][ 'text'])){
            return trim($args[ 'anchor' ][ 'text' ]);
        }
        return false;
    }
    
    /**
     * Generator Input Field for Copy String
     * 
     * @param type $args
     * @return type
     */
    private function __copyUrl( $args ){
        if( isset( $args['copyurl'])){
            return '<input type="text" value = "'.$args['copyurl'].'" class = "form-control input-copy" readonly = "" /><p class="description"></p>';
        }
    }

    /**
     * Get href attribute
     * 
     * @param type $args
     * @return type
     */
    private function _href( $args ){
        return isset( $args[ 'href' ]) ? ' href = "'.$args[ 'href' ].'"' : 'href = "#"';
    }

    /**
     * Generate Type Attribute
     * 
     * @param type $args
     * @return type
     */
    private function _input_type( $args ){
        return isset( $args[ 'input_type' ]) ? ' type = "'.$args[ 'input_type' ].'"' : '';
    }
    
    /**
     * Generate id Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _id( $args ){
        if( isset( $args['checkbox_same_id'])){ //onlyfor checkbox
            return isset( $args[ 'id' ]) ? ' id = "'. Helper::get('PLUGIN_DATA_PREFIX') . $args[ 'checkbox_same_id' ].'" name = "'.  Helper::get('PLUGIN_DATA_PREFIX') . $args[ 'checkbox_same_id' ].'"' : '';
        }else{
            return isset( $args[ 'id' ]) ? ' id = "'. Helper::get('PLUGIN_DATA_PREFIX') . $args[ 'id' ].'" name = "'.  Helper::get('PLUGIN_DATA_PREFIX') . $args[ 'id' ].'"' : '';
        }
    }
    
    /**
     * Generate Class Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _class($args ){
        return isset( $args[ 'class' ]) ? ' class = "'.$args[ 'class' ].'"' : '';
    }
    
    /**
     * Generate Maxlength Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _maxlength($args ){
        return isset( $args[ 'maxlength' ]) ? ' maxlength = "'.$args[ 'maxlength' ].'"' : '';
    }
    
    /**
     * Generate Width Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _width( $args ){
        return isset( $args[ 'width' ]) ? ' style = "width:'.$args[ 'width' ].';"' : 'class = "form-control"';
    }
    
    /**
     * Generate Width Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _input_width( $args ){
        return isset( $args[ 'input_width' ]) ? ' style = "width:'.$args[ 'input_width' ].';"' : '';
    }
    
    /**
     * Generate Readonly Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _readonly( $args ){
        return isset( $args[ 'readonly' ] ) && $args[ 'readonly' ] === true ? ' readonly ' : '';
    }
    
    /**
     * Generate Disabled Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _disabled( $args ){
        return isset( $args[ 'disabled' ] ) && $args[ 'disabled' ] === true ? ' disabled ' : '';
    }
    
    /**
     * Generate Required Attribute
     * 
     * @param type $args
     * @return string
     */
    private function _required( $args ){
        return isset( $args[ 'required' ] ) && $args[ 'required' ] === true ? ' required = "" ' : '';
    }

    /**
     * Generate Placeholder Text
     * 
     * @param type $args
     * @return type
     */
    private function _placeholder( $args ){
        return isset( $args[ 'placeholder' ]) ? ' placeholder = "'.$args[ 'placeholder' ].'" ' : '';
    }
    
    /**
     * Value generator
     * 
     * @param type $args
     * @return type
     */
    private function _value( $args ){
        if( isset( $args['value'] ) && ! empty( $args['value'] ) ){
            if( $args['type'] == 'input' ){
                return $this->_input_value( $args );
            }elseif( $args['type'] == 'checkbox' ){
                return $this->_checkbox_value( $args );
            }elseif( $args['type'] == 'radio' ){
                return $this->_radio_value( $args );
            }
        }
    }

    /**
     * Set default value
     * 
     * @param type $args
     * @return type
     */
    private function _default_value( $args ){
        if($args['default_value']){
            return 'value = "' . $args['default_value'] . '"';
        }
    }

    /**
     * Checkbox value Generator
     * 
     * @param type $args
     * @return type
     */
    private function _checkbox_value( $args ){
        return empty($args['value']) ? '' : 'checked="checked"';
    }
    
    /**
     * Checkbox value Generator
     * 
     * @param type $args
     * @return type
     */
    private function _radio_value( $args ){
        if( ! empty($args['value']) && $args['value'] == $args['default_value'] ){
            return 'checked="checked"';
        }
    }

    /**
     * Generate Value
     * 
     * @param type $args
     * @return type
     */
    private function _input_value( $args ){ 
        return empty( $args['value'] ) ? '' : ' value = "'.$this->_sanitize_output( $args['value'] ).'" ';
    }
    
    /**
     * Generate Textarea Value
     * 
     * @param type $args
     * @return type
     */
    private function __textarea_value( $args ){
        return isset( $args[ 'value' ]) ? $this->_sanitize_output( $args[ 'value' ] )  : '';
    }
    
    /**
     * Generate Select options
     * 
     * @param type $args
     * @return boolean|string
     */
    private function __options( $args ){
        if( (!isset( $args[ 'options' ] ) || empty( $args[ 'options' ] ) ) && ( !isset( $args[ 'sub_options' ] ) || empty( $args[ 'sub_options' ] ) )  ) return false;
        $options = '';
        $optnArr = isset( $args[ 'options' ] )? $args[ 'options' ] : ( isset($args[ 'sub_options' ]) ? $args[ 'sub_options' ] : '' );
        
        foreach( $optnArr as $val => $label ){
            $selected = '';
            
            //if no value set in array, key and value is same
            if( isset( $args['option_key'] ) && $args['option_key'] == 'same_as_value' ){
                $val = $label;
            }
            
            //if flip options has set then flip the label to val
            if( isset( $args['options_flip'])){
                $flag = $val;
                $val = $label;
                $label = $flag;
            }
            
            //check selected value
            if( isset($args['select_value']) && $args['select_value'] == $val ) {
                $selected = 'selected ="selected"';
            }
            
            //get label
            if( isset( $args['label_filter']) && $args['label_filter'] == true ){
                if( !empty($filtered_label = CsFormLabels::get_og_type_label( $label ))){
                    $label = $filtered_label;
                }
            }
            
            $options .= '<option value="'.$val.'" '.$selected.'>' . $label . '</option>';
        }
        return $options;
    }
    
    /**
     * Generate Textarea rows
     * 
     * @param type $args
     * @return type
     */
    private function _rows( $args ){
        return isset( $args[ 'rows' ]) ? ' rows = "'.  $this->_sanitize_output( $args[ 'rows' ] ).'"' : '';
    }
    
    /**
     * Generate Textarea Cols
     * 
     * @param type $args
     * @return type
     */
    private function _cols( $args ){
        return isset( $args[ 'cols' ]) ? ' cols = "'.  $this->_sanitize_output( $args[ 'cols' ] ).'"' : '';
    }
    
    

    /**
     * Sanitize output
     * 
     * @param String $content
     * @return String
     */
    private function _sanitize_output( $content ){
        $content = trim( $content );
        $content = htmlspecialchars( $content );
        $content = stripcslashes( $content );
        $content = wp_kses( $content, array());
        $content = esc_html( $content );
        return $content;
    }
}

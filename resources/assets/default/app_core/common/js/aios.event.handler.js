/**
 * CSMBP Event Handler
 * 
 * @package CSMBP
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

/**
 * Decleration of Form Handler
 * 
 * @param {object} _obj
 * @returns {Aios_Form_Handler}
 */

var Aios_Form_Handler = function( _obj ){
    this.$ = _obj.$;
    this.reset = _obj.form_reset;
    this.TEXT_FIELD = 'input[type=text]';
    this.redirect_after_success = _obj.redirect;
    this.additional_data = _obj.additional_data;
    this.destroy_after_success = _obj.destroy_after_success;
};

Aios_Form_Handler.prototype.setup = function( _section_id ){
    this.section_id = _section_id;
    this.$( _section_id ).find( 'form' ).ajaxForm( {
        beforeSubmit: this.$.proxy( this.before_submit, this),
        success: this.$.proxy( this.on_success, this ),
        error: this.$.proxy( this.on_server_error, this ),
        dataType: 'json'
    } );
};

Aios_Form_Handler.prototype.destroy = function(){
    if( typeof this.section_id !== 'undefined' ){
        this.$( this.section_id ).find( 'form' ).ajaxFormUnbind();
    }
};

Aios_Form_Handler.prototype.before_submit = function( arr, $form, options ){
//    if( typeof this.additional_data !== 'undefined' ){
//        arr.push( [{ additional_data: this.additional_data }] );
//    }
    return swal({title: AIOS.swal_processing_title,text: AIOS.swal_processing_text,timer: 200000,imageUrl: AIOS.loading_gif_url,showConfirmButton: false, html :true});
};

Aios_Form_Handler.prototype.on_success = function( _response ){
//    console.log(_response);
    var error = this.get_redirect_error( _response );
    if(this.reset){
        this.reset_form();
    }
    //popup notice 
    if ( error )
        swal( error );
    else
        swal( _response );
    
    if( typeof this.redirect_after_success !== 'undefined'){
        var redirect_params = typeof _response.redirect_param !== 'undefined' ? _response.redirect_param : '';
        this.page_redirect( this.redirect_after_success + redirect_params );
    }
    
    if( typeof this.section_id !== 'undefined'){
        this.$( this.section_id ).find('button[type=submit]').removeAttr('disable');
    }
    
    if( typeof this.destroy_after_success !== 'undefined' ){
        this.destroy();
    }
};

Aios_Form_Handler.prototype.page_redirect = function( location ){
  window.location = location;  
};

Aios_Form_Handler.prototype.reset_form = function() {
    this.$( this.section_id ).find('form')[0].reset();
};

Aios_Form_Handler.prototype.show_notice = function( _send_obj ){
//    console.log( 'notice' + _send_obj);
    var obj = Object.assign({
        html: true
    }, _send_obj);
    swal( obj );
};

Aios_Form_Handler.prototype.get_redirect_error = function( _response ) {
    if ( parseInt( _response, 10 ) === 0 || parseInt( _response, 10 ) === -1 ){
        return [ AIOS.swal_error_title, AIOS.error_msg, 'error'];
    }
    else {
        return false;
    }
};
Aios_Form_Handler.prototype.on_server_error = function( xhr ) {
    return swal( {title: AIOS.swal_error_title,text: xhr.responseText,timer: 25000, type : 'error', html: true} );
};

Aios_Form_Handler.prototype.stripslashes = function( str ){
    return str.replace(/\\(.)/mg, "$1");
};

/**
 * Ajax Action Handler
 * 
 * @param {object} _obj
 * @returns {Aios_Action_Handler}
 */
Aios_Action_Handler = function( _obj ){
    this.$ = _obj.$;
    this.data_id = _obj.data_section_id;
    this.redirect = _obj.redirect;
    this.row_id = _obj.row_id; // selected row ids
    this.action_type = _obj.action_type; // action type - delete / others
    this.single_item = _obj.single_item; // for each table row action
    this.additional_data = _obj.additional_data; // for only a button fired action
    this.single_btn_action = _obj.single_btn_action; // for only a button fired action
    this.swal_confirm_text = _obj.swal_confirm_text;
    this.swal_confirm_btn_text = _obj.swal_confirm_btn_text;
};

Aios_Action_Handler.prototype.setup = function( _action_id ){
    this.action_id = _action_id;
    this.$( _action_id ).on( "click", this.$.proxy( this.check_action_type, this ) );
};

Aios_Action_Handler.prototype.check_action_type = function( ev ){
    if(typeof this.action_type !== 'undefined'){
        this.delete_confirmation();
    }else{
        this.single_bulk_actions();
    }
};

Aios_Action_Handler.prototype.delete_confirmation = function(){
    if(typeof AIOS === 'undefined') return false;
    var swalConfirmText = AIOS.swal_confirm_text;
    if( typeof this.swal_confirm_text !== 'undefined'){
         swalConfirmText = this.swal_confirm_text;
    }
    var swalConfirmBtnText = AIOS.swal_confirm_btn_text;
    if( typeof this.swal_confirm_btn_text !== 'undefined'){
        swalConfirmBtnText = this.swal_confirm_btn_text;
    }
    swal({title: AIOS.swal_confirm_title,text: swalConfirmText,type: "warning",showCancelButton: true,confirmButtonColor: "#DD6B55",confirmButtonText: swalConfirmBtnText,closeOnConfirm: false,showLoaderOnConfirm: false}, this.$.proxy( this.single_bulk_actions, this ));
};

Aios_Action_Handler.prototype.single_bulk_actions = function( ev ){
    if( typeof this.single_btn_action !== 'undefined'){
        //when a single button fired for any action
        var _user_data = this.get_single_button_acton_data();
    }else{
        if( typeof this.single_item === 'undefined'){
            var _user_data = this.get_acton_data();
        }else{
            var _user_data = this.get_single_acton_data();
        }
    }
    
    //check if user has selected an item for action and it's not single button action
    if( typeof _user_data._item_id !== 'undefined' && 0 === ((_user_data._item_id).toString()).length   ){
        this.check_id_selection();
    }else{
        var _ajax_data = {
                ajaxurl : this.build_action_url( _user_data._action ),
                data : _user_data
            };
        this.ajax_action( _ajax_data );
    }
};

Aios_Action_Handler.prototype.check_id_selection = function(){
    swal( AIOS.swal_error_title, AIOS.SwalErrorTextNoIdSelection, "error");
};

Aios_Action_Handler.prototype.ajax_action = function( obj ){
    this.AiosFormHandler = new Aios_Form_Handler( { $: this.$ } );
    this.$.ajax({
        type: 'POST',
        url: obj.ajaxurl,
        data: obj.data,
        beforeSend: this.$.proxy( this.AiosFormHandler.before_submit, this),
        success: this.$.proxy( this.on_ajax_response, this ),
        error: this.$.proxy( this.AiosFormHandler.on_server_error, this),
        dataType: 'json'
    });
};

Aios_Action_Handler.prototype.on_ajax_response = function( _response ){
    var no_response = this.AiosFormHandler.get_redirect_error(_response);
    
    /*when delete rows*/
    if( typeof _response.remove_id !== 'undefined'){
        for(i=0; i< _response.remove_id.length; i++){
            this.$( this.row_id+_response.remove_id[i]).css({'background':'orangered'}).fadeTo("slow", 0.33).fadeOut('blind');;
        }
        delete _response.remove_id;
    }
    
    if( no_response ){
        swal( no_response );
    }
    else {
        swal( _response );
    } 
    
    
    if( typeof this.redirect !== 'undefined'){
        this.AiosFormHandler.page_redirect( this.redirect );
    }
};

Aios_Action_Handler.prototype.get_acton_data = function(){
    if( typeof this.action_id === 'undefined') return false;
    var a = this.$( this.action_id ).data( 'action' ), n = this.$( "#_wpnonce" ).val(), c = this.$( this.data_id ).find('[type="checkbox"]:checked').map(function(){
        return $(this).closest('tr').find('input.item_id').val();
    }).get();
    return { _action : a, _ajax_nonce:n, _item_id : c};
};
Aios_Action_Handler.prototype.get_single_acton_data = function(){
    if( typeof this.action_id === 'undefined') return false;
    var a = this.$( this.action_id ).data( 'action' ), n = this.$( "#_wpnonce" ).val(), c = this.$( this.action_id ).data( 'item_id' );
    return { _action : a, _ajax_nonce:n, _item_id : c};
};

Aios_Action_Handler.prototype.get_single_button_acton_data = function(){
    if( typeof this.action_id === 'undefined') return false;
    var a = this.$( this.action_id ).data( 'action' ), n = this.$( "#_wpnonce" ).val();
    return { _action : a, _ajax_nonce: n, _additional_data : this.additional_data};
};

Aios_Action_Handler.prototype.build_action_url = function( _action ){
    if( typeof AIOS.base_url !== 'undefined' )
    return AIOS.base_url +'&action='+ _action;
};

/**
 * Redirect to Compare page
 * 
 * @param {object} _obj
 * @returns {Aios_Redirect_Detail_Page}
 */
Aios_Redirect_Detail_Page = function( _obj){
    this.$ = _obj.$;
    this.table_id = _obj.table_id;
    this.current_location = _obj.current_location;
};

Aios_Redirect_Detail_Page.prototype.setup = function( _action_id ){
    this.action_id = _action_id;
    this.$( _action_id ).on( "click", this.$.proxy( this.redirect_to_detail_page, this));
};

Aios_Redirect_Detail_Page.prototype.redirect_to_detail_page = function(){
//    console.log( this.get_acton_data() );
    var AAH  = new Aios_Action_Handler({$:this.$});   
    var _item_data = this.get_acton_data();
    if( 0 === ((_item_data._item_id).toString()).length ){
        AAH.check_id_selection();
    }else{
        window.location = this.current_location+'&item_ids='+(_item_data._item_id).toString()+'&compare_by_date=true';
    }
};

Aios_Redirect_Detail_Page.prototype.get_acton_data = function(){
    var c = this.$( this.table_id ).find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.item_id').val();
    }).get();
    return {  _item_id : c};
};


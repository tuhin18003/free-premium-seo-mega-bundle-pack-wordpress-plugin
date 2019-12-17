/**
 * Add Manage Property Script
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

jQuery(document).ready(function( $ ){
    var $loading_img = jQuery("#loading_gif").val();
    
    
    
    
    
    
    
    jQuery("#create_new_cat").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSAddNewPropertyGroup',
            data : jQuery(this).serialize()
        };
        
        $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: $data,
              async: false,
              beforeSend: function(xhr){
                if( xhr && xhr.overrideMimeType ) {
                    xhr.overrideMimeType('Content-type: application/json; charset=UTF-8');
                }
              },
              dataType: 'json',
              success: function( data, textStatus, XMLHttpRequest ) {
                                    console.log(data);
                    if(200 === XMLHttpRequest.status){
                        if( false === data.error ){
                                swal( { title: ''+data.title+'', text: ''+data.response_text+'', type : "success", html: true, timer: 2500 });
                                window.location = window.location.href;
                            }else{
                                swal( ''+data.title+'', ''+data.response_text+'', "error");
                            }
                    }else{
                        swal( ''+XMLHttpRequest.status+'', ''+textStatus+'', "error");
                    }
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                  swal( ''+textStatus+'', ''+errorThrown+'('+XMLHttpRequest.status+")"+'', "error");
              }
        });
        
        return false;
    });
    
    
//    $('#datatable').dataTable();
    
});

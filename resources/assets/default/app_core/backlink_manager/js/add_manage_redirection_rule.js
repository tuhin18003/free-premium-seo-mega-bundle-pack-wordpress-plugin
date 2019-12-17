/**
 * Add Manage Redirection Rule Script
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

jQuery(document).ready(function( $ ){
    var $loading_img = jQuery("#loading_gif").val();
    jQuery("#FormAddNewRedirectionRule").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSaddNewRedirectionRule',
            data: jQuery(this).serialize()
        };
        
        $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: $data,
              async: false,
              beforeSend: function(xhr) {
                if( xhr && xhr.overrideMimeType ) {
                    xhr.overrideMimeType('Content-type: application/json; charset=UTF-8');
                }
              },
              dataType: 'json',
              success: function( data, textStatus, XMLHttpRequest ) {
//                                    console.log(data);
                    if(200 === XMLHttpRequest.status){
                        if( false === data.error ){
                            swal( { title: ''+data.title+'', text: ''+data.response_text+'', type : "success", html: true, timer: 2500 });
                            $('#FormAddNewRedirectionRule')[0].reset();
                            window.location = jQuery("#redirect_after_success").val();
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
    
    
    jQuery("#btn_delete").on( "click", function(){
        var a = $(this).data('type'), checked = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.item_id').val();
        }).get();
        swal({
              title: "Are you sure?",
              text: "You will not be able to recover the data!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes, delete it!",
              closeOnConfirm: false,
              showLoaderOnConfirm: true
            },
            function(){ 
                $(".confirm").attr('disabled','disabled');
                var $data = {
                    action: 'CSdeleteRedirectionRule',
                    item_id: checked,
                    type : a
                };

                $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: $data,
                  async: false,
                  beforeSend: function( xhr ) {
                    if( xhr && xhr.overrideMimeType ) {
                        xhr.overrideMimeType('Content-type: application/json; charset=UTF-8');
                    }
                  },
                  dataType: 'json',
                  success: function( data, textStatus, XMLHttpRequest ) {
                                  console.log(data);
                      if( 200 === XMLHttpRequest.status ){
                          if( false === data.error ){
                                swal( { title: ''+data.title+'', text: ''+data.response_text+'', type : "success", html: true, timer: 2500 });
                                for(i=0; i< data.id.length; i++){
                                    $("#item_id_"+data.id[i]).css({'background':'orangered'}).fadeTo("slow", 0.33).fadeOut('blind');;
                                }
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

            });
    });
    
    $('[data-toggle="tooltip"]').tooltip({html: true});
    $("#make_inactive, #make_active").on("click", function(){
        var a = $(this).attr('id'), c = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.item_id').val();
        }).get();
        
        c.toString();
        if( c.length === 0){
            swal( 'Ops!', 'Please select an item and try this action.', "error");
        }
        else if( a === 'make_inactive' || a === 'make_active'){
            swal({
              title: "Are you sure?",
              text: "Your selected action will perform immediately!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes, do it!",
              closeOnConfirm: false,
              showLoaderOnConfirm: true
            },
            function(){ 
                $(".confirm").attr('disabled','disabled');
                var $data = {
                    action: 'CSRedirectionBulkActBtn',
                    item_id: c,
                    type : a
                };

                $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: $data,
                  async: false,
                  beforeSend: function( xhr ) {
                    if( xhr && xhr.overrideMimeType ) {
                        xhr.overrideMimeType('Content-type: application/json; charset=UTF-8');
                    }
                  },
                  dataType: 'json',
                  success: function( data, textStatus, XMLHttpRequest ) {
    //                              console.log(data);
                      if( 200 === XMLHttpRequest.status ){
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
            });
        }
        
    });
    
//    create new group
    jQuery("#create_new_group").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSaddNewRedirectionGroup',
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
                            $('#create_new_group')[0].reset();
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
    
    
    jQuery("#FormRedirectionOptions").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSaddRedirectionOptoins',
            data: jQuery(this).serialize()
        };
        
        $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: $data,
              async: false,
              beforeSend: function(xhr) {
                if( xhr && xhr.overrideMimeType ) {
                    xhr.overrideMimeType('Content-type: application/json; charset=UTF-8');
                }
              },
              dataType: 'json',
              success: function( data, textStatus, XMLHttpRequest ) {
//                                    console.log(data);
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

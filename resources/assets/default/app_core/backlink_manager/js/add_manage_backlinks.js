/**
 * Add Manage Backlink Script
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @version 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

jQuery(document).ready(function( $ ){
    var $loading_img = jQuery("#loading_gif").val();
    jQuery("#FindBacklinkMatrix").on( "submit", function(){
        var $form = jQuery(this);
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSaddNewBacklinks',
            data: $form.serialize()
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
                                    console.log(data);
                    if(200 === XMLHttpRequest.status){
                        if( false === data.error ){
                            swal( { title: ''+data.title+'', text: ''+data.response_text+'', type : "success", html: true, timer: 2500 });
                            $('#FindBacklinkMatrix')[0].reset();
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
    
    //keyword group
    jQuery("#group").on("change",function(){
        if( jQuery(this).val() === 'new'){
            jQuery(".create_new_group_field").addClass("animated fadeInDown").css('display','block');
            jQuery("#new_group_name").attr( 'required', 'required' ).removeAttr('disabled');
        }else{
            jQuery(".create_new_group_field").removeClass("animated fadeInDown").css('display','none');
            jQuery("#new_group_name").removeAttr('required').attr('disabled','disabled');
        }
    });
    
    jQuery("#create_new_cat").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSaddNewBacklinksCat',
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
                            $('#create_new_cat')[0].reset();
                            if( typeof data.current_url === 'undefined'){
                                window.location = window.location.href;
                            }else{
                                window.location = data.current_url;
                            }
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
          return $(this).closest('tr').find('input.backlink_id').val();
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
                    action: 'CSdeleteBacklink',
                    backlink_id: checked,
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
                                    $("#backlink_id_"+data.id[i]).css({'background':'orangered'}).fadeTo("slow", 0.33).fadeOut('blind');;
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
    
    $("#btn_compare, #btn_compare_date, #update_now, #set_auto_update_backlink, #remove_auto_update_backlink").on("click", function(){
        var a = $(this).attr('id'), c = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.backlink_id').val();
        }).get();
        
        c.toString();
        if(c.length === 0 ){
                swal( 'Ops!', 'Please select an item and try this action.', "error");
        }
        else if(a === 'btn_compare_date'){
            var h = $("#base_url").val();
            swal({
              title: "Processing..",
              text: "Please wait a while...",
              timer: 20000,
              imageUrl: $loading_img,
              showConfirmButton: false
            });
            window.location = h+'&backlinkIds='+c+'&compare_by_date=true';
        }else if( a === 'btn_compare'){
            if(c.length < 2){
                swal({title: "Error!",text: "Please select at least 2 properties to compare.",type: "warning"});
            }else{
                var h = $("#base_url").val();
                swal({
                  title: "Processing..",
                  text: "Please wait a while...",
                  timer: 20000,
                  imageUrl: $loading_img,
                  showConfirmButton: false
                });
                window.location = h+'&backlinkIds='+c+'&compare=true';
            }
        }else if( a === 'set_auto_update_backlink' || a === 'remove_auto_update_backlink' ){
            swal({
              title: "Processing..",
              text: "Please wait a while...",
              timer: 50000,
              imageUrl: $loading_img,
              showConfirmButton: false
            });
            var $data = {
                action: 'CSsetAutoUpdateBacklink',
                backlink_id: c,
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
        }else if( a === 'update_now'){
            if(c.length >1 ){
                swal({title: "Error!",text: "You can\'t select more then 1 backlink to update instantly.",type: "warning"});
            }else{
                var w = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
                  return $(this).closest('tr').find('input.backlink_url').val();
                }).get();
                
                swal({
                  title: "Processing..",
                  text: "Please wait a while...",
                  timer: 50000,
                  imageUrl: $loading_img,
                  showConfirmButton: false
                });
                var $data = {
                    action: 'CSbacklinkAutoUpdateInstantly',
                    backlink_id: c,
                    backlink_url: w
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
            }
        }
        
    });
    
    jQuery("#FormBacklinkOptions").on( "submit", function(){
        var $form = jQuery(this);
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: 'CSbacklinkOptoins',
            data: $form.serialize()
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
    
});

/**
 * Submit From
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

jQuery(document).ready(function( $ ){
   var $loading_img = jQuery("#loading_gif").val();
   jQuery("form").on( "submit", function(){
        swal({
          title: "Processing..",
          text: "Please wait a while...",
          timer: 50000,
          imageUrl: $loading_img,
          showConfirmButton: false
        });
        var $data = {
            action: jQuery("#action").val(),
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
    
    
    jQuery("#btn_delete").on( "click", function(){
        var a = $(this).data('action'), t = $(this).data('type'), c = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
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
                    action: a,
                    item_id: c,
                    type: t
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
    
    //tooltip into manage page
    $('[data-toggle="tooltip"]').tooltip({html: true});
    
    //bulk action button
    $("#btn_compare_date, #update_now, #set_auto_update, #remove_auto_update").on("click", function(){
        var $this = $(this);
        var t = $(this).attr('id'), c = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.item_id').val();
        }).get();
        c.toString();
        if( c.length === 0){
            swal( 'Ops!', 'Please select an item and try this action.', "error");
        }
        else if(t === 'btn_compare_date'){
            var h = $("#base_url").val();
            swal({
              title: "Processing..",
              text: "Please wait a while...",
              timer: 20000,
              imageUrl: $loading_img,
              showConfirmButton: false
            });
            window.location = h+'&item_ids='+c+'&compare_by_date=true';
        }else if( t === 'btn_compare'){
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
                window.location = h+'&item_ids='+c+'&compare=true';
            }
        }else if( t === 'set_auto_update' || t === 'remove_auto_update' || t === 'set_auto_update_backlink' || t === 'remove_auto_update_backlink' ){
            swal({
              title: "Processing..",
              text: "Please wait a while...",
              timer: 50000,
              imageUrl: $loading_img,
              showConfirmButton: false
            });
            var $data = {
                action: $this.data('action'),
                item_id: c,
                type : t
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
        }else if( t === 'update_now'){
            if(c.length >1 ){
                swal({title: "Error!",text: "You can\'t select more then 1 item to update instantly.",type: "warning"});
            }else{
                
                swal({
                  title: "Processing..",
                  text: "Please wait a while...",
                  timer: 50000,
                  imageUrl: $loading_img,
                  showConfirmButton: false
                });
                var $data = {
                    action: $this.data('action'),
                    item_id: c
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
    
});
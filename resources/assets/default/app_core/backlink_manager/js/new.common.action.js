/**
 * Submit From
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

jQuery(document).ready(function( $ ){
   jQuery("#btn_delete").on( "click", function(){
        var _action = $(this).data('action'),n = $("#aios_nonce").val(), _base_url = $("#base_url").val(), t = $(this).data('type'), c = $('#demo-custom-toolbar').find('[type="checkbox"]:checked').map(function(){
          return $(this).closest('tr').find('input.item_id').val();
        }).get();
        swal({
              title: "Are you sure?",text: "You will not be able to recover the data!",type: "warning",showCancelButton: true,confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",closeOnConfirm: false,showLoaderOnConfirm: true
            },
            function(){ 
                $(".confirm").attr('disabled','disabled');
                var $data = {_aios_nonce : n,_item_id: c,_type: t };
                $.ajax({
                  type: 'POST',
                  url: _base_url+'&action='+_action,
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
});
/**
 * CSMBP Long Tail Keyword Finder
 * 
 * @package CSMBP
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.com>
 */

var Aios_Keyword_Suggestion = function( _obj ){
    this.$ = _obj.$;
    this.container = _obj.container;
    this.startSearch = _obj.startSearch;
    this.searchTypes = _obj.searchTypes;
    this.btnStart = _obj.btnStart;
    this.btnStop = _obj.btnStop;
    this.beforeResultsContainer = _obj.beforeResultsContainer;
    this.resultsContainer = _obj.resultsContainer;
    this.queryContainer = _obj.query;
    this.defaultResultNotice = _obj.defaultResultNotice;
    this.counter = _obj.counter;
    this.progress = _obj.progress;
    this.checkAll = _obj.checkAll;
    this.searchFinishedNotice = _obj.searchFinishedNotice;
};

Aios_Keyword_Suggestion.prototype.init = function(){
    this.$( this.btnStart ).on( 'click', this.$.proxy( this.setup, this ) );
    this.$( this.btnStop ).on( 'click', this.$.proxy( this.stop, this ) );
    this.$( this.checkAll ).on( 'click', this.$.proxy( this.check_all, this ) );
};

Aios_Keyword_Suggestion.prototype.setup = function(){
    //get user query
    this.get_user_query();
    if( this.user_query.length !== 0 ){
//        console.log( 'started');
        //disable inputs
        this.$( this.defaultResultNotice + ',' + this.searchFinishedNotice ).slideUp( 'slow' );
        this.$( this.btnStart ).attr( 'disabled', 'disabled' );
        this.$( 'input'+this.searchTypes+':checkbox, input' ).attr( 'disabled', 'disabled' );
        this.$( this.btnStop + ',' + this.resultsContainer + ',' + this.progress ).show( 'slow' );
        //get the values
        this.startSearch = true;
        this.keywords = [];
        this.keywords[ 'loop' ] = 0;
        //init the query 
        this.keywords_to_query( this.user_query );
        this.$('html, body').animate({ scrollTop: this.$( this.beforeResultsContainer ).offset().top }, 800);
        //init the function
        this.automation = setInterval( this.initialization.bind(this), 750 );
    }
};

Aios_Keyword_Suggestion.prototype.initialization = function(){
    if( this.startSearch === true ){
        var searchFinished = true;
        for( var i=0; i< this.selected_types.length;i++){
            if( this.keywords[ 'loop' ] === 0 ) { //first loop
                this.nextSearch = 0; 
                this.searchRunning = false;
                this.keywords[ 'loop' ]++;
                this.initSearch = true;
            }

            if( i === this.nextSearch && this.searchRunning === false ){
                if( typeof this.selected_types[i] !== 'undefined' && typeof this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ][ this.keywords[ 'loop' ]] !== 'undefined' ){
                    
                    //active selector
                    this.$( ".KeySuggList").find( '.key-search-active').removeClass( 'key-search-active');
                    this.$( '.s_' + this.selected_types[i] ).addClass( 'key-search-active' );
                    
                    this[this.selected_types[i]]( this.selected_types[i], this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ][ this.keywords[ 'loop' ]] );
                    searchFinished = false;
//                    this.$( this.selected_types[i] ).removeClass( 'key-search-active' );
                }

                if( typeof this.selected_types[i] !== 'undefined' && typeof this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ][ this.keywords[ 'loop' ]] === 'undefined' ){
                    delete this.selected_types[i]; //if search keyword not found, remove search type
                }
                this.nextSearch = i+1; 
                if( this.nextSearch >= this.selected_types.length ){
                    this.nextSearch = 0; 
                    if( this.initSearch === false ){ //check first loop
                        this.keywords[ 'loop' ]++;
                    }
                    this.initSearch = false;
                }
                break; //break the loop
            }
        }
       
        if( searchFinished === true ){
            this.$( this.searchFinishedNotice ).show( 'slow' );
//            console.log( 'search finished');
            this.stop();
        }
    }else if( this.stopSearch === true ){
        clearInterval( this.automation );
    }
};

Aios_Keyword_Suggestion.prototype.get_user_query = function(){
    this.user_query = this.$( this.queryContainer ).val();
    var _selected_items = [];
    this.$('input'+this.searchTypes+':checkbox:checked').each(function () {
        _selected_items.push($(this).val());
    });
    this.selected_types = _selected_items;
    if( this.user_query.length === 0 ){
        this.$( this.queryContainer ).focus();
    }
};

Aios_Keyword_Suggestion.prototype.keywords_to_query = function( keyword, id ){
    if( typeof keyword === 'undefined' ) return false;
    if( typeof id === 'undefined'){ //on first keyword
        for( var i=0; i< this.selected_types.length;i++){
            if( typeof this.keywords[ this.selected_types[i] ] === 'undefined' ){
                this.keywords[ this.selected_types[i] ] = [];
                if( typeof this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ] === 'undefined' ){
                    this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ] = [];
                }
            }
            this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ].push( keyword );
            for(j = 0; j < 26; j++){
                var char = String.fromCharCode(97 + j);
                this.keywords[ this.selected_types[i] ][ 'keywords_to_query' ].push( keyword+' '+char );
            }
        }
    }else{
        this.keywords[ id ][ 'keywords_to_query' ].push( keyword );
        for(j = 0; j < 26; j++){
            var char = String.fromCharCode(97 + j);
            this.keywords[ id ][ 'keywords_to_query' ].push( keyword+' '+char );
        }
    }
};

Aios_Keyword_Suggestion.prototype.stop = function(){
//    console.log( 'stoped' );
    this.startSearch = false;        
    this.stopSearch = true;    
    this.$( this.btnStop ).hide('slow');
    this.$( this.progress ).hide( 'slow' );
    this.$( this.queryContainer ).val('');
    this.$( this.btnStart ).removeAttr( 'disabled' );
    this.$( 'input'+this.searchTypes+':checkbox, input').removeAttr( 'disabled' );
    clearInterval( this.automation );
};

Aios_Keyword_Suggestion.prototype.google = function( _id, _query ){
   var _data ={q: _query,client: 'chrome'}; 
   this.ajax_api_call( AIOS_APIS.g, _data, _id);
};

Aios_Keyword_Suggestion.prototype.bing = function(_id, _query ){
   var _data ={Query: _query,Market: "en-us"}; 
   this.ajax_api_call( AIOS_APIS.b, _data, _id);
};

Aios_Keyword_Suggestion.prototype.youtube = function(_id, _query ){
   var _data ={q: _query,client: "chrome",ds: "yt"}; 
   this.ajax_api_call( AIOS_APIS.u, _data, _id);
};

Aios_Keyword_Suggestion.prototype.yahoo = function(_id, _query ){
   var _data ={command: _query,nresults: "20",output: "jsonp"}; 
   this.ajax_api_call( AIOS_APIS.y, _data, _id);
};

Aios_Keyword_Suggestion.prototype.ebay = function(_id, _query ){
   var _data ={kwd: _query,v: "jsonp",_dg: "1",sId: "0"}; 
   this.ajax_api_call( AIOS_APIS.e, _data, _id);
};

Aios_Keyword_Suggestion.prototype.amazon = function(_id, _query ){
   var _data ={q: _query,method: "completion",'search-alias': "aps",mkt: "1"}; 
   this.ajax_api_call( AIOS_APIS.a, _data, _id);
};

Aios_Keyword_Suggestion.prototype.ajax_api_call = function( url, data, _id ){
//    console.log( 'ac - ' + _id);
    this.$.ajax({
        url: url,
//        jsonp: "jsonp",
        dataType: "jsonp",
        async:true,
        crossDomain:true,
        data: data,
        error: this.$.proxy( this.on_error, this ),
        success: this.$.proxy( this.on_success, this,  { section_id : _id } )
    });
};

Aios_Keyword_Suggestion.prototype.on_error = function( xhr){
   swal( AIOS.swal_error_title, xhr.responseText, 'error' );
};

Aios_Keyword_Suggestion.prototype.on_success = function( data, _response ){
    if( typeof _response !== 'undefined' ){
        //hold the loop
        this.searchRunning = true; 
        this.startSearch = false;
        this['assemble_'+data.section_id]( data.section_id, _response );
        //release the loop
        this.searchRunning = false; 
        this.startSearch = true;
    }else{
//        console.log( 'wrong response' );
    }
};

Aios_Keyword_Suggestion.prototype.assemble_google = function( _id, _response ){
    if( typeof _response[1] !== 'undefined' ){
        var _res = _response[1];
        this.data_rearrangement( _id, _res );
    }
};

Aios_Keyword_Suggestion.prototype.assemble_bing = function( _id, _response ){
    if( typeof _response[1] !== 'undefined' ){
        var _res = _response[1];
        this.data_rearrangement( _id, _res );
    }
};

Aios_Keyword_Suggestion.prototype.assemble_yahoo = function( _id, _response ){
    if( typeof _response.gossip.results !== 'undefined' ){
        var _res = this.$.map(_response.gossip.results, function(value, index) {
            for( key in value ){
                return value[key];
            }
        });
    
        if( typeof _res !== 'undefined' ){
            this.data_rearrangement( _id, _res );
        }
    }
};

Aios_Keyword_Suggestion.prototype.assemble_youtube = function( _id, _response ){
    if( typeof _response[1] !== 'undefined' ){
        var _res = _response[1];
        this.data_rearrangement( _id, _res );
    }
};
Aios_Keyword_Suggestion.prototype.assemble_ebay = function( _id, _response ){
    if( typeof _response.res !== 'undefined' && typeof _response.res.sug !== 'undefined' ){
        var _res = _response.res.sug;
        this.data_rearrangement( _id, _res );
    }
};
Aios_Keyword_Suggestion.prototype.assemble_amazon = function( _id, _response ){
    if( typeof _response[1] !== 'undefined' ){
        var _res = _response[1];
        this.data_rearrangement( _id, _res );
    }
};

Aios_Keyword_Suggestion.prototype.data_rearrangement = function( _id, _data ){
    
    if( typeof this.keywords[ _id ] === 'undefined' ){
        this.keywords[ _id ] = [];
    }

    if( this.keywords[ _id ].length === 0 ){
        this.keywords[ _id ].push(' '); //insert empty to first loop
        this.$( this.counter + _id ).text( 0 );
    }
    
    for(var i = 0; i < _data.length; i++){
        if( this.data_filter( _data[i] ) !== false){
            var k = '<input type="checkbox" id = "'+_id+'" class = "k_'+_id+'" name = "keywords['+_id+'][]" value="'+_data[i]+'" /> '+ _data[i];
            if( this.data_exists( this.keywords[ _id ], k ) === false ){
                this.keywords[ _id ].push( k );
                this.$( this.counter + _id ).text( this.keywords[ _id ].length - 1 );
                this.keywords_to_query( _data[i], _id ); //add to search query
            }
        }
    }

    if( this.keywords[ _id ].length > 1 ){
        this.$( "#r_" +_id ).html( this.keywords[ _id ].join( '<li>') );
        this.$( "#r_" +_id  ).animate({scrollTop: this.$( "#r_" +_id  ).prop("scrollHeight")}, 500);
    }else{
        this.$( "#r_" +_id ).html( AIOS_APIS.nkf ); // no keyword found
    }
    
};

Aios_Keyword_Suggestion.prototype.data_exists = function( _array, _handler ){
    var a = _array.indexOf( _handler ); //support IE 8+
    if( a !== -1 ){ // value found
        return true;
    }else return false;
};

Aios_Keyword_Suggestion.prototype.data_filter = function( _input ){
    _input.replace("\\u003cb\\u003e", "");
    _input.replace("\\u003c\\/b\\u003e", "");
    _input.replace("\\u003c\\/b\\u003e", "");
    _input.replace("\\u003cb\\u003e", "");
    _input.replace("\\u003c\\/b\\u003e", "");
    _input.replace("\\u003cb\\u003e", "");
    _input.replace("\\u003cb\\u003e", "");
    _input.replace("\\u003c\\/b\\u003e", "");
    _input.replace("\\u0026amp;", "&");
    _input.replace("\\u003cb\\u003e", "");
    _input.replace("\\u0026", "");
    _input.replace("\\u0026#39;", "'");
    _input.replace("#39;", "'");
    _input.replace("\\u003c\\/b\\u003e", "");
    _input.replace("\\u2013", "2013");
    if (_input.length > 4 && _input.substring(0, 4) === "http") return false;
    return _input; 
};

Aios_Keyword_Suggestion.prototype.check_all = function( $this ){
    var _id = $this.target.id;
    if (this.$( $this.target ).is(":checked")) {
        this.$( ".k_"+_id ).each(function() {
            this.checked = true; 
        });
    }else{
        this.$( ".k_"+_id ).each(function() {
            this.checked = false; 
        });
    }
};
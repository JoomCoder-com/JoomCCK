var FelixLib_v1 =
{
  styleAnimate : function ( element, sProperty, to_cnd, step, params ) 
  {
     
    var E = document.getElementById( element );
    if( typeof params.styleTmpl != "undefined" ) {
      var curr_cnd = E.style[ sProperty ].match( params.styleTmpl );
      curr_cnd = parseInt( curr_cnd[2] );
    } else {
      var curr_cnd = parseFloat( E.style[ sProperty ] );
      var suffix   = E.style[ sProperty ].replace( /[\.\-0-9]/g, "" );
    }
    
    if( typeof params.sign == "undefined" ) {
      if ( curr_cnd < to_cnd ) {
        params.sign = 1;
      } else if( curr_cnd == to_cnd ) {
        params.sign = 0;
      } else if( curr_cnd > to_cnd ) {
        params.sign = -1;
      }
    }
    
    var new_cnd = Math.round( ( curr_cnd + step * params.sign ) * 10000 ) / 10000;
    
    if( ( params.sign <= 0 && new_cnd <= to_cnd ) || ( params.sign >= 0 && new_cnd >= to_cnd ) ){
      new_cnd = to_cnd;
    }
    
    if( typeof params.styleTmpl != "undefined" ) {
      E.style[sProperty] = E.style[ sProperty ].replace( params.styleTmpl, "$1"+new_cnd+"$3" );
    } else {
      E.style[sProperty] = new_cnd + suffix;
    }
    
    if( new_cnd != to_cnd ) {
      var str_params = "{";
      var str_p = []; 
      for( var i in params ) {
         str_p[ str_p.length ] = "'"+i+"':"+( typeof params[i] == "string" ? "'"+params[i]+"'" : params[i] );
      }
      str_params += str_p.join( "," );
      str_params += "}";
    
      setTimeout( "FelixLib_v1.styleAnimate( '"+element+"', '"+sProperty+"', "+to_cnd+", "+step+", "+str_params+" )", 1000/24);
    } else {
      if( params.onAfterAnimate ) {
        eval( unescape( params.onAfterAnimate ) );
      }
    }
  },
  
  "appendFormHidden" : function ( name, value, form )
  {
    var input = document.createElement( 'input' );
	  input.type = 'hidden';
	  input.name = name;
	  input.value = value;
	  form.appendChild( input );
	  return input;
  },
  
  "removeFormHidden" : function ( name, form )
  {
    var elements = document.getElementsByName( "name" );
    for( var i = 0; i < elements.length; i++ ) {
      if( elements[i].tagName.toLocaleLowerCase() == "hidden" && elements[i].form == form ) {
        from.removeChild( hidden ); 
      }
    } 
  },
  
  "setEvent" : function( element, event, f )
  {
    if( element.addEventListener && navigator.appName != 'Opera' ) {
      element.addEventListener( event, f, true);
    } else {
      element.attachEvent( 'on'+event, f);
    }
  },
  
  "unsetEvent" : function( element, event, f )
  {
    if( element.removeEventListener && navigator.appName != 'Opera' ) {
      element.removeEventListener( event, f, true );
    } else {
      element.detachEvent( 'on'+event, f );
    }
  }
}
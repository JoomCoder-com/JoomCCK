felixItemManager_v1 =
{
  'remove' : function ( elements )
  {
    if( typeof elements == "string" ) {
      elements = [ elements ];
    }
    
    for( var i = 0; i < elements.length; i++ ) {
      var E = document.getElementById( elements[i] );

      if ( typeof document.body.style.filter != "undefined" ) {
        E.style.filter = "alpha(opacity:"+100+")";
	      FelixLib_v1.styleAnimate( elements[i], 'filter', 0, 10, {"styleTmpl" : /(alpha\(opacity\:)([0-9]+)(\))/ , "onAfterAnimate" : 'felixItemManager_v1.removeStage2("'+elements[i]+'")' } );
	    } else if ( typeof document.body.style.opacity != "undefined" ) {
	      E.style.opacity = 1;
	      
	      FelixLib_v1.styleAnimate( elements[i], 'opacity', 0, 0.1, { "onAfterAnimate" : 'felixItemManager_v1.removeStage2("'+elements[i]+'")' } );
	    } else {
	      alert("Opacity not supported");
	    }
    }
  },
  
  'removeStage2' : function ( element )
  {
    var E = document.getElementById( element );
    var t = E.offsetHeight;
    E.style.overflow = "hidden";
    E.style.height = E.offsetHeight + "px";
    E.style.height = parseInt( E.style.height ) - ( E.offsetHeight - t ) + "px";
    
    FelixLib_v1.styleAnimate( element, 'height', 0, 2, { "onAfterAnimate" : 'felixItemManager_v1.removeStage3("'+element+'")' } );
  },
  
  'removeStage3' : function ( element )
  {
    var E = document.getElementById( element );
    E.parentNode.removeChild( E );
  }
};
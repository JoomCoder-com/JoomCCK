function removeDate(id)
{
	var myEffect = new Fx.Morph('date' + id, {
	    duration: 'short',
	    transition: Fx.Transitions.Sine.easeOut
	});
	myEffect.addEvent('complete', function(){$('date' + id).dispose();});
	myEffect.start({'height': 0});
			
}
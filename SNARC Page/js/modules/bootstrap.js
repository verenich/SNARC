define( ["jQuery","lib/nicescroll","lib/yummy","lib/pace"],
	function( jQuery, nicescroll ){
		console.log("**** Boostraping ****");
		
		$("body").niceScroll({cursorcolor:"#000"});	 
		$('.wrapper').toggleClass('on off');
		Pace.start();
		
		console.log("**** Finished the Bootstrap ****");
	}
);
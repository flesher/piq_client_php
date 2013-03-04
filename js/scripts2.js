//scrolls past browser search on load
nobrowser = function(){
	var page = document.getElementById('page'),
	    ua = navigator.userAgent,
	    iphone = ~ua.indexOf('iPhone') || ~ua.indexOf('iPod'),
	    ipad = ~ua.indexOf('iPad'),
	    ios = iphone || ipad,
	    fullscreen = window.navigator.standalone,
	    android = ~ua.indexOf('Android'),
	    lastWidth = 0;
	 
	if (android) {
	  window.onscroll = function() {
	    page.style.height = window.innerHeight + 'px'
	  } 
	}
	var setupScroll = window.onload = function() {
	  if (ios) {
	    var height = document.documentElement.clientHeight;
	    if (iphone && !fullscreen) height += 60;
	    page.style.height = height + 'px';
	  } else if (android) {
	    page.style.height = (window.innerHeight + 56) + 'px'
	  }
	  setTimeout(scrollTo, 0, 0, 1);
	};
	(window.onresize = function() {
	  var pageWidth = page.offsetWidth;
	  if (lastWidth == pageWidth) return;
	  lastWidth = pageWidth;
	  setupScroll();
	})();
}

//adds/removes class that shows expanded form of tracks on the queue
expand = function(){

	$('ol li:nth-of-type(1)').addClass("expand");
	
	$("li").click(function() {
		var index = $('li').index($(this));


		if ($(this).hasClass("expand expand-np") == true && index != 0)
			{
			$(this).removeClass("expand");
			setTimeout(function(){$("li").get(index).className = ""}, 500);
			}	
		else if ($(this).hasClass("expand expand-np") == false && index != 0)
			{
        $('li:nth-of-type(1n+2)').removeClass("expand expand-np");
      $(this).addClass("expand expand-np");
			}	
	});

}

//ajax post request on click of vote up vote down
vote = function(){

	// var url = "http://piq.fm/try/vote.php?song_id=" + _.template('<%= trackID %>');
	var base_url = "http://piq.fm/try/vote.php";
	// console.log(url);

	$(".vote-up").click(function(event) {
		event.preventDefault();
		event.stopPropagation();
    var track_id = $(this).parent().parent().data('trackid');
    var url = base_url + "?song_id=" + track_id;
    var voting = $(this).siblings(".vote-count");
		if ($(this).hasClass('vote-up-selected'))
			{
			$(this).removeClass('vote-up-selected');
			$.post(url, {vote_value: 0},function(data){
         var vote = data.votes;
         voting.html(vote);
      });
			}
		else if ($(this).siblings(".vote-down").hasClass('vote-down-selected'))
			{
			$(this).siblings(".vote-down").removeClass('vote-down-selected');
			$(this).addClass("vote-up-selected");
			$.post(url, {vote_value: 1},function(data){
         var vote = data.votes;
         voting.html(vote);
      });
			}
		else 
			{
			$(this).addClass("vote-up-selected");
			  $.post(url, {vote_value: 1}).done( function(data){
         var vote = data.votes;
         voting.html(vote);
      });
      console.log($(this));
      }
  });
	$(".vote-down").click(function(event) {
		event.preventDefault();
		event.stopPropagation();
  	var track_id = $(this).parent().parent().data('trackid');
    var url = base_url + "?song_id=" + track_id;
    var voting = $(this).siblings(".vote-count");
		if ($(this).hasClass('vote-down-selected'))
			{
			$(this).removeClass('vote-down-selected');
			$.post(url, {vote_value: 0}, function(data){
         var vote = data.votes;
         voting.html(vote);
      });
			}		
		else if ($(this).siblings(".vote-up").hasClass('vote-up-selected'))
			{
			$(this).siblings(".vote-up").removeClass('vote-up-selected');
			$(this).addClass("vote-down-selected");
			$.post(url, {vote_value: -1}, function(data){
        $(this).siblings(".vote-count").html(data.votes);
        var vote = data.votes;
        voting.html(vote);
      });
			}
		else
			{
			$(this).addClass("vote-down-selected");
			$.post(url, {vote_value: -1},  function(data){
        var vote = data.votes;
        voting.html(vote);
    });
			}
	});
}

//animates up search for song screen
addSong = function(){

	$('#add-song').click(function(){
    window.location = "http://piq.fm/try/search.php";
		// if ($(this).hasClass("add-expand") == true)
		// 	{
		// 	$(this).removeClass("add-expand");
		// 	}	
		// else if ($(this).hasClass("add-expand") == false)
		// 	{
		// 	$(this).addClass("add-expand");
		// 	}	
	});
}

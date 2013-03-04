var getResults = function(){
  var name = term.value;
  //var albumArt = "http://ws.spotify.com/search/1/track.json?q=" + albumArt;
  var queryTrack = "http://ws.spotify.com/search/1/track.json?q=" + name;
  var queryArtist = "http://ws.spotify.com/search/1/artist.json?q=" + name;

  $('.results').html("<h2 class='loading'>Searching library...</h2>");//loading message
  $('#artist-group').css('display', 'inherit');
  $('#track-group').css('height','205px'); 

  //search spotify library and generate list of track results
  $.getJSON(queryTrack, function(data){
    $('#resultsTrack').html('');
   
    $.each(data.tracks, function(){

      var territories = (this.album.availability.territories);
      var sub_ter = territories.split(" ");
      var check = $.inArray("US", sub_ter);

      if (check != -1){
        $('#resultsTrack').append('<li data-spotifyid="'+this.href+'"><h1>'+this.name+'</h1><span class="tk-nimbus-sans-condensed" data-spotifyid=' + this.href + '>' + this.artists[0].name + '</span></h1>' + '<button class="add"><img src="img/add-arrow.png"></button></li>');
      }

    });

    $("#resultsTrack li").click(function(){
      var id = $(this).data("spotifyid");
      var link = "http://piq.fm/try/que.php?song="+id;
      $.post("http://piq.fm/try/que.php?song="+id, function(data){
        location.reload();
      });
    });
  });

  $.getJSON(queryArtist, function(data){
    $('#resultsArtist').html('');
    
    $.each(data.artists, function(){
      $('#resultsArtist').append('<li data-spotifyid="'+this.name+'"><h1 class="tk-nimbus-sans-condensed">' + this.name + '</h1><button class="add"><img src="img/more-arrow.png"></button></li>');
    });

    //re-search by artist
    $("#resultsArtist li").click(function(){
      var artist = $(this).data("spotifyid");
      var artist_c = artist.substr(0).replace("&", "");
      var lookup = "http://ws.spotify.com/search/1/track.json?q=artist:"+artist_c;
      console.log(lookup);

      $('.results').html("<h2 class='loading'>pulling from library...</h2>");//loading message
      $('#artist-group').css('display', 'none');
      $('#track-group').css('height','1297px');

      $.getJSON(lookup, function(data){
        $('#resultsTrack').html('');
     
        $.each(data.tracks, function(){

          var territories = (this.album.availability.territories);
          var sub_ter = territories.split(" ");
          var check = $.inArray("US", sub_ter);

          if (check != -1){
            $('#resultsTrack').append('<li data-spotifyid="'+this.href+'"><h1>'+this.name+'</h1><span class="tk-nimbus-sans-condensed" data-spotifyid=' + this.href + '>' + this.artists[0].name + '</span></h1>' + '<button class="add"><img src="img/add-arrow.png"></button></li>');
          }

        });
        
        $("#resultsTrack li").click(function(){
          var id = $(this).data("spotifyid");
          var link = "http://piq.fm/try/que.php?song="+id;
          $.post(link, function(data){
          }).done(function(){
            location.reload();
          });
        });
      });

    });

  });
}


$("li").hover(function(){
  $(this).children("h1").css("color", "#6dc7da");
})


//search field actions
$('#search').click(function(){
  getResults();
  
  if ($('#add-song').hasClass("add-expand") == false){
    $('#add-song').addClass("add-expand");
    $('#add-song').css('overflow', 'scroll');
    $('#page').css('overflow', 'hidden');
    $('#close-search').fadeIn(500);
  }
  
});

$('#term').keyup(function(event){
  if(event.keyCode == 13){
    getResults();
  
    if ($('#add-song').hasClass("add-expand") == false){
      $('#add-song').addClass("add-expand");
      $('#add-song').css('overflow', 'scroll');
      $('#page').css('overflow', 'hidden');
      $('#close-search').fadeIn(500);
    }
  }
});

//close search
$('#close-search').click(function(){
  $('#add-song').removeClass("add-expand");
  $('#add-song').css('overflow', 'inherit');
  $('#page').css('overflow', 'inherit');
  $(this).fadeOut(500);
});


$('.slide header').click(function(){
  var height_string = $(this).parent().css("height");

  var height = Math.round(height_string.substring(0, height_string.length - 2));
  console.log(height.type);

  if (height > 205){
    $(this).parent().css("height", "205px");
  } else if (height == 205){
    $(this).parent().css("height", "1297px");
  }
    
})


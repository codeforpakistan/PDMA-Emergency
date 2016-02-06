jQuery(document).ready(function($) {

	/* ----------- NEWS SLIDER ---------------*/
	var news = null;
    var news_len = 0;
    var news_counter = 0;
	news = $(".news-item");
	news_len = news.length - 1;
	$(".news").html(news.get(news_counter));

	  $("#left").click(function (e){
	      news_counter -= 1;
	      if(news_counter >= 0){
	        $(".news").html(news.get(news_counter));
	      } else {
	        news_counter += 1;
	      }
	  });

	  $("#right").click(function (e){
	      news_counter += 1;
	      if(news_counter <= news_len){
	       $(".news").html(news.get(news_counter));
	      }else {
	        news_counter -= 1;
	      }
	  }); 
	  /* ----------- END NEWS SLIDER ---------------*/

	  /* ----------- NEWS SLIDER ---------------*/
	var events = null;
    var events_len = 0;
    var events_counter = 0;
	events = $(".events-item");
	events_len = events.length - 1;
	$(".events").html(events.get(events_counter));

	  $("#eleft").click(function (e){
	      events_counter -= 1;
	      if(events_counter >= 0){
	        $(".events").html(events.get(events_counter));
	      } else {
	        events_counter += 1;
	      }
	  });

	  $("#eright").click(function (e){
	      events_counter += 1;
	      if(events_counter <= events_len){
	       $(".events").html(events.get(events_counter));
	      }else {
	        events_counter -= 1;
	      }
	  }); 
	  /* ----------- END NEWS SLIDER ---------------*/
});
$(document).ready(function() {
  
  /*$(".fb-comments").hide();
  
  $("#commentToggle").click(function(){
    $(".fb-comments").slideToggle();
    return false;
  });*/
  
  // http://www.dailycoding.com/Posts/default_text_fields_using_simple_jquery_trick.aspx
  $(".defaultText").live('focus', function(srcc){
    if ($(this).val() == $(this)[0].title)
    {
        $(this).removeClass("defaultTextActive");
        $(this).val("");
    }
  });
  $(".defaultText").live('blur', function(){
    if ($(this).val() == "")
    {
        $(this).addClass("defaultTextActive");
        $(this).val($(this)[0].title);
    }
  });
  $(".defaultText").blur();
  
  
  tweetCounter();
  
  $('#preview').hide();
  $('#jsLib').hide();
  $('#hTag').hide();
  $('#copyTags').hide();
  $('#copyJs').hide();
  
  
  $("#includeJs").click(function(){
    var includeJs = $('#includeJs').is(':checked');
    if(includeJs){
      $('#jsLib').slideDown();
      $("#copyJs").show();
      
      $("#copyJs").zclip({
        path: "js/ZeroClipboard.swf",
        copy: function(){
          return $("textarea#jsLib").val();
        }
      });
      
    } else {
      $('#jsLib').slideUp();
      $("#copyJs").hide();
    }
  });
  
  $("#hashTags").click(function(){
    var hashTags = $('#hashTags').is(':checked');
    if(hashTags){
      
      var strVar = $("#codeWrapper").text();
      
      $.post("hashtags.php",      
    		{ str: strVar },
    		function(data){
    		  $("#hTag").html(data);
    		  $("#hTag").slideDown();
    		  $("#copyTags").show();
    		  
    		  $("#copyTags").zclip({
            path: "js/ZeroClipboard.swf",
            copy: function(){
              return $("#hTag").val();
            }
          });
    		}
    	);
    	
    } else {
      $('#hTag').slideUp();
      $("#copyTags").hide();
    }
  });
  
  
  
  
  $("input[type=checkbox]").click(function(){
    // reset previous stuff
    $('#preview').hide();
    $("#embeddedTweets").html('');
    $("#codeWrapper").html('');

    $('.tweet').each(function(index) {
      if( $(this).is(':checked') ) {
        var tweetHtml = $(this).next().next(".tweetToCopy").html();
        // append html
        $("#embeddedTweets").append(tweetHtml);    
        // append code
        $("#codeWrapper").append(encode(tweetHtml));
      }
    });
    
    // header
    $("#previewHeader").html("<h2>Preview and code: </h2>");  
	  
    // show number of tweets in digest
    tweetCounter();
    
    var boxesChecked = $('form input.tweet:checked').size();
    if(boxesChecked) {
      $('#preview').show();
    }
  
    // copy to clipboard
    $("#copyHtml").zclip({
      path: "js/ZeroClipboard.swf",
      copy: function(){
        return $("#codeWrapper").text();
      }
    });
    
  });

});


function toggleChecked(status) {
  $(".tweet").each( function() {
    $(this).attr("checked",status);
  })
}

// http://stackoverflow.com/questions/1219860/javascript-jquery-html-encoding
function encode(input){
  return $('<div/>').text(input).html();
}

function decode(input){
  return $('<div/>').html(input).text();
}

function tweetCounter() {
  var boxesChecked = $('form input.tweet:checked').size();
  var tweetTxt = ' tweets';
  if(boxesChecked == 1) {
    tweetTxt = ' tweet';
  }
  $("#counter").html(boxesChecked + tweetTxt + " in Tweet Digest&nbsp;|&nbsp;<a id='goback' href='index.php'>Change @user &amp; # of tweets</a>");
}
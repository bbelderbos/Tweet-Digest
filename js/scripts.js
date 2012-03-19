$(document).ready(function() {
  
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
  
  
  // submit form
  $("#returnHTML").submit(function(){	
    return false;
  });
  
  $("input[type=checkbox]").click(function(){
    var boxesChecked = $('form input.tweet:checked').size();
    var tweet = '';
    if(boxesChecked == 1) {
      tweet = ' tweet';
    } else {
      tweet = ' tweets';
    }
    $("#counter").html(boxesChecked + tweet + " in Tweet Digest");
  });
  
  $("#preview").hide();
  
  $("#generateHtml").click(function(){	
    
    $("#preview").hide();
    $("#loader").html('<img src="img/progressBar.gif" alt="Formatting tweets ...">');  
		$("#embeddedTweets").html('');  
		$("#previewHeader").html("");  
		$("#codeWrapper").html("");  
		
    var includeJs = $('#includeJs').is(':checked');
		if(includeJs){
			var includeJsVal = 1; 
		} else {
			var includeJsVal = 0;
		}
    // http://stackoverflow.com/questions/1557273/jquery-post-array-of-multiple-checkbox-values-to-php
    var tweets = $('.tweet:checked').map(function(i,n) {
        return $(n).val();
    }).get(); //get converts it to an array
    
    $.post("preview.php",      
  		{ 'selectedTweets[]': tweets, js: includeJsVal },
  		function(data){
  		  $("#previewHeader").html("<h2>Preview and code: </h2>");  
  			$("#embeddedTweets").html(data);  
  			$("#codeWrapper").html(encode(data));
  			$("#loader").html('');
  			$("#preview").slideDown();
        
          $("#copy-button").zclip({
            path: "js/ZeroClipboard.swf",
            copy: function(){
              return $("#codeWrapper").text();
            }
          });
          
  		}
  	);
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
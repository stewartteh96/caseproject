var defaultLocation = new google.maps.LatLng(53.36652, -2.29855); //Set default location based on CCSC coordinates
var selectedLocation = ""; //Retrieved based on tweet's location
// var query = "";
// var marker;

$(document).ready(function () {
  var mapCanvas, map, latLng, mapOpt;

  /**
   * Function to initialize Google Maps and place marker
   */
  function init() { 
    latLng = defaultLocation;

    mapOpt = { //Specify map properties
      center: latLng,
      zoom: 16,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    mapCanvas = document.getElementById("mapCanvas");
    map = new google.maps.Map(mapCanvas, mapOpt);

    ccscMarker = new google.maps.Marker({ 
      position: latLng,
      map: map
    });

    var infoWindow = new google.maps.InfoWindow({
      content: "Coast City Sports Centre" //Define content to display at marker
    });

    infoWindow.open(map, ccscMarker); //Place marker on CCSC location with content
  } //End init()

  init();
  google.maps.event.addDomListener(window, "load", init);

  /**
   * Function to search for tweets 
   */
  $("#search").click(function () {
    var query = $("#keyword").val(); //Obtain search keyword from textbox
    query = query.replace("#", "%23"); //Convert "#" symbol to "%23"
    var url = "server/twitter/search.php";

    $.post(url, { query: query }, function (tweets) {
      // console.log(tweets);
      $("#tweetResult").html("");
      var parseStr = "<table id='tblTweetResults'>";
      parseStr += "<tr>";
      parseStr += "<th>Username</th>";
      parseStr += "<th>Tweet Message</th>";
      parseStr += "<th>Date</th>";
      parseStr += "</tr>";

      for (var i = 0; i < tweets.statuses.length; i++) { //Get username, tweet, date & location from tweet
        parseStr += "<tr>";
        parseStr += "<td>";
        parseStr += tweets.statuses[i].user.screen_name;
        parseStr += "</td>";

        parseStr += "<td>";
        parseStr += tweets.statuses[i].text;
        parseStr += "</td>";

        //Convert twitter date format to YYYY-MM-DD hh:mm:ss using moment library
        var dateString = tweets.statuses[i].created_at;
        var dateObj = new Date(dateString);
        var momentObj = moment(dateObj);
        var momentString = momentObj.format('YYYY-MM-DD hh:mm:ss');
        parseStr += "<td>";
        parseStr += momentString;
        parseStr += "</td>";

        parseStr += "<td style='display: none;'>";
        // parseStr += "<td >";
        if (tweets.statuses[i].user.geo_enabled) { //User enabled geolocation in Twitter account
          var placeName = tweets.statuses[i].place;

          if (placeName != null) { //Get location based on location tagged in tweet
            var lat = parseFloat(placeName.bounding_box.coordinates[0][0][1]);
            var lgt = parseFloat(placeName.bounding_box.coordinates[0][0][0]);
            selectedLocation = new google.maps.LatLng(lat, lgt);
            parseStr += "<div data-Lat=" + lat + " data-Lng=" + lgt + " >";
            parseStr += selectedLocation;
            parseStr += "</div>";
          }
          else { //Get location from users' account
            if (tweets.statuses[i].user.location != "") {
              selectedLocation = "";
              parseStr += tweets.statuses[i].user.location;
            }
            else { //Account location is empty
              selectedLocation = "";
              parseStr += "-";
            }
          }
        }
        else { //Unknown location
          selectedLocation = "";
          parseStr += "-";
        }
        parseStr += "</td>";
        parseStr += "</tr>";
      } //End for loop
      parseStr += "</table>";
      $("#tweetResult").html(parseStr);

      /**
       * Function to show tweet/account location when mouse over tweet
       */
      $("#tweetResult tr").mouseenter(function () {
        $(this).addClass("selectedStyle"); //Apply CSS to highlight hovered row

        var username = $(this).find('td:first-child').text(); //Get username from 1st td 
        var latLgtStr = $(this).find('td:nth-child(4) div').text(); //Get lat, lgt from div in 4th td
        latLgtStr = latLgtStr.replace(/[{()}]/g, ''); //Strip brackets
        latLgtStr = latLgtStr.split(',', 2); //Split by comma into lat, lgt 
        var accountLocation = $(this).find('td:nth-child(4)').text(); //Get account location from 4th td

        //Account location; latLgtStr does not contain comma, cannot be split
        if (latLgtStr.length == 1 && (accountLocation != "-" && accountLocation != "")) {
          var caption = "Tweeted by " + username + " based at " + accountLocation;
          $("#hoverdiv").text(caption).show(); //Display caption

          var left = $(this).offset().left + ($(this).width() / 2),
              top = $(this).offset().top;
          $("#hoverdiv").css("left", left)
                        .css("top", top); //Display caption at specified location
        }
        else if (latLgtStr.length > 1) { //Tweet location
          var geocoder = new google.maps.Geocoder;
          var infowindow = new google.maps.InfoWindow;
          var latlng = new google.maps.LatLng(parseFloat(latLgtStr[0]), parseFloat(latLgtStr[1]));
          var left = $(this).offset().left + ($(this).width() / 2),
              top = $(this).offset().top;

          //Obtain street, city, country using reverse geocoding based on tweet's lat & lgt
          geocoder.geocode({ 'location': latlng }, function (results, status) {
            if (status === 'OK') {
              if (results[0]) {
                var place = results[0].formatted_address;
                var caption = "Tweeted from " + place + " by " + username
                $("#hoverdiv").text(caption).show(); //Display caption
                $("#hoverdiv").css("left", left)
                              .css("top", top); //Display caption at specified location

                var marker = new google.maps.Marker({ //Place marker at location specified in tweet
                  position: latlng,
                  map: map
                });
                infowindow.setContent(place); //Define content to display at marker
                infowindow.open(map, marker);

                map.setCenter(latlng); //Set center to tweet's location
                var bounds = new google.maps.LatLngBounds(); //Set bounds to show both markers from tweet & Coast City Sports Center
                bounds.extend(ccscMarker.getPosition());
                bounds.extend(marker.getPosition());
                map.fitBounds(bounds);
              }
            }
          });
        }
      });

      /**
       * Function to remove CSS from hovered row
       */
      $("#tweetResult tr").mouseleave(function () {
        $(this).removeClass("selectedStyle");
      });

      /**
       * Function to hide caption & reinitialize Google Maps 
       * when user's mouse pointer leaves the row
       */
      $("#tweetResult td").mouseleave(function () {
        init();
        $("#hoverdiv").hide();
      });
    }); //End $.post
  }); //End search button click event

  /**
   * Function to request token to post tweet
   */
  $("#requestBtn").click(function () { 
    var tweetText = $("#tweetText").val();
    var tweetText = $.trim(tweetText);

    if (tweetText != "") { //User already entered a tweet
      tweetText += " #CM0677"; //Append '#CM0667' to tweet message
      
      var tweetURL = "./server/twitter/twitter_login.php?tweetText=" + tweetText;
      window.open(tweetURL, "MsgWindow", "width=300, height=300");
    }
    else { //No tweet entered; Show error message
      alert("No tweet message entered!");
    }
  }); //End requestBtn click event

  /**
   * Function to post a tweet to Twitter
   */
  $("#tweetBtn").click(function () { 
    var tokenKey = $("#tokenKey").val(); //Obtain token key
    //Get tweet message again in case of any changes after requesting token
    var tweetText = $("#tweetText").val();
    var tweetText = $.trim(tweetText);

    if (tokenKey != "") { //Token key is entered
      if (tweetText != "") { //Tweet is entered
        tweetText += " #CM0677"; //Append '#CM0667' to tweet message
        var tweetURL = "./server/twitter/twitter_oauth.php";
        
        $.get(tweetURL, { oauth_verifier: tokenKey, tweetText: tweetText }, function (data) {
          alert("Tweet posted!");
          $("#tweetText").val(""); //Empty tweet textbox
          $("#tokenKey").val(""); //Empty token key
        });
      }
      else { //No tweet entered; Show error message
        alert("No tweet message entered!");
      }
    }
    else { //No token key entered; Show error message
      alert("Token key is required!");
    }
  }); //End tweetBtn click event
}); //End document.ready

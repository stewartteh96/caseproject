$(document).ready(function () {
  loadWeather();

  setInterval(function () { //Refresh weather forecast every hour (Note: 1000 ms = 1s)
    loadWeather();
  }, 1000 * 60 * 60);
});

/**
 * Function to load weather forecast 
 */
function loadWeather() { 
  var url = "http://api.openweathermap.org/data/2.5/forecast?lat=53.36652&lon=-2.29855&appid=1c907afc6625eb3d88df266e1a51d173";

  $.post(url, function (data) {
    // console.log(JSON.stringify(data, null, 2));
    var currDate = moment().format("YYYY-MM-DD");
    var parseCode = "<table id='tblWeather'><tr>";
    var found = false;
    var index = 0;

    for (var i = 0; i < data.list.length; i++) {
      var dateTime = data.list[i].dt_txt.split(" "); //Split date and time & store into array

      //Get forecasted weather's date (e.g. 2018-03-20) and day (e.g. Tuesday)
      var dateString = data.list[i].dt_txt;
      var dateObj = new Date(dateString);
      var momentObj = moment(dateObj);
      var forecastDate = momentObj.format('YYYY-MM-DD');
      var forecastDay = momentObj.format('dddd');
      index = -1; 

      if (dateTime[0] == currDate) { //Current day; Display any weather forecast at 00:00:00 (if available)
        if (!found) { //Check if weather forecast is already shown
          index = i;
          found = true; 
        }
      }
      else { //Subsequent days
        if (dateTime[1] == "00:00:00") { //Show only weather forecast at 00:00:00
          index = i;
        }
      }

      if (index != -1) { //(index != -1) == Skip this row
        parseCode += "<td>";
        parseCode += "<p style='font-weight:bold';>" + forecastDay + " - " + forecastDate + "</p><br/>";
        parseCode += "<p>Temperature: ";
        var celsius = data.list[index].main.temp - 272.15; //Kelvin to degree celsius conversion
        celsius = celsius.toFixed(2);
        parseCode += celsius + "&deg;C </p><br/>";
        parseCode += "<p>Weather: ";
        parseCode += data.list[index].weather[0].main + " - <br />" + data.list[index].weather[0].description + "</p><br/> ";
        parseCode += "<p>Humidity: ";
        parseCode += data.list[index].main.humidity + "% </p>";
        parseCode += "</td>";
      }
    }

    parseCode += "</tr></table>";
    $("#divWeather").append(parseCode);
  });
}

var map;
var geocoder;
var address;
var speakerSize = {};
var speakerColor = {};
speakerSize.small = 0.01;
speakerSize.middle = 0.05;
speakerSize.big = 0.32;
speakerSize.huge = 0.64;
speakerSize.now = speakerSize.big;
speakerColor.border = "#FF0000";
speakerColor.body = "#F8F500";
var speakerLevel = 1;
var shoutTo = [];
var lat = 36.917;
var lng = 105.397;
var place = null;


function initialize() {
    map = new GMap2(document.getElementById("map_canvas"), {
        draggableCursor: 'crosshair',
        draggingCursor: 'pointer'
    });
    map.setCenter(new GLatLng(36.917, 105.397), 4);
    map.addControl(new GLargeMapControl);
    GEvent.addListener(map, "click", getAddress);
    geocoder = new GClientGeocoder();
    setSpeakerSize(speakerSize.huge, 1, dojo.byId('speaker_huge'));
    load(('中国'), 0);
    drawCircle(36.917, 105.397, 10, speakerColor.border, 3, 0.75, speakerColor.body, .5);
}


/**
 * Add a circle to the global variable "map". This function won't work for circles that encompass
 * the North or South Pole. Also, there is a slight distortion in the upper-left, upper-right,
 * lower-left, and lower-right sections of the circle that worsens as it gets larger and/or closer
 * to a pole.
 * @param lat Latitude in degrees
 * @param lng Longitude in degrees
 * @param radius Radius of the circle in statute miles
 * @param {String} strokeColor Color of the circle outline in HTML hex style, e.g. "#FF0000"
 * @param strokeWidth Width of the circle outline in pixels
 * @param strokeOpacity Opacity of the circle outline between 0.0 and 1.0
 * @param {String} fillColor Color of the inside of the circle in HTML hex style, e.g. "#FF0000"
 * @param fillOpacity Opacity of the inside of the circle between 0.0 and 1.0
 */
function drawCircle(lat, lng, radius, strokeColor, strokeWidth, strokeOpacity, fillColor, fillOpacity) {
    map.clearOverlays();
    var d2r = Math.PI / 180;
    var r2d = 180 / Math.PI;
    var Clat = radius * speakerSize.now; // Convert statute miles into degrees latitude
    var Clng = Clat / Math.cos(lat * d2r);
    var Cpoints = [];
    for (var i = 0; i < 33; i++) {
        var theta = Math.PI * (i / 16);
        Cy = lat + (Clat * Math.sin(theta));
        Cx = lng + (Clng * Math.cos(theta));
        var P = new GPoint(Cx, Cy);
        Cpoints.push(P);
    }
    map.setCenter(new GLatLng(lat, lng), speakerLevel * 2 + 1);
    var polygon = new GPolygon(Cpoints, strokeColor, strokeWidth, strokeOpacity, fillColor, fillOpacity);
    map.addOverlay(polygon);
}


function getAddress(overlay, latlng) {
    if (latlng != null) {
        address = latlng;
        geocoder.getLocations(latlng, getRegionName);
    }
}

function getRegionName(response) {
    if (!response || response.Status.code != 200) {
        if (place) {
            _parsePlace(place);
        } else {
            drawCircle(lat, lng, 10, speakerColor.border, 3, 0.75, speakerColor.body, .5);
        }
    } else {
        place = response.Placemark[0];
        lat = place.Point.coordinates[1];
        lng = place.Point.coordinates[0];
        _parsePlace(place);
    }
}

function _parsePlace(place) {
    drawCircle(place.Point.coordinates[1], place.Point.coordinates[0], 10, speakerColor.border, 3, 0.75, speakerColor.body, .5);
    //console.log(place);
    var level = speakerLevel;
    var locality = null;
    shoutTo = [];
    shoutTo.push(place.AddressDetails.Country.CountryName);
    level--;
    
    if (level > 0) {
        if (place.AddressDetails.Country.AdministrativeArea) {
            shoutTo.push(place.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName);
        }
        level--;
    }
    
    if (level > 0) {
        if (place.AddressDetails.Country.AdministrativeArea) {
            shoutTo.push(place.AddressDetails.Country.AdministrativeArea.Locality.LocalityName);
            locality = place.AddressDetails.Country.AdministrativeArea.Locality;
        } else if (place.AddressDetails.Country.Locality) {
            shoutTo.push(place.AddressDetails.Country.Locality.LocalityName);
            locality = place.AddressDetails.Country.Locality;
        }
        level--;
    }
    
    if (level > 0) {
        if (locality) {
            shoutTo.push(locality.DependentLocality.DependentLocalityName);
        }
    }
    
    dojo.byId('shout_to').value = shoutTo.join(',');
    load(shoutTo.join(','), 0);
}

function setSpeakerSize(size, level, e) {
    speakerSize.now = size;
    speakerLevel = level;
    getRegionName(null);
    dojo.forEach(dojo.query("#speaker_sizer a"), function(item, idx, arr) {
        dojo.removeClass(item, 'selected_size');
    });
    dojo.addClass(e, 'selected_size');
}

function submitForm() {
    dojo.xhrPost({
        // The page that parses the POST request
        url: 'http://dev.360quan.com/trumpet/save/',
        // Name of the Form we want to submit
        form: 'speaker_form',
        // Loads this function if everything went ok
        load: function(data) {
            // Put the data into the appropriate <div>
            load(dojo.byId('shout_to').value, 0);
        },
        // Call this function if an error happened
        error: function(error) {
            console.error('Error: ', error);
        }
    });
}

function load(regions, type) {
    dojo.xhrGet({
        url: 'http://dev.360quan.com/trumpet/load/' + regions + '/' + type + '/',
        load: function(data) {
            // Put the data into the appropriate <div>
            display(data);
        },
        // Call this function if an error happened
        error: function(error) {
            console.error('Error: ', error);
        }
    });
}

function display(json) {
    dojo.byId('displayUL').innerHTML = '';
    dojo.forEach(dojo.fromJson(json), function(item, idx, arr){
        dojo.byId('displayUL').innerHTML += '<li>'
        + '<span>' + item.author + ' post to: '
        + '<a href="#" onclick="load(\'' + item.regions + '\', 1)">' + item.regions + '</a> at '
        + item.time + '</span>'
        + '<p>' + item.content + '</p>'
        + '</li>';
        //console.log(item); 
    });
}

function showAddress(response) {
    map.clearOverlays();
    if (!response || response.Status.code != 200) {
        alert("Status Code:" + response.Status.code);
    } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]);
        marker = new GMarker(point);
        map.addOverlay(marker);
        marker.openInfoWindowHtml('<b>orig latlng:</b>' + response.name + '<br/>' +
        '<b>latlng:</b>' +
        place.Point.coordinates[0] +
        "," +
        place.Point.coordinates[1] +
        '<br>' +
        '<b>Status Code:</b>' +
        response.Status.code +
        '<br>' +
        '<b>Status Request:</b>' +
        response.Status.request +
        '<br>' +
        '<b>Address:</b>' +
        place.address +
        '<br>' +
        '<b>Accuracy:</b>' +
        place.AddressDetails.Accuracy +
        '<br>' +
        '<b>Country code:</b> ' +
        place.AddressDetails.Country.CountryNameCode);
    }
}

function parseMessage(json) {
    var msgs = dojo.fromJson(json);
    dojo.forEach(dojo.fromJson(json), function(msg) {
        console.log(msg);
        var el = new Element('div');
        var author = new Element('h3', {
            'html': msg.author
        }).inject(el);
        var regions = new Element('span', {
            'html': msg.regions
        }).inject(el);
        var content = new Element('p', {
            'html': msg.content
        }).inject(author, 'after');
        //var footer = new Element('span').inject(img, 'after');
        el.inject(messages);
    });
    //$('log').set('html', msg.toString());
}

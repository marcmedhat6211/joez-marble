$(document).ready(function() {
    $("#contact_form").validate();
});

function initMap() {
    // The location of Uluru
    const location = { lat: 31.079699655933762, lng: 29.847133697578183 };
    // The map, centered at location
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: location,
        disableDefaultUI: true,
        mapTypeControl: false,
        streetViewControl: false,
        styles: [{
                "featureType": "all",
                "elementType": "labels.text",
                "stylers": [{
                    "color": "#878787"
                }]
            },
            {
                "featureType": "all",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "off"
                }]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{
                    "color": "#f9f5ed"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [{
                    "color": "#f5f5f5"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#c9c9c9"
                }]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [{
                    "color": "#aee0f4"
                }]
            }
        ]
    });
    // The marker, positioned at location
    const marker = new google.maps.Marker({
        position: location,
        map: map,
    });
}
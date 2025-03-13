/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
// [START maps_add_map]
// Initialize and add the map

let functions = {};
function log(...args) {
    let date = `[${Date.now()}]`;

    console.log(date, ...args);
}

const { Map } = await google.maps.importLibrary("maps");
const { Autocomplete, PlacesService, RankBy } = await google.maps.importLibrary("places");
const { Geocoder } = await google.maps.importLibrary("geocoding");
const { spherical } = await google.maps.importLibrary("geometry");
const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary(
    "marker",
);
let map;

functions.initMap = async function () {

    const position = { lat: 13.2855079, lng: 100.9246009 };
    document.getElementById("latlng").innerText = `${position.lat}, ${position.lng}`;
    map = new Map(document.getElementById("map"), {
        zoom: 17,
        center: position,
        mapId: "DEMO_MAP_ID",
    });
    const input = document.getElementById("pac-input");
    const autocomplete = new Autocomplete(input, {
        fields: ["place_id", "geometry", "formatted_address", "name"],
    });
    autocomplete.bindTo("bounds", map);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    const infowindow = new google.maps.InfoWindow();
    const infowindowContent = document.getElementById("infowindow-content");

    infowindow.setContent(infowindowContent);

    const pinBackground = new PinElement({
        glyph: "🏠",
        glyphColor: "white",
        background: "#71BBFF",
        scale: 1.5
    });
    const marker = new google.maps.marker.AdvancedMarkerElement({
        position: position,
        map: map,
        content: pinBackground.element,
        gmpDraggable: true,
    });

    let tempMarker = [];
    marker.addListener("dragend", async (event) => {
        log("dragend", event);
        const position = marker.position;
        let nearbyPlaces = await functions.getNearbyPlaces(position, document.getElementById("keyword").value, document.getElementById("distance").value);
        tempMarker.forEach(marker => {
            marker.removeMarker();
        });
        tempMarker = [];
        nearbyPlaces.forEach(async (place, index) => {
            let marker = functions.placeMarker(place.geometry.location, map, index.toString(), 1, "#71BBFF");
            tempMarker.push(marker);
        });

        // let places = await functions.getPlaces(position);
        // // let nearby = functions.getNearbyLocations(position);
        // console.log(places)
        // places.forEach(async (place, index) => {
        //     let placeDetail = await functions.getPlaceDetail(place.place_id);
        //     log('place', index, placeDetail);
        // });

        // get the place details for the new position
        // let service = new PlacesService(map);
        // RankBy.PROMINENCE
        // RankBy.DISTANCE
        // service.nearbySearch({ location: position, radius: 100, RankBy: RankBy.DISTANCE }, (results, status) => {
        //     log('nearbySearch', results, status);
        //     if (status !== "OK") {
        //         window.alert("PlacesService failed due to: " + status);
        //         return;
        //     }
        //     let result = results[1];
        //     if (result) {
        //         let placeId = result.place_id;
        //         // let service = new PlacesService(map);
        //         log('getDetails', result);

        //         marker.position = result.geometry.location;
        //         infowindowContent.children.namedItem("place-name").textContent = result.name;
        //         infowindowContent.children.namedItem("place-id").textContent =
        //             result.place_id;
        //         infowindowContent.children.namedItem("place-address").textContent =
        //             result.formatted_address;
        //         infowindow.open(map, marker);
        //     } else {
        //         window.alert("No results found");
        //         return;
        //     }
        // });
    });
    //     const geocoder = new Geocoder();
    //     geocoder.geocode({ location: position.toJSON() }, (results, status) => {
    //         if (status !== "OK") {
    //             window.alert("Geocoder failed due to: " + status);
    //             return;
    //         }
    //         if (results[0]) {
    //             log('dragend_results', results);
    //             let placeId = results[0].place_id;
    //             // let service = new PlacesService(map);
    //             // service.getDetails({ placeId: placeId }, (results, status) => {
    //             //     log('getDetails', results, status);
    //             //     if (status !== "OK") {
    //             //         window.alert("PlacesService failed due to: " + status);
    //             //         return;
    //             //     }
    //             //     marker.position = results.geometry.location;
    //             //     infowindowContent.children.namedItem("place-name").textContent = results.name;
    //             //     infowindowContent.children.namedItem("place-id").textContent =
    //             //         results.place_id;
    //             //     infowindowContent.children.namedItem("place-address").textContent =
    //             //         results.formatted_address;
    //             //     infowindow.open(map, marker);
    //             // });
    //         } else {
    //             window.alert("No results found");
    //             return;
    //         }
    //     });
    // });

    map.addListener("click", (e) => {
        log("click", e);

        let pos = e.latLng;
        document.getElementById("latlng").innerText = `${pos.lat()}, ${pos.lng()}`;
        let dist = spherical.computeDistanceBetween(marker.position, pos);
        document.getElementById("distance").innerText = `${dist} m`;

        return
        if (e.placeId) {
            // get the place details for the place ID.
            const geocoder = new Geocoder();
            geocoder.geocode({ placeId: e.placeId }, (results, status) => {
                if (status !== "OK") {
                    window.alert("Geocoder failed due to: " + status);
                    return;
                }
                marker.position = e.latLng;
                map.setZoom(17);
                map.setCenter(e.latLng);
                infowindow.close();
                infowindowContent.children.namedItem("place-name").textContent = results[0].name;
                infowindowContent.children.namedItem("place-id").textContent =
                    results[0].place_id;
                infowindowContent.children.namedItem("place-address").textContent =
                    results[0].formatted_address;
                // infowindow.open(map);
            });
        }
    });

    marker.addListener("click", () => {
        log("click_marker", marker);
        infowindow.open(map, marker);
    });

    autocomplete.addListener("place_changed", () => {
        log("place_changed", autocomplete);
        infowindow.close();

        const place = autocomplete.getPlace();

        if (!place.geometry || !place.geometry.location) {
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        // Set the position of the marker using the place ID and location.
        // @ts-ignore This should be in @typings/googlemaps.
        // marker.setPlace({
        //     placeId: place.place_id,
        //     location: place.geometry.location,
        // });
        // marker.setPosition(place.geometry.location);
        marker.position = place.geometry.location
        // marker.setVisible(true);
        infowindowContent.children.namedItem("place-name").textContent = place.name;
        infowindowContent.children.namedItem("place-id").textContent =
            place.place_id;
        infowindowContent.children.namedItem("place-address").textContent =
            place.formatted_address + "\n" + place.geometry.location;
        infowindow.open(map, marker);
    });




    map.addListener("center_changed", () => {
        let pos = map.getCenter();
        document.getElementById("latlng").innerText = `${pos.lat()}, ${pos.lng()}`;
        // marker.position = pos;
    });
}

functions.initMap();

functions.getPlaces = async function (latlng) {
    return new Promise((resolve, reject) => {
        const geocoder = new Geocoder();
        geocoder.geocode({ location: latlng.toJSON() }, (results, status) => {
            if (status !== "OK") {
                window.alert("Geocoder failed due to: " + status);
                return;
            }
            log('places', results);
            return resolve(results);
        });
    });
}

functions.getNearbyPlaces = async function (latlng, keyword, distance = 100) {
    return new Promise((resolve, reject) => {
        let service = new PlacesService(map);
        service.nearbySearch({ location: latlng, keyword, radius: distance, RankBy: RankBy.DISTANCE }, (results, status) => {
            log('nearbySearch', results, status);
            if (status !== "OK") {
                window.alert("PlacesService failed due to: " + status);
                return;
            }
            return resolve(results);
        });
    });
}

functions.getPlaceDetail = async function (placeId) {
    return new Promise((resolve, reject) => {

        let service = new PlacesService(map);
        service.getDetails({ placeId: placeId }, (results, status) => {
            log('getDetails', results, status);
            if (status !== "OK") {
                window.alert("PlacesService failed due to: " + status);
                return;
            }
            return resolve(results);
        });
    });
}

functions.placeMarker = function (position, map, title, scale, color, draggable = false) {
    let pinBackground = new PinElement({
        glyph: title,
        glyphColor: "white",
        background: color,
        scale: scale
    });
    let marker = new google.maps.marker.AdvancedMarkerElement({
        position: position,
        map: map,
        content: pinBackground.element,
        gmpDraggable: draggable,
    });
    return {
        marker,
        hideMarker: () => {
            marker.map = null;
        },
        showMarker: () => {
            marker.map = map;
        },
        removeMarker: () => {
            marker.map = null;
            marker = null;
        }
    };
};



let mapMarkers = [];
// let markerPositions = [{ "lat": 13.2855079, "lng": 100.9246009 }, { "lat": 13.285048464741582, "lng": 100.92485839206545 }, { "lat": 13.284457062087077, "lng": 100.92437901103726 }]
// let markerPositions = [{ "lat": 13.287047839126954, "lng": 100.92403673180713 }, { "lat": 13.28455740922251, "lng": 100.9221798092837 }, { "lat": 13.283077295399822, "lng": 100.9270211423306 }, { "lat": 13.284791053922184, "lng": 100.92586712020223 }];
let markerPositions = [];


functions.initMarkers = function () {
    markerPositions.forEach(pos => {
        let marker = new google.maps.marker.AdvancedMarkerElement({
            position: pos,
            map: map,
        });
        mapMarkers.push(marker);
    });
}
functions.initMarkers();



functions.addMarker = function () {
    let pos = map.getCenter();
    markerPositions.push(pos);
    let marker = new google.maps.marker.AdvancedMarkerElement({
        position: pos,
        map: map,
    });
    mapMarkers.push(marker);
    document.getElementById("poslist").innerText = JSON.stringify(markerPositions);
}

functions.panToRandomMarker = function () {
    let pos = mapMarkers[Math.floor(Math.random() * mapMarkers.length)].position;
    map.panTo(pos);
}


window.functions = functions;

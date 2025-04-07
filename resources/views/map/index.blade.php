@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <style>
        #infowindow-content {
            display: none;
        }

        #map #infowindow-content {
            display: inline;
        }
    </style>
    <div class="flex flex-col gap-4 h-full">
        {{-- report card --}}
        {{-- <div class=" bg-white shadow-md rounded-lg p-6 flex flex-col gap-3 "> --}}
        <!--The div element for the map -->
        <div class="flex flex-row gap-4 justify-center items-center bg-gray-100 rounded-lg p-2 cursor-pointer"
            onclick="document.getElementById('pac-input').focus()">
            <input id="pac-input" class="flex-grow p-4 py-2" type="text" placeholder="ค้นหา" />
            <span class="icon-[material-symbols--search] text-4xl"></span>
        </div>
        <div class='flex flex-row gap-4 justify-center items-center'>
            <button onclick="window.functions.analyze()"
                class="bg-primary-light text-white px-4 py-2 rounded cursor-pointer flex-grow font-bold border border-gray-400">วิเคราะห์</button>
            <select id="distance" class="p-2 bg-gray-100 rounded-lg cursor-pointer">
                <option value="500">0.5 km</option>
                <option value="1000" selected>1.0 km</option>
                <option value="1500">1.5 km</option>
                <option value="2000">2.0 km</option>
            </select>
        </div>

        <div id="map" class="w-full h-96 rounded-xl shadow-md"></div>
        <div id="infowindow-content">
            <span id="place-name" class="title"></span><br />
            <strong>Place ID:</strong> <span id="place-id"></span><br />
            <span id="place-address"></span>
        </div>

        <div class=" bg-white shadow-md rounded-lg p-6 hidden flex-col gap-3" id='result'>
            <h3 class="text-2xl font-bold text-primary-dark">ผลวิเคราะห์</h3>
            <div id="resultAmount"></div>
            <div id="loading" class="hidden justify-center items-center w-full">
                <span class="icon-[mdi--loading] text-4xl animate-spin"></span>
            </div>

            <div id="results" class="grid grid-cols-1 divide-y divide-gray-200">
            </div>

        </div>
    </div>

    {{-- </div> --}}
@endsection


@section('script')
    <script type="module">
        let functions = {};

        function log(...args) {
            let date = `[${Date.now()}]`;

            console.log(date, ...args);
        }

        const {
            Map
        } = await google.maps.importLibrary("maps");
        const {
            Autocomplete,
            PlacesService,
            RankBy
        } = await google.maps.importLibrary("places");
        const {
            Geocoder
        } = await google.maps.importLibrary("geocoding");
        const {
            spherical
        } = await google.maps.importLibrary("geometry");
        const {
            AdvancedMarkerElement,
            PinElement
        } = await google.maps.importLibrary("marker");
        let map;
        let Markers = [];
        let MapMarker = null;
        let Circles = [];

        function removeAllMarkers() {
            Markers.forEach(marker => {
                marker.removeMarker();
            });
            Markers = [];
        }

        function removeAllCircles() {
            Circles.forEach(circle => {
                circle.removeCircle();
            });
            Circles = [];
        }

        function removeAll() {
            removeAllMarkers();
            removeAllCircles();
        }
        const infowindow = new google.maps.InfoWindow();
        const infowindowContent = document.getElementById("infowindow-content");

        infowindow.setContent(infowindowContent);

        const infoWindow = {
            open: function(map, marker) {
                infowindow.open({
                    anchor: marker,
                    map,
                    shouldFocus: false
                });
            },
            close: function() {
                infowindow.close();
            },
            setContent: function({
                name,
                placeId,
                address
            }) {
                infowindowContent.children.namedItem("place-name").textContent = name;
                infowindowContent.children.namedItem("place-id").textContent = placeId;
                infowindowContent.children.namedItem("place-address").textContent = address;
            },
        }

        functions.initMap = async function() {
            const position = {
                lat: 13.2855079,
                lng: 100.9246009
            };
            map = new Map(document.getElementById("map"), {
                zoom: 15,
                center: position,
                mapId: "DEMO_MAP_ID",
            });
            const input = document.getElementById("pac-input");
            const autocomplete = new Autocomplete(input, {
                fields: ["place_id", "geometry", "formatted_address", "name"],
            });
            autocomplete.bindTo("bounds", map);
            // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            const pinBackground = new PinElement({
                glyph: "⭐",
                glyphColor: "white",
                scale: 1.5
            });
            MapMarker = new google.maps.marker.AdvancedMarkerElement({
                position: position,
                map: map,
                content: pinBackground.element,
                gmpDraggable: true,
            });
            let circle = functions.drawCircle({
                position: MapMarker.position,
                map: map,
                radius: parseFloat(document.getElementById("distance").value),
                strokeColor: "#000E87",
                fillColor: "#7BBBE9",
                opacity: 0.35,
                fillOpacity: 0.35,
                weight: 2
            });
            // Update circle's center while dragging the marker
            MapMarker.addListener("drag", (event) => {
                const newPosition = MapMarker.position;
                circle.circle.setCenter(newPosition);
            });

            // Optional: Perform actions after dragging ends
            MapMarker.addListener("dragend", (event) => {
                const finalPosition = MapMarker.position;
                // Additional actions can be performed here
            });

            document.getElementById("distance").addEventListener("change", (e) => {
                let dist = parseFloat(e.target.value);
                circle.circle.setRadius(dist);
            });



            map.addListener("click", (e) => {
                log("click", e);

                let pos = e.latLng;
                let dist = spherical.computeDistanceBetween(MapMarker.position, pos);

                return
                if (e.placeId) {
                    // get the place details for the place ID.
                    const geocoder = new Geocoder();
                    geocoder.geocode({
                        placeId: e.placeId
                    }, (results, status) => {
                        if (status !== "OK") {
                            window.alert("Geocoder failed due to: " + status);
                            return;
                        }
                        MapMarker.position = e.latLng;
                        map.setZoom(17);
                        map.setCenter(e.latLng);
                        infoWindow.close();
                        infoWindow.setContent({
                            name: results[0].name,
                            placeId: results[0].place_id,
                            address: results[0].formatted_address
                        });
                        infoWindow.open(map, MapMarker);
                    });
                }
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
                    map.setZoom(16);
                }

                // Set the position of the marker using the place ID and location.
                // @ts-ignore This should be in @typings/googlemaps.
                // marker.setPlace({
                //     placeId: place.place_id,
                //     location: place.geometry.location,
                // });
                // marker.setPosition(place.geometry.location);
                MapMarker.position = place.geometry.location
                circle.circle.setCenter(place.geometry.location);
                // marker.setVisible(true);
                infowindowContent.children.namedItem("place-name").textContent = place.name;
                infowindowContent.children.namedItem("place-id").textContent =
                    place.place_id;
                infowindowContent.children.namedItem("place-address").textContent =
                    place.formatted_address + "\n" + place.geometry.location;
                infowindow.open(map, MapMarker);
            });




            map.addListener("center_changed", () => {
                let pos = map.getCenter();
                // marker.position = pos;
            });
        }


        functions.getPlaces = async function(latlng) {
            return new Promise((resolve, reject) => {
                const geocoder = new Geocoder();
                geocoder.geocode({
                    location: latlng.toJSON()
                }, (results, status) => {
                    if (status !== "OK") {
                        window.alert("Geocoder failed due to: " + status);
                        return;
                    }
                    log('places', results);
                    return resolve(results);
                });
            });
        }

        functions.getNearbyPlaces = async function(latlng, keyword, distance = 100) {
            return new Promise((resolve, reject) => {
                let service = new PlacesService(map);
                service.nearbySearch({
                    location: latlng,
                    keyword,
                    radius: distance,
                    RankBy: RankBy.DISTANCE
                }, (results, status) => {
                    log('nearbySearch', results, status);
                    log('config', latlng, keyword, distance);
                    if (status !== "OK") {
                        // window.alert("PlacesService failed due to: " + status);
                        return resolve([]);
                    }
                    return resolve(results);
                });
            });
        }

        functions.getPlaceDetail = async function(placeId) {
            return new Promise((resolve, reject) => {

                let service = new PlacesService(map);
                service.getDetails({
                    placeId: placeId
                }, (results, status) => {
                    log('getDetails', results, status);
                    if (status !== "OK") {
                        window.alert("PlacesService failed due to: " + status);
                        return;
                    }
                    return resolve(results);
                });
            });
        }

        functions.placeMarker = function({
            position,
            map,
            title,
            scale,
            color,
            draggable = false
        }) {
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
                gmpDraggable: draggable || false,
            });

            marker.addListener("gmp-click", () => {
                log("click_marker", marker);
                infoWindow.close();
                infoWindow.setContent({
                    name: title,
                    placeId: marker.placeId,
                    address: marker.position
                });
                infoWindow.open(map, marker);
            });

            let markerObject = {
                marker,
                position: marker.position,
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

            Markers.push(markerObject);
            return markerObject;
        };

        functions.drawCircle = function({
            position,
            map,
            radius,
            strokeColor,
            fillColor,
            opacity = 0.35,
            fillOpacity = 0.35,
            weight = 2
        }) {
            let circle = new google.maps.Circle({
                strokeColor: strokeColor,
                strokeOpacity: opacity,
                strokeWeight: weight,
                fillColor: fillColor,
                fillOpacity: fillOpacity,
                map: map,
                center: position,
                radius: radius
            });

            let circleObject = {
                circle,
                position: circle.getCenter(),
                hideCircle: () => {
                    circle.setMap(null);
                },
                showCircle: () => {
                    circle.setMap(map);
                },
                removeCircle: () => {
                    circle.setMap(null);
                    circle = null;
                }
            };
            Circles.push(circleObject);
            return circleObject;
        }

        functions.panToRandomMarker = function() {
            let pos = Markers[Math.floor(Math.random() * Markers.length)].position;
            map.panTo(pos);
        }

        functions.analyze = async function() {
            let Places = [];
            let pos = MapMarker.position;

            // clear previous results
            document.getElementById("resultAmount").innerText = "";
            document.getElementById("results").innerHTML = "";
            removeAllMarkers();

            // Show loading spinner
            document.getElementById("loading").classList.remove("hidden");
            document.getElementById("loading").classList.add("flex");

            // show result 
            document.getElementById("result").classList.remove("hidden");
            document.getElementById("result").classList.add("flex");

            // scroll to result 
            document.getElementById("result").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

            const radius = parseFloat(document.getElementById("distance").value);

            try {
                let nearbyPlaces = await functions.getNearbyPlaces(
                    pos,
                    'ส่งของ',
                    radius
                );
                log("nearbyPlaces", nearbyPlaces);
                removeAllMarkers();
                nearbyPlaces.forEach(async (place, index) => {
                    let gMapEmoji = '🗺️';
                    let formatted = {
                        "poi_name": place.name,
                        "poi_gps_lat": place.geometry.location.lat(),
                        "poi_gps_lng": place.geometry.location.lng(),
                        "poi_type": 'Google Map: ' + place.types.join(", "),
                        "poit_name": place.types[0],
                        "poi_distance": spherical.computeDistanceBetween(pos, place.geometry
                            .location),
                        "poit_icon": gMapEmoji,
                    }
                    Places.push(formatted);
                });

                // Fetch additional data and update the UI
                let response = await fetch('{{ route('api.map.get') }}?' + new URLSearchParams({
                    lat: pos.lat,
                    lng: pos.lng,
                    radius: document.getElementById("distance").value,
                    limit: 5
                }).toString());
                let data = await response.json();

                Places = [...Places, ...data.data];
                // sort by distance
                Places.sort((a, b) => {
                    return a.poi_distance - b.poi_distance;
                });
                // filter out places outside of the radius
                Places = Places.filter((place) => {
                    return place.poi_distance <= radius;
                });
                document.getElementById("resultAmount").innerText = `ผลลัพธ์ ${Places.length}`;
                document.getElementById("results").innerHTML = "";
                Places.forEach((place, index) => {
                    let div = document.createElement("div");
                    div.className = "flex flex-row gap-2 py-4";
                    div.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center">
                            <div class="text-6xl">
                                ${place.poit_icon}
                            </div>
                            <div>${index + 1}.</div>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <div id="place-name" class="text-primary-light font-bold text-sm">${place.poi_name}</div>
                            <div class="text-xs text-gray-400">${place.poit_name}</div>
                            <div class="text-xs text-gray-400">${place.poi_distance.toFixed(2)} M</div>
                            <div class="text-xs text-gray-400">${place.poi_gps_lat}, ${place.poi_gps_lng}</div>
                        </div>
                    `;
                    document.getElementById("results").appendChild(div);
                });
                log("Places", Places);
                Places.forEach((place, index) => {
                    functions.placeMarker({
                        position: {
                            lat: place.poi_gps_lat,
                            lng: place.poi_gps_lng
                        },
                        map: map,
                        title: `${index + 1}`,
                        scale: 1,
                        color: place.poit_color ? place.poit_color : "red",
                        draggable: false
                    });
                });
            } catch (error) {
                console.error('Error fetching data:', error);
            } finally {
                // Hide loading spinner once data is loaded
                document.getElementById("loading").classList.remove("flex");
                document.getElementById("loading").classList.add("hidden");
            }
        }


        functions.initMap();
        window.functions = functions;
    </script>
    <!-- prettier-ignore -->
    <script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>
@endsection

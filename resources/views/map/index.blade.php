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
        <div class=" bg-white shadow-md rounded-lg p-6 flex flex-col gap-3 ">
            <h3></h3>
            <p>my location: <span id="latlng"> </span></p>
            <p><span id="distanceFromMarker"> </span></p>

            <button onclick="window.functions.analyze()" class="bg-blue-500 text-white px-4 py-2 rounded">วิเคราะห์</button>
            <input id="keyword" type="text" placeholder="Enter a keyword" value="ส่งของ" />
            <input id="distance" type="number" placeholder="Enter a search distance" value="500" />
            <p>Marker Locations: <span id="poslist"></span></p>

            <!--The div element for the map -->
            <input id="pac-input" class="bg-white border-2 shadow w-full focus:border-amber-500" type="text"
                placeholder="Enter a location" />
            <div id="map" class="w-full h-96 rounded-xl"></div>
            <div id="infowindow-content">
                <span id="place-name" class="title"></span><br />
                <strong>Place ID:</strong> <span id="place-id"></span><br />
                <span id="place-address"></span>
            </div>
        </div>
        <div class=" bg-white shadow-md rounded-lg p-6 flex flex-col gap-3">
            <h3>Nearby Places</h3>
            <div id="resultAmount"></div>
            <div id="loading" class="hidden justify-center items-center w-full">
                <span class="icon-[mdi--loading] text-4xl animate-spin"></span>
            </div>

            <div id="results" class="flex flex-col gap-2">

            </div>
        </div>
    </div>
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

        function removeAllMarkers() {
            Markers.forEach(marker => {
                marker.removeMarker();
            });
            Markers = [];
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

            MapMarker.addListener("dragend", async (event) => {
                const position = MapMarker.position;
                let nearbyPlaces = await functions.getNearbyPlaces(
                    position,
                    document.getElementById("keyword").value,
                    document.getElementById("distance").value
                );
                removeAllMarkers();
                nearbyPlaces.forEach(async (place, index) => {
                    functions.placeMarker({
                        position: place.geometry.location,
                        map: map,
                        title: `${index + 1}`,
                        scale: 1,
                        color: "red",
                        draggable: false
                    });
                });
            });

            map.addListener("click", (e) => {
                log("click", e);

                let pos = e.latLng;
                document.getElementById("latlng").innerText = `${pos.lat()}, ${pos.lng()}`;
                let dist = spherical.computeDistanceBetween(MapMarker.position, pos);
                document.getElementById("distanceFromMarker").innerText = `${dist} m`;

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
                    map.setZoom(17);
                }

                // Set the position of the marker using the place ID and location.
                // @ts-ignore This should be in @typings/googlemaps.
                // marker.setPlace({
                //     placeId: place.place_id,
                //     location: place.geometry.location,
                // });
                // marker.setPosition(place.geometry.location);
                MapMarker.position = place.geometry.location
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
                document.getElementById("latlng").innerText = `${pos.lat()}, ${pos.lng()}`;
                // marker.position = pos;
            });
        }

        functions.initMap();

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
                        window.alert("PlacesService failed due to: " + status);
                        return;
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


            try {
                let nearbyPlaces = await functions.getNearbyPlaces(
                    pos,
                    document.getElementById("keyword").value,
                    document.getElementById("distance").value
                );
                removeAllMarkers();
                nearbyPlaces.forEach(async (place, index) => {
                    log("nearbyPlaces", place);
                    let gMapEmoji = '🗺️';
                    let formatted = {
                        "poi_name": place.name,
                        "poi_gps_lat": place.geometry.location.lat(),
                        "poi_gps_lng": place.geometry.location.lng(),
                        "poi_type": 'Google Map: ' + place.types.join(", "),
                        "poi_distance": spherical.computeDistanceBetween(pos, place.geometry.location),
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
                document.getElementById("resultAmount").innerText = `Found ${Places.length} results`;
                document.getElementById("results").innerHTML = "";
                Places.forEach((place, index) => {
                    let div = document.createElement("div");
                    div.className = "flex flex-row gap-2";
                    div.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center">
                            <div class="text-6xl">
                                ${place.poit_icon}
                            </div>
                            <div>${index + 1}.</div>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <div id="place-name" class="text-primary-light font-bold">${place.poi_name}</div>
                            <div class="">${place.poi_type}</div>
                            <div class="">${place.poi_distance.toFixed(2)} M</div>
                            <div class="">${place.poi_gps_lat}, ${place.poi_gps_lng}</div>
                        </div>
                    `;
                    document.getElementById("results").appendChild(div);
                });
                document.getElementById("poslist").innerText = Places.map(p =>
                    `${p.poi_name} (${p.poi_gps_lat}, ${p.poi_gps_lng})`).join(", ");
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


        window.functions = functions;
    </script>
    <!-- prettier-ignore -->
    <script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>
@endsection

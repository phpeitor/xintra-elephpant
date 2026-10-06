(function () {
    "use strict";

    const mapElement = document.getElementById("profile-location-map");
    if (!mapElement) return;

    const ipElement = document.getElementById("public-ip");
    const statusElement = document.getElementById("profile-map-status");
    const values = {
        country: document.getElementById("profile-location-country"),
        region: document.getElementById("profile-location-region"),
        city: document.getElementById("profile-location-city"),
        timezone: document.getElementById("profile-location-timezone"),
        isp: document.getElementById("profile-location-isp"),
        coordinates: document.getElementById("profile-location-coordinates"),
    };

    const setStatus = (message) => {
        if (statusElement) statusElement.textContent = message;
    };

    fetch("controller/profile_location.php", { headers: { Accept: "application/json" } })
        .then((response) => {
            if (!response.ok) throw new Error("No se pudieron cargar los datos de ubicación.");
            return response.json();
        })
        .then((result) => {
            if (!result.ok || !result.location) throw new Error(result.message || "No hay datos de ubicación.");

            const location = result.location;
            if (ipElement) ipElement.textContent = result.ip || "IP no disponible";
            if (values.country) values.country.textContent = [location.country, location.countryCode].filter(Boolean).join(" (") + (location.countryCode ? ")" : "") || "—";
            if (values.region) values.region.textContent = location.regionName || "—";
            if (values.city) values.city.textContent = location.city || "—";
            if (values.timezone) values.timezone.textContent = location.timezone || "—";
            if (values.isp) values.isp.textContent = location.isp || "—";
            if (values.coordinates) values.coordinates.textContent = `${location.lat}, ${location.lon}`;

            if (!window.L) throw new Error("No se pudo cargar el mapa.");
            const coordinates = [Number(location.lat), Number(location.lon)];
            const map = L.map(mapElement, { scrollWheelZoom: false }).setView(coordinates, 13);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            const popup = document.createElement("div");
            const place = document.createElement("strong");
            place.textContent = [location.city, location.regionName, location.country].filter(Boolean).join(", ") || "Ubicación aproximada";
            const coords = document.createElement("div");
            coords.textContent = `Lat: ${location.lat} · Lon: ${location.lon}`;
            popup.append(place, coords);
            L.marker(coordinates).addTo(map).bindPopup(popup).openPopup();
            setStatus("");
            window.setTimeout(() => map.invalidateSize(), 100);
        })
        .catch((error) => {
            if (ipElement) ipElement.textContent = "IP no disponible";
            setStatus(error.message || "No se pudieron cargar los datos de ubicación.");
        });
})();

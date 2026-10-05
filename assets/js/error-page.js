(function () {
    "use strict";

    const ipElement = document.getElementById("public-ip");
    if (!ipElement) return;

    fetch("controller/public_ip.php", { headers: { Accept: "application/json" } })
        .then((response) => {
            if (!response.ok) throw new Error("No se pudo obtener la IP.");
            return response.json();
        })
        .then((result) => {
            ipElement.textContent = result.ok && result.ip ? result.ip : "IP no disponible";
        })
        .catch(() => {
            ipElement.textContent = "IP no disponible";
        });
})();

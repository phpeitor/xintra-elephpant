(function () {
  "use strict";

  const tooltipId = "xintra-floating-tooltip";
  const tooltipAttr = "data-xintra-tooltip";
  const colorAttr = "data-xintra-tooltip-color";

  const formatMonth = (value) => {
    if (!value) return "-";
    const parts = String(value).split("-");
    if (parts.length !== 2) return String(value);

    const [yy, mm] = parts;
    const dateObj = new Date(Number(yy), Number(mm) - 1, 1);
    const formatted = new Intl.DateTimeFormat("en-US", {
      month: "short",
      year: "2-digit",
    }).format(dateObj);

    return formatted.replace(/\./g, "").replace(/^./, (s) => s.toUpperCase());
  };

  const getTooltip = () => {
    let tooltip = document.getElementById(tooltipId);

    if (!tooltip) {
      tooltip = document.createElement("span");
      tooltip.id = tooltipId;
      tooltip.className = "hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:!bg-black rounded-sm";
      tooltip.setAttribute("role", "tooltip");
      tooltip.style.cssText = "position:fixed;z-index:99999;pointer-events:none;opacity:0;visibility:hidden;transition:opacity 150ms ease;";
      document.body.appendChild(tooltip);
    }

    return tooltip;
  };

  const hide = () => {
    const tooltip = getTooltip();
    tooltip.style.opacity = "0";
    tooltip.style.visibility = "hidden";
  };

  const show = (target) => {
    const text = target.getAttribute(tooltipAttr);
    if (!text) return;

    const tooltip = getTooltip();
    const color = target.getAttribute(colorAttr) || "#000";
    tooltip.textContent = text;
    tooltip.style.setProperty("background-color", color, "important");
    tooltip.style.opacity = "1";
    tooltip.style.visibility = "visible";

    const rect = target.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    const top = Math.max(8, rect.top - tooltipRect.height - 8);
    const left = Math.min(
      window.innerWidth - tooltipRect.width - 8,
      Math.max(8, rect.left + rect.width / 2 - tooltipRect.width / 2)
    );

    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;
  };

  const init = (container = document) => {
    container.querySelectorAll(`[${tooltipAttr}]:not([data-xintra-tooltip-ready])`).forEach((target) => {
      target.setAttribute("data-xintra-tooltip-ready", "true");
      if (!target.hasAttribute("tabindex")) target.setAttribute("tabindex", "0");
      target.classList.add("cursor-help");
      target.addEventListener("mouseenter", () => show(target));
      target.addEventListener("focus", () => show(target));
      target.addEventListener("mouseleave", hide);
      target.addEventListener("blur", hide);
    });
  };

  const escapeAttr = (value) => String(value).replace(/"/g, "&quot;");

  const attr = (text, color = "") => {
    const colorValue = color ? ` ${colorAttr}="${escapeAttr(color)}"` : "";
    return `${tooltipAttr}="${escapeAttr(text)}"${colorValue}`;
  };

  window.XintraTooltip = {
    attr,
    formatMonth,
    hide,
    init,
    show,
  };
})();

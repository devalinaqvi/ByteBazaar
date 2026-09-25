"use strict";
const appBase = document.querySelector('meta[name="app-base"]')?.content || "";
let toastTimer;
function notify(message, showBag = false) {
  const toast = document.getElementById("toast");
  if (!toast) return;
  toast.replaceChildren(document.createTextNode(message));
  if (showBag) {
    const link = document.createElement("a");
    link.href = `${appBase}/cart`;
    link.textContent = "View bag →";
    toast.append(link);
  }
  toast.hidden = false;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.hidden = true;
  }, 6500);
}
document.addEventListener("submit", async (event) => {
  const form = event.target.closest("form[data-async]");
  if (!form) return;
  event.preventDefault();
  if (form.dataset.busy === "true") return;
  if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) return;
  const errorBox = form.querySelector(".form-error");
  if (errorBox) {
    errorBox.hidden = true;
    errorBox.textContent = "";
  }
  const payload = new FormData(form);
  const buttons = [...form.querySelectorAll('button[type="submit"]')];
  const labels = buttons.map((button) => button.textContent);
  form.dataset.busy = "true";
  form.setAttribute("aria-busy", "true");
  buttons.forEach((button) => {
    button.disabled = true;
    button.textContent = button.classList.contains("add-button")
      ? "…"
      : "Working…";
  });
  try {
    const response = await fetch(form.action, {
      method: "POST",
      body: payload,
      credentials: "same-origin",
      headers: { Accept: "application/json" },
    });
    let result;
    try {
      result = await response.json();
    } catch {
      throw new Error("We couldn’t complete that request. Please try again.");
    }
    if (!response.ok || !result.success)
      throw new Error(
        result.message || "Please check your details and try again.",
      );
    if (result.redirect) {
      const target = new URL(result.redirect, window.location.origin);
      if (target.origin === window.location.origin) {
        window.location.assign(target.href);
        return;
      }
    }
    if (form.hasAttribute("data-reload")) {
      window.location.reload();
      return;
    }
    if (result.cart_count !== undefined)
      document.querySelectorAll("[data-cart-count]").forEach((node) => {
        node.textContent = String(result.cart_count);
      });
    notify(result.message || "Saved.", result.cart_count !== undefined);
  } catch (error) {
    const message =
      error instanceof TypeError
        ? "Connection interrupted. Please try again. Your details are still here."
        : error.message;
    if (errorBox) {
      errorBox.textContent = message;
      errorBox.hidden = false;
      errorBox.tabIndex = -1;
      errorBox.focus();
    } else notify(message);
  } finally {
    form.dataset.busy = "false";
    form.removeAttribute("aria-busy");
    buttons.forEach((button, index) => {
      button.disabled = false;
      button.textContent = labels[index];
    });
  }
});
document.addEventListener("click", (event) => {
  const toggle = event.target.closest("[data-password]");
  if (!toggle) return;
  const input = document.getElementById(toggle.dataset.password);
  const visible = input.type === "password";
  input.type = visible ? "text" : "password";
  toggle.textContent = visible ? "Hide" : "Show";
  toggle.setAttribute(
    "aria-label",
    visible ? "Hide password" : "Show password",
  );
});
document.querySelectorAll("[data-image-input]").forEach((input) => {
  let objectUrl;
  input.addEventListener("change", () => {
    input.setCustomValidity("");
    const file = input.files[0];
    if (!file) return;
    if (
      !["image/jpeg", "image/png", "image/webp"].includes(file.type) ||
      file.size > 2 * 1024 * 1024
    ) {
      input.setCustomValidity("Choose a JPEG, PNG, or WebP image up to 2 MB.");
      input.reportValidity();
      return;
    }
    const preview = input.closest("form").querySelector("[data-image-preview]");
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    if (preview) preview.src = objectUrl;
  });
});

// A restored tab must not display stale account details after login or logout.
window.addEventListener("pageshow", (event) => {
  if (event.persisted) window.location.reload();
});

// Templates stay local to this form; cycling does not submit or reload it.
document.querySelectorAll("[data-description-tools]").forEach((tools) => {
  const form = tools.closest("form");
  const description = form.querySelector('[name="description"]');
  const category = form.querySelector('[name="category_id"]');
  const templates = JSON.parse(tools.dataset.templates);
  const undo = tools.querySelector("[data-undo-description]");
  const status = form.querySelector("[data-description-status]");
  const history = [];
  const positions = new Map();
  tools.hidden = false;
  tools.querySelector("[data-rotate-description]").addEventListener("click", () => {
    if (form.dataset.busy === "true") return;
    const key = category.value || "general";
    const options = templates[key] || templates.general;
    let index = positions.get(key) ?? options.indexOf(description.value.trim());
    index = (index + 1) % options.length;
    if (options[index] === description.value.trim()) index = (index + 1) % options.length;
    history.push(description.value);
    description.value = options[index];
    positions.set(key, index);
    undo.disabled = false;
    description.dispatchEvent(new Event("input", { bubbles: true }));
    status.textContent = `Description ${index + 1} of ${options.length}. Review before saving. Use Undo to restore your previous text.`;
  });
  undo.addEventListener("click", () => {
    if (form.dataset.busy === "true" || !history.length) return;
    description.value = history.pop();
    undo.disabled = history.length === 0;
    description.dispatchEvent(new Event("input", { bubbles: true }));
    status.textContent = "Previous description restored.";
  });
});

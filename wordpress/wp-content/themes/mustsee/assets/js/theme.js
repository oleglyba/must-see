/* Must See Travel — front-end interactivity.
 * Vanilla JS (no jQuery). Each feature is a method on the MustSee namespace
 * object so the global scope stays clean and modules group logically. */
window.MustSee = (function () {
  "use strict";

  /* ---------- Mobile menu ---------- */
  const menu = {
    init() {
      const panel = document.querySelector("[data-menu]");
      if (!panel) return;
      const close = () => {
        panel.hidden = true;
        document.body.classList.remove("is-locked");
      };
      document.querySelectorAll("[data-menu-open]").forEach((btn) =>
        btn.addEventListener("click", () => {
          panel.hidden = false;
          document.body.classList.add("is-locked");
        })
      );
      panel.querySelectorAll("[data-menu-close]").forEach((b) => b.addEventListener("click", close));
      panel.querySelectorAll("a").forEach((a) => a.addEventListener("click", close));
    },
  };

  /* ---------- Generic tabs ----------
     [data-tabs] with [data-tab-btn="i"] buttons, optional <select data-tab-select>,
     and [data-tab-panel="i"] panels. Active/inactive classes come from
     data-tab-active / data-tab-inactive (space-separated). */
  const tabs = {
    init() {
      document.querySelectorAll("[data-tabs]").forEach((root) => this.bind(root));
    },
    bind(root) {
      const btns = root.querySelectorAll("[data-tab-btn]");
      const panels = root.querySelectorAll("[data-tab-panel]");
      const select = root.querySelector("[data-tab-select]");
      const active = (root.getAttribute("data-tab-active") || "").split(" ").filter(Boolean);
      const inactive = (root.getAttribute("data-tab-inactive") || "").split(" ").filter(Boolean);

      const activate = (index) => {
        btns.forEach((b) => {
          const on = b.getAttribute("data-tab-btn") === String(index);
          if (active.length || inactive.length) {
            active.forEach((c) => b.classList.toggle(c, on));
            inactive.forEach((c) => b.classList.toggle(c, !on));
          }
          b.setAttribute("aria-selected", on ? "true" : "false");
        });
        panels.forEach((p) => p.classList.toggle("hidden", p.getAttribute("data-tab-panel") !== String(index)));
        if (select && select.value !== String(index)) select.value = String(index);
      };

      btns.forEach((b) => b.addEventListener("click", () => activate(b.getAttribute("data-tab-btn"))));
      select?.addEventListener("change", () => activate(select.value));
      activate(0);
    },
  };

  /* ---------- Generic toggle ----------
     [data-toggle="<selector>"] toggles the "hidden" class on the target. */
  const toggles = {
    init() {
      document.querySelectorAll("[data-toggle]").forEach((btn) =>
        btn.addEventListener("click", () => {
          document.querySelector(btn.getAttribute("data-toggle"))?.classList.toggle("hidden");
        })
      );
    },
  };

  /* ---------- Header dropdowns (click + keyboard, alongside CSS hover) ---------- */
  const dropdown = {
    init() {
      const triggers = document.querySelectorAll("[data-dropdown]");
      if (!triggers.length) return;

      const closeAll = (except) => {
        document.querySelectorAll(".dropdown.is-open").forEach((dd) => {
          if (dd === except) return;
          dd.classList.remove("is-open");
          dd.querySelector("[data-dropdown]")?.setAttribute("aria-expanded", "false");
        });
      };

      triggers.forEach((t) => {
        t.addEventListener("click", (e) => {
          e.stopPropagation();
          const dd = t.closest(".dropdown");
          const open = dd.classList.toggle("is-open");
          t.setAttribute("aria-expanded", open ? "true" : "false");
          closeAll(dd);
        });
      });

      document.addEventListener("click", () => closeAll());
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeAll();
      });
    },
  };

  /* ---------- Booking flow ---------- */
  const booking = {
    init() {
      const root = document.querySelector("[data-booking]");
      if (!root) return;

      const panels = root.querySelectorAll("[data-step-panel]");
      const chips = root.querySelectorAll("[data-step-chip]");
      const prev = root.querySelector("[data-step-prev]");
      const next = root.querySelector("[data-step-next]");
      const submit = root.querySelector("[data-step-submit]");
      const seatCount = root.querySelector("[data-seat-count]");
      const formsWrap = root.querySelector("[data-tourist-forms]");
      const emptyMsg = root.querySelector("[data-tourist-empty]");
      const tpl = root.querySelector("[data-tourist-template]");
      const total = panels.length;

      let current = 0;
      const selected = [];

      const buildTouristForms = () => {
        if (!formsWrap || !tpl || current !== total - 1) return;
        if (selected.length === 0) {
          formsWrap.innerHTML = "";
          emptyMsg?.classList.remove("hidden");
          return;
        }
        emptyMsg?.classList.add("hidden");
        formsWrap.innerHTML = selected.map((s) => tpl.innerHTML.replace(/__SEAT__/g, s)).join("");
      };

      const render = () => {
        chips.forEach((c, i) => {
          c.classList.toggle("bg-brand", i === current);
          c.classList.toggle("text-white", i === current);
          c.classList.toggle("bg-gray-100", i !== current);
          c.classList.toggle("text-gray-500", i !== current);
        });
        panels.forEach((p, i) => p.classList.toggle("hidden", i !== current));
        if (prev) prev.disabled = current === 0;
        next?.classList.toggle("hidden", current >= total - 1);
        submit?.classList.toggle("hidden", current < total - 1);
        buildTouristForms();
      };

      prev?.addEventListener("click", () => {
        current = Math.max(0, current - 1);
        render();
      });
      next?.addEventListener("click", () => {
        current = Math.min(total - 1, current + 1);
        render();
      });

      /* Seat selection */
      root.querySelectorAll("[data-seat]").forEach((seat) => {
        if (seat.hasAttribute("disabled")) return;
        seat.addEventListener("click", () => {
          const n = seat.getAttribute("data-seat");
          const idx = selected.indexOf(n);
          if (idx >= 0) {
            selected.splice(idx, 1);
            seat.classList.remove("border-brand", "bg-brand", "text-white");
            seat.classList.add("border-gray-300", "bg-white", "text-gray-700");
          } else {
            selected.push(n);
            seat.classList.add("border-brand", "bg-brand", "text-white");
            seat.classList.remove("border-gray-300", "bg-white", "text-gray-700");
          }
          if (seatCount) seatCount.textContent = String(selected.length);
        });
      });

      /* Confirm -> POST the booking request to the WP AJAX stub.
         Guarded against double submit. */
      let submitting = false;
      submit?.addEventListener("click", async () => {
        if (submitting) return;
        submitting = true;
        submit.disabled = true;
        const msg = root.querySelector("[data-booking-msg]");
        const show = (text) => {
          if (msg) msg.textContent = text || "";
        };

        const tourists = Array.from(formsWrap?.querySelectorAll("input[type=text], input[type=tel], input[type=email], textarea") || [])
          .map((f) => f.value.trim())
          .filter(Boolean)
          .join(" | ");

        const data = new FormData();
        data.append("action", "mustsee_booking");
        data.append("nonce", window.MustSeeData?.nonce || "");
        data.append("tour", root.getAttribute("data-tour") || "");
        data.append("departure", root.querySelector("input[name=dep]:checked")?.value || "");
        data.append("room", root.querySelector("input[name=room]:checked")?.value || "");
        data.append("seats", selected.join(", "));
        data.append("tourists", tourists);

        try {
          const res = await fetch(window.MustSeeData?.ajax || "/wp-admin/admin-ajax.php", {
            method: "POST",
            credentials: "same-origin",
            body: data,
          });
          const json = await res.json();
          show(json?.message ?? "");
          if (json?.success) return; // keep the button disabled after success
        } catch {
          show("Сталася помилка. Спробуйте пізніше.");
        }
        submitting = false;
        submit.disabled = false;
      });

      render();
    },
  };

  /* ---------- AJAX forms (lead / newsletter) ---------- */
  const forms = {
    init() {
      document.querySelectorAll("[data-form]").forEach((form) => this.bind(form));
    },
    bind(form) {
      const action = form.getAttribute("data-form");
      const msg = form.querySelector("[data-form-msg]");
      const btn = form.querySelector("[type=submit]");
      const loadedAt = Date.now();
      let busy = false;

      const show = (text) => {
        if (msg) msg.textContent = text || "";
      };

      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (busy) return;
        busy = true;
        if (btn) btn.disabled = true;
        show("");

        try {
          const data = new FormData(form);
          data.append("action", "mustsee_" + action);
          data.append("nonce", window.MustSeeData?.nonce || "");
          data.append("elapsed", String(Date.now() - loadedAt));
          const res = await fetch(window.MustSeeData?.ajax || "/wp-admin/admin-ajax.php", {
            method: "POST",
            credentials: "same-origin",
            body: data,
          });
          const json = await res.json();
          show(json?.message ?? "");
          if (json?.success && json?.data?.redirect) {
            window.location.href = json.data.redirect;
            return;
          }
          if (json?.success) form.reset();
        } catch (err) {
          console.dir(err);
          show("Сталася помилка. Спробуйте ще раз.");
        } finally {
          busy = false;
          if (btn) btn.disabled = false;
        }
      });
    },
  };

  function init() {
    menu.init();
    tabs.init();
    toggles.init();
    dropdown.init();
    booking.init();
    forms.init();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  return { menu, tabs, toggles, dropdown, booking, forms, init };
})();

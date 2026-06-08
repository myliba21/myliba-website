(function () {
  const header = document.querySelector(".site-header");
  const toggle = document.querySelector(".nav-toggle");
  const nav = document.querySelector("#site-navigation");
  const languageSwitcher = document.querySelector(".language-switcher--dropdown");
  const languageTrigger = document.querySelector(".language-switcher__trigger");
  const megaItem = document.querySelector(".site-nav__item--mega");
  const megaToggle = megaItem ? megaItem.querySelector(".site-nav__link") : null;
  const isMobileNav = () => window.matchMedia("(max-width: 960px)").matches;

  if (header) {
    const syncHeader = () => {
      header.classList.toggle("is-scrolled", window.scrollY > 8);
    };

    syncHeader();
    window.addEventListener("scroll", syncHeader, { passive: true });
  }

  if (toggle && nav) {
    const closeMobileNav = () => {
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("nav-open");

      if (megaItem) {
        megaItem.classList.remove("is-mega-open");
      }

      if (megaToggle) {
        megaToggle.setAttribute("aria-expanded", "false");
      }
    };

    toggle.addEventListener("click", () => {
      const isOpen = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!isOpen));
      document.body.classList.toggle("nav-open", !isOpen);

      if (isOpen && megaItem) {
        megaItem.classList.remove("is-mega-open");
      }

      if (isOpen && megaToggle) {
        megaToggle.setAttribute("aria-expanded", "false");
      }
    });

    nav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", (event) => {
        if (link.classList.contains("site-nav__link") && link.closest(".site-nav__item--mega") && isMobileNav()) {
          event.preventDefault();
          const isMegaOpen = link.closest(".site-nav__item--mega").classList.toggle("is-mega-open");
          link.setAttribute("aria-expanded", String(isMegaOpen));
          return;
        }

        closeMobileNav();
      });
    });

    document.addEventListener("click", (event) => {
      if (!document.body.classList.contains("nav-open")) {
        return;
      }

      if (!nav.contains(event.target) && !toggle.contains(event.target)) {
        closeMobileNav();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeMobileNav();
      }
    });
  }

  if (languageSwitcher && languageTrigger) {
    languageTrigger.addEventListener("click", (event) => {
      event.preventDefault();
      languageSwitcher.classList.toggle("is-open");
    });

    document.addEventListener("click", (event) => {
      if (!languageSwitcher.contains(event.target)) {
        languageSwitcher.classList.remove("is-open");
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        languageSwitcher.classList.remove("is-open");
      }
    });
  }

  if (megaItem) {
    let closeTimer;
    const openMega = () => {
      if (isMobileNav()) {
        return;
      }

      window.clearTimeout(closeTimer);
      megaItem.classList.add("is-mega-open");
      if (megaToggle) {
        megaToggle.setAttribute("aria-expanded", "true");
      }
    };
    const queueCloseMega = () => {
      if (isMobileNav()) {
        return;
      }

      window.clearTimeout(closeTimer);
      closeTimer = window.setTimeout(() => {
        megaItem.classList.remove("is-mega-open");
        if (megaToggle) {
          megaToggle.setAttribute("aria-expanded", "false");
        }
      }, 260);
    };

    megaItem.addEventListener("mouseenter", openMega);
    megaItem.addEventListener("mouseleave", queueCloseMega);
    megaItem.addEventListener("focusin", openMega);
    megaItem.addEventListener("focusout", queueCloseMega);

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        megaItem.classList.remove("is-mega-open");
        if (megaToggle) {
          megaToggle.setAttribute("aria-expanded", "false");
        }
      }
    });
  }
})();

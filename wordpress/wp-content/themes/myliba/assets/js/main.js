(function () {
  const header = document.querySelector(".site-header");
  const toggle = document.querySelector(".nav-toggle");
  const nav = document.querySelector("#site-navigation");
  const languageSwitcher = document.querySelector(".language-switcher--dropdown");
  const languageTrigger = document.querySelector(".language-switcher__trigger");
  const megaItem = document.querySelector(".site-nav__item--mega");
  const megaToggle = megaItem ? megaItem.querySelector(".site-nav__link") : null;
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
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

  document.querySelectorAll(".site-promo").forEach((promo) => {
    const dismiss = promo.querySelector(".site-promo__dismiss");
    const storageKey = "myliba-promo-dismissed-" + (promo.dataset.sitePromo || "default");

    try {
      if (window.sessionStorage.getItem(storageKey) === "1") {
        promo.hidden = true;
        return;
      }
    } catch (error) {
      // Session storage can be unavailable in locked-down browsers.
    }

    if (dismiss) {
      dismiss.addEventListener("click", () => {
        promo.hidden = true;
        try {
          window.sessionStorage.setItem(storageKey, "1");
        } catch (error) {
          // Dismissal still works for the current render even without storage.
        }
      });
    }
  });

  const revealTargets = Array.from(document.querySelectorAll([
    ".section__heading",
    ".section--split > div",
    ".feature-card",
    ".module-card",
    ".testimonial-card",
    ".post-row",
    ".quick-start-stepper",
    ".quick-start-step",
    ".quick-start-section__cta",
  ].join(",")));

  if (revealTargets.length > 0 && !reducedMotion.matches && "IntersectionObserver" in window) {
    revealTargets.forEach((target, index) => {
      target.classList.add("reveal-on-scroll");
      target.style.setProperty("--reveal-delay", `${Math.min(index % 4, 3) * 70}ms`);
    });

    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    }, {
      rootMargin: "0px 0px -8% 0px",
      threshold: 0.16,
    });

    revealTargets.forEach((target) => revealObserver.observe(target));
  }

  document.querySelectorAll("[data-rotating-title]").forEach((rotator) => {
    const items = Array.from(rotator.querySelectorAll(".hero-title-rotator__item"));

    if (items.length < 2 || reducedMotion.matches) {
      return;
    }

    let activeIndex = items.findIndex((item) => item.classList.contains("is-active"));
    activeIndex = activeIndex >= 0 ? activeIndex : 0;

    window.setInterval(() => {
      items[activeIndex].classList.remove("is-active");
      activeIndex = (activeIndex + 1) % items.length;
      items[activeIndex].classList.add("is-active");
    }, 3600);
  });

  document.querySelectorAll("[data-hero-media-rotator]").forEach((rotator) => {
    const slides = Array.from(rotator.querySelectorAll("[data-hero-media-slide]"));
    const dots = Array.from(rotator.querySelectorAll("[data-hero-media-dot]"));

    if (slides.length < 2 || reducedMotion.matches) {
      return;
    }

    let activeIndex = slides.findIndex((slide) => slide.classList.contains("is-active"));
    activeIndex = activeIndex >= 0 ? activeIndex : 0;

    window.setInterval(() => {
      slides[activeIndex].classList.remove("is-active");
      if (dots[activeIndex]) {
        dots[activeIndex].classList.remove("is-active");
      }

      activeIndex = (activeIndex + 1) % slides.length;
      slides[activeIndex].classList.add("is-active");
      if (dots[activeIndex]) {
        dots[activeIndex].classList.add("is-active");
      }
    }, 4800);
  });
})();

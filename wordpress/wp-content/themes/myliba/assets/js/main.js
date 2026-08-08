(function () {
  const header = document.querySelector(".site-header");
  const toggle = document.querySelector(".nav-toggle");
  const nav = document.querySelector("#site-navigation");
  const languageSwitcher = document.querySelector(".language-switcher--dropdown");
  const languageTrigger = document.querySelector(".language-switcher__trigger");
  const megaItems = Array.from(document.querySelectorAll(".site-nav__item--mega"));
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const isMobileNav = () => window.matchMedia("(max-width: 1080px)").matches;
  const supportedLocales = ["tr", "en"];
  const setLocaleCookie = (locale) => {
    document.cookie = `myliba_locale=${locale}; path=/; max-age=31536000; samesite=lax`;
  };
  const setLocalePreference = (locale) => {
    if (!supportedLocales.includes(locale)) {
      return;
    }

    try {
      window.localStorage.setItem("myliba_locale", locale);
    } catch (error) {
      // Cookies remain as the fallback when localStorage is unavailable.
    }

    setLocaleCookie(locale);
  };

  const pathLocale = window.location.pathname
    .split("/")
    .filter(Boolean)
    .find((segment) => supportedLocales.includes(segment));

  if (pathLocale) {
    setLocalePreference(pathLocale);
  } else {
    try {
      const stored = window.localStorage.getItem("myliba_locale") || "";
      if (!supportedLocales.includes(stored)) {
        const browserLanguages = Array.isArray(navigator.languages) && navigator.languages.length
          ? navigator.languages
          : [navigator.language || ""];
        const isTurkish = browserLanguages.some((lang) => String(lang).toLowerCase().startsWith("tr"));
        const initialLocale = isTurkish ? "tr" : "en";
        setLocalePreference(initialLocale);
      }
    } catch (error) {}
  }

  document.querySelectorAll("[data-myliba-locale]").forEach((link) => {
    link.addEventListener("click", () => {
      setLocalePreference(link.dataset.mylibaLocale);
    });
  });

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

      megaItems.forEach((megaItem) => {
        megaItem.classList.remove("is-mega-open");
        megaItem.querySelector(".site-nav__link")?.setAttribute("aria-expanded", "false");
      });
    };

    toggle.addEventListener("click", () => {
      const isOpen = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!isOpen));
      document.body.classList.toggle("nav-open", !isOpen);

      if (isOpen) {
        megaItems.forEach((megaItem) => {
          megaItem.classList.remove("is-mega-open");
          megaItem.querySelector(".site-nav__link")?.setAttribute("aria-expanded", "false");
        });
      }
    });

    nav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", (event) => {
        if (link.classList.contains("site-nav__link") && link.closest(".site-nav__item--mega") && isMobileNav()) {
          event.preventDefault();
          const currentMegaItem = link.closest(".site-nav__item--mega");
          megaItems.forEach((megaItem) => {
            if (megaItem !== currentMegaItem) {
              megaItem.classList.remove("is-mega-open");
              megaItem.querySelector(".site-nav__link")?.setAttribute("aria-expanded", "false");
            }
          });
          const isMegaOpen = currentMegaItem.classList.toggle("is-mega-open");
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
    let langCloseTimer;

    const openLanguageSwitcher = () => {
      window.clearTimeout(langCloseTimer);
      languageSwitcher.classList.add("is-open");
    };

    const queueCloseLanguageSwitcher = () => {
      window.clearTimeout(langCloseTimer);
      langCloseTimer = window.setTimeout(() => {
        languageSwitcher.classList.remove("is-open");
      }, 220);
    };

    languageSwitcher.addEventListener("mouseenter", openLanguageSwitcher);
    languageSwitcher.addEventListener("mouseleave", queueCloseLanguageSwitcher);
    languageSwitcher.addEventListener("focusin", openLanguageSwitcher);
    languageSwitcher.addEventListener("focusout", queueCloseLanguageSwitcher);

    languageTrigger.addEventListener("click", (event) => {
      event.preventDefault();
      window.clearTimeout(langCloseTimer);
      languageSwitcher.classList.toggle("is-open");
    });

    document.addEventListener("click", (event) => {
      if (!languageSwitcher.contains(event.target)) {
        window.clearTimeout(langCloseTimer);
        languageSwitcher.classList.remove("is-open");
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        window.clearTimeout(langCloseTimer);
        languageSwitcher.classList.remove("is-open");
      }
    });
  }

  megaItems.forEach((megaItem) => {
    const megaToggle = megaItem.querySelector(".site-nav__link");
    let closeTimer;
    const openMega = () => {
      if (isMobileNav()) {
        return;
      }

      megaItems.forEach((otherMegaItem) => {
        if (otherMegaItem !== megaItem) {
          otherMegaItem.classList.remove("is-mega-open");
          otherMegaItem.querySelector(".site-nav__link")?.setAttribute("aria-expanded", "false");
        }
      });
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
  });

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

  document.querySelectorAll(".software-faq").forEach((faq) => {
    faq.querySelectorAll("details").forEach((item) => {
      item.addEventListener("toggle", () => {
        if (!item.open) {
          return;
        }

        faq.querySelectorAll("details[open]").forEach((openItem) => {
          if (openItem !== item) {
            openItem.removeAttribute("open");
          }
        });
      });
    });
  });

  document.querySelectorAll("[data-role-gains]").forEach((component) => {
    const tabs = Array.from(component.querySelectorAll("[data-role-tab]"));
    const panels = Array.from(component.querySelectorAll("[data-role-panel]"));

    if (tabs.length === 0 || panels.length === 0) {
      return;
    }

    const activateTab = (nextTab) => {
      tabs.forEach((tab) => {
        const isActive = tab === nextTab;
        tab.classList.toggle("is-active", isActive);
        tab.setAttribute("aria-selected", String(isActive));
      });

      panels.forEach((panel) => {
        const isActive = panel.id === nextTab.getAttribute("aria-controls");
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      });
    };

    tabs.forEach((tab, index) => {
      tab.addEventListener("click", () => activateTab(tab));
      tab.addEventListener("keydown", (event) => {
        if (!["ArrowDown", "ArrowRight", "ArrowUp", "ArrowLeft", "Home", "End"].includes(event.key)) {
          return;
        }

        event.preventDefault();
        let nextIndex = index;
        if (event.key === "ArrowDown" || event.key === "ArrowRight") {
          nextIndex = (index + 1) % tabs.length;
        } else if (event.key === "ArrowUp" || event.key === "ArrowLeft") {
          nextIndex = (index - 1 + tabs.length) % tabs.length;
        } else if (event.key === "Home") {
          nextIndex = 0;
        } else if (event.key === "End") {
          nextIndex = tabs.length - 1;
        }

        tabs[nextIndex].focus();
        activateTab(tabs[nextIndex]);
      });
    });
  });

  document.querySelectorAll("[data-home-tabs]").forEach((component) => {
    const tabs = Array.from(component.querySelectorAll("[data-home-tab]"));
    const panels = Array.from(component.querySelectorAll("[data-home-panel]"));

    if (tabs.length === 0 || panels.length === 0) {
      return;
    }

    const activate = (nextTab, moveFocus = false) => {
      tabs.forEach((tab) => {
        const isActive = tab === nextTab;
        tab.classList.toggle("is-active", isActive);
        tab.setAttribute("aria-selected", String(isActive));
        tab.tabIndex = isActive ? 0 : -1;
      });

      panels.forEach((panel) => {
        const isActive = panel.id === nextTab.getAttribute("aria-controls");
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      });

      if (moveFocus) {
        nextTab.focus();
      }
    };

    tabs.forEach((tab, index) => {
      tab.tabIndex = index === 0 ? 0 : -1;
      tab.addEventListener("click", () => activate(tab));
      tab.addEventListener("keydown", (event) => {
        if (!["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)) {
          return;
        }

        event.preventDefault();
        let nextIndex = index;
        if (event.key === "ArrowRight") nextIndex = (index + 1) % tabs.length;
        if (event.key === "ArrowLeft") nextIndex = (index - 1 + tabs.length) % tabs.length;
        if (event.key === "Home") nextIndex = 0;
        if (event.key === "End") nextIndex = tabs.length - 1;
        activate(tabs[nextIndex], true);
      });
    });
  });

  document.querySelectorAll("[data-hero-slider]").forEach((slider) => {
    const slides = Array.from(slider.querySelectorAll("[data-hero-slide]"));
    const dots = Array.from(slider.querySelectorAll("[data-hero-dot]"));
    const previous = slider.querySelector("[data-hero-prev]");
    const next = slider.querySelector("[data-hero-next]");

    if (slides.length < 2) {
      return;
    }

    let activeIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains("is-active")));
    let autoplay;

    const show = (nextIndex, announce = false) => {
      activeIndex = (nextIndex + slides.length) % slides.length;
      slides.forEach((slide, index) => {
        const isActive = index === activeIndex;
        slide.classList.toggle("is-active", isActive);
        slide.setAttribute("aria-hidden", String(!isActive));
        slide.querySelectorAll("a, button, input, select, textarea").forEach((control) => {
          control.tabIndex = isActive ? 0 : -1;
        });
      });
      dots.forEach((dot, index) => {
        const isActive = index === activeIndex;
        dot.classList.toggle("is-active", isActive);
        dot.setAttribute("aria-selected", String(isActive));
        dot.tabIndex = isActive ? 0 : -1;
      });
      slider.querySelector(".hero-slider__viewport").setAttribute("aria-live", announce ? "polite" : "off");
    };

    const stop = () => window.clearInterval(autoplay);
    const start = () => {
      stop();
      if (!reducedMotion.matches) {
        autoplay = window.setInterval(() => show(activeIndex + 1), 6500);
      }
    };

    previous?.addEventListener("click", () => { show(activeIndex - 1, true); start(); });
    next?.addEventListener("click", () => { show(activeIndex + 1, true); start(); });
    dots.forEach((dot, index) => dot.addEventListener("click", () => { show(index, true); start(); }));
    slider.addEventListener("mouseenter", stop);
    slider.addEventListener("mouseleave", start);
    slider.addEventListener("focusin", stop);
    slider.addEventListener("focusout", (event) => {
      if (!slider.contains(event.relatedTarget)) start();
    });
    slider.addEventListener("keydown", (event) => {
      if (event.key === "ArrowLeft") { show(activeIndex - 1, true); start(); }
      if (event.key === "ArrowRight") { show(activeIndex + 1, true); start(); }
    });

    show(activeIndex);
    start();
  });

  const revealTargets = Array.from(document.querySelectorAll([
    ".section__heading",
    ".section--split > div",
    ".feature-card",
    ".module-card",
    ".homepage-card",
    ".strategy-flow__step",
    ".academy-spotlight",
    ".role-gains",
    ".resource-card",
    ".final-cta",
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

  const academyDialog = document.querySelector("[data-academy-dialog]");
  if (academyDialog) {
    const academyProgramSelect = academyDialog.querySelector("[data-academy-program-select]");
    const academyParticipationInputs = Array.from(academyDialog.querySelectorAll('[name="participation_type"]'));
    const closeAcademyDialog = () => {
      if (academyDialog.open) {
        academyDialog.close();
      }
    };

    document.querySelectorAll("[data-academy-form-open]").forEach((trigger) => {
      trigger.addEventListener("click", () => {
        const program = trigger.dataset.program || "";
        const participation = trigger.dataset.participation || "";

        if (academyProgramSelect && program) {
          academyProgramSelect.value = program;
        }
        if (participation) {
          const input = academyParticipationInputs.find((item) => item.value === participation);
          if (input) input.checked = true;
        }

        if (typeof academyDialog.showModal === "function") {
          academyDialog.showModal();
        } else {
          academyDialog.setAttribute("open", "");
        }
      });
    });

    academyDialog.querySelector("[data-academy-form-close]")?.addEventListener("click", closeAcademyDialog);
    academyDialog.addEventListener("click", (event) => {
      if (event.target === academyDialog) closeAcademyDialog();
    });

    const academyFormStatus = new URLSearchParams(window.location.search).get("myliba_form");
    if (academyFormStatus && typeof academyDialog.showModal === "function") {
      academyDialog.showModal();
    }
  }

  document.querySelectorAll("[data-academy-slider]").forEach((slider) => {
    const track = slider.querySelector("[data-slider-track]");
    const previous = slider.querySelector("[data-slider-previous]");
    const next = slider.querySelector("[data-slider-next]");
    if (!track) return;

    const move = (direction) => {
      const card = track.querySelector("article");
      const distance = card ? card.getBoundingClientRect().width + 18 : track.clientWidth * 0.85;
      track.scrollBy({ left: distance * direction, behavior: reducedMotion.matches ? "auto" : "smooth" });
    };

    previous?.addEventListener("click", () => move(-1));
    next?.addEventListener("click", () => move(1));
  });
})();

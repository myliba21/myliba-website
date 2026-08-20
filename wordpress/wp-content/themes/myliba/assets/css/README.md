# CSS architecture

Editable CSS lives in `src/`; production CSS lives in `dist/`.

- `src/base/`: global foundations, responsive rules and theme refinements
- `src/components/`: shared interface components such as navigation and announcement bars
- `src/pages/`: page-specific and legacy page layouts
- `src/shared.css`: late shared overrides used across page bundles
- `main.css`: compatibility entry point only; WordPress does not enqueue it
- `dist/`: generated, minified bundles; do not edit these files directly

After changing a source module, rebuild the production bundles from the repository root:

```sh
node tools/build-css.mjs
```

Bundle-to-route selection is defined in `functions.php` inside `myliba_enqueue_assets()`.

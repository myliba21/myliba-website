#!/usr/bin/env node

import { mkdir, readFile, writeFile } from "node:fs/promises";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const projectRoot = dirname(dirname(fileURLToPath(import.meta.url)));
const cssDir = join(projectRoot, "wordpress/wp-content/themes/myliba/assets/css");
const sourceDir = join(cssDir, "src");
const outputDir = join(cssDir, "dist");

const sourceFiles = {
  base: [
    "base/foundation.css",
    "pages/home-foundation.css",
    "components/navigation.css",
    "pages/academy-foundation.css",
    "components/mobile-navigation.css",
    "pages/home-refresh.css",
    "base/responsive.css",
    "pages/home-pillars.css",
    "pages/legacy-layouts.css",
    "components/announcement.css",
    "base/theme-refinements.css",
    "pages/home-premium.css",
  ],
  academy: "pages/academy.css",
  software: "pages/software.css",
  solutions: "pages/solutions.css",
  development: "pages/development.css",
  shared: "shared.css",
  story: "pages/story.css",
  ethics: "pages/ethics.css",
  faq: "pages/faq.css",
};

const readSources = async (paths) => {
  const sourcePaths = Array.isArray(paths) ? paths : [paths];
  const contents = await Promise.all(
    sourcePaths.map((relativePath) => readFile(join(sourceDir, relativePath), "utf8")),
  );
  return contents.join("");
};

const sections = Object.fromEntries(await Promise.all(
  Object.entries(sourceFiles).map(async ([name, paths]) => [
    name,
    await readSources(paths),
  ]),
));

// Conservative minification: comments, indentation and blank lines are removed,
// while declaration values and quoted/data-URI contents remain untouched.
const minify = (css) => css
  .replace(/\/\*[\s\S]*?\*\//g, "")
  .split("\n")
  .map((line) => line.trim())
  .filter(Boolean)
  .join("\n")
  .concat("\n");

await mkdir(outputDir, { recursive: true });
await Promise.all(
  Object.entries(sections).map(([name, css]) => writeFile(join(outputDir, `${name}.min.css`), minify(css))),
);

const totalSourceBytes = Object.values(sections).reduce((sum, css) => sum + Buffer.byteLength(css), 0);
const totalOutputBytes = Object.values(sections).reduce((sum, css) => sum + Buffer.byteLength(minify(css)), 0);
process.stdout.write(`Built ${Object.keys(sections).length} CSS bundles (${totalSourceBytes} -> ${totalOutputBytes} bytes).\n`);

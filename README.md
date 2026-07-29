# Living Canvas — Portfolio 2026

WordPress **block theme** behind [goncalogoncalves.pt](https://www.goncalogoncalves.pt):
a draggable "living canvas" of overlapping cards that visitors can move, resize,
restack, and show/hide — on desktop **and** mobile.

Everything on the canvas is ordinary Gutenberg content, so the markup stays
semantic (real `<h1>`/`<h2>`/`<p>`/`<img>`) and the page is fully editable in
WordPress. A small vanilla-JS layer progressively enhances that content into the
canvas; with JavaScript off it degrades to a plain readable column.

## Repository layout

| Folder | What it is |
|---|---|
| [`goncalo-portfolio-theme/`](goncalo-portfolio-theme) | **Parent theme.** The design, the `goncalo/card` block, templates, patterns, canvas JS/CSS. |
| [`goncalo-portfolio-child/`](goncalo-portfolio-child) | **Child theme — activate this one.** Where site-specific tweaks live so they survive parent updates. |

## Install

1. Copy **both** folders into `wp-content/themes/`.
2. **Appearance → Themes** → activate *Portfolio 2026 (Child)*.
3. Create a page, insert the pattern **Patterns → Portfolio → "Portfolio — homepage
   (H1, notes, cards)"**, then set it as the static homepage in
   **Settings → Reading**.

No build step: the block editor code uses WordPress's bundled `wp.*` globals.

## Features

- **Card block** (`goncalo/card`) — add/reorder Heading, Paragraph, Image and List
  blocks inside each card; per-card label, size and colours.
- **Canvas controls** — a floating chip with the `GG` wordmark (links home, resets
  the layout), one colour swatch per card (bring to front) and a single
  **show/hide all cards** toggle that reveals the "thinking" notes behind them.
- **Per card** — drag by the top handle, resize from the corner, send to back.
- **Mobile mirrors desktop** — the same canvas and the same controls at every size.
- **Editable design** — colours, fonts and type scale in **Appearance → Editor →
  Styles**; System Serif for headings and System Sans-Serif for body by default,
  with Google Fonts installable via the WordPress Font Library.

## Standards / audit compliance

Meta description, Open Graph + Twitter cards, JSON-LD (`Person`, `WebSite`,
`CreativeWork`), `theme-color`/`color-scheme`, an H1-first heading order, a
focus-visible skip link, visible focus rings, `prefers-reduced-motion`,
conservative security headers, AI-crawler rules in `robots.txt`, a generated
`/llms.txt` index, and Portuguese translations for all UI strings.

See [`goncalo-portfolio-theme/README.md`](goncalo-portfolio-theme/README.md) for
the full documentation, including the items that must be fixed at the host level
(HSTS, CSP, cache headers).

## Requirements

WordPress 6.6+ (developed against 7.0), PHP 7.4+.

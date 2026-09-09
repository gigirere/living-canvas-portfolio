# Gonçalo Gonçalves — Portfolio 2026 (WordPress block theme)

A draggable-card portfolio for `goncalogoncalves.pt`. Content is authored with
Gutenberg blocks (real `<h1>`/`<h2>`/`<p>`/`<img>` for SEO); a small vanilla-JS
layer turns the cards into a draggable canvas — on **mobile and desktop alike**.

**v2.0 is a block theme (FSE)**, so typography, colours and fonts are editable in
**Appearance → Editor → Styles**.

## Install (LocalWP or any WordPress)

Use **one** of these. `goncalo-portfolio-theme.zip` (one folder up) already has
the correct structure for the WordPress uploader.

**A. Upload the zip**
1. WP Admin → **Appearance → Themes → Add New → Upload Theme**.
2. Choose the zip → **Install Now** → **Activate**.

**B. Copy the folder (LocalWP)**
1. LocalWP → right-click the site → **Reveal in Finder**.
2. Go to `app/public/wp-content/themes/`.
3. Copy the whole **`goncalo-portfolio-theme`** folder there, so you get
   `.../themes/goncalo-portfolio-theme/style.css`.
4. WP Admin → **Appearance → Themes → Activate**.

**Then add content**
1. Create a Page (e.g. "Home"). In the editor: inserter → **Patterns → Portfolio →
   "Portfolio — homepage (H1, notes, cards)"**.
2. **Settings → Reading → Your homepage displays → A static page →** select it.

No build step — the Card block uses WordPress's bundled `wp.*` scripts.

## The homepage anatomy

Everything on the canvas is ordinary block content in the page:

| Element | Block | Notes |
|---|---|---|
| `goncalo goncalves, 2026` | **Heading (H1)**, class `pf-h1` | The page H1. Fully editable. |
| `thinking` / `public notes to self` + notes | **Group**, class `pf-notes`, containing a Paragraph (class `pf-notes__label`), an H2 and Paragraphs | Sits in the background, behind the cards. No longer a card — just core blocks. The 3rd paragraph onward renders dimmed. |
| The cards | **Card** block (`goncalo/card`) | One per card. Any core block works inside it and can be reordered freely. |

## Editing content

A Card is a **container only** — everything inside it is an ordinary Gutenberg
block, with its own toolbar and sidebar. Heading, Paragraph, Image, Gallery,
Video, Embed, Columns, Group, Quote, Table, Details, Buttons, List, Separator,
third-party blocks: all of them insert, paste, transform and reorder natively.
Images upload to the **Media Library**.

The one exception is the Card block itself, which cannot be nested inside
another Card — the canvas script positions every card it finds, so a card
inside a card would break the layout.

Two things to know about how a Card lays out its contents:

- **The card is a fixed size, and taller content scrolls inside it.** The
  editor shows the same fixed height and the same internal scrolling, so what
  you see while authoring is what the visitor gets. Adjust **Height** in the
  sidebar to fit more.
- **The native alignment and spacing controls work.** The card body is a core
  flow-layout container, so "Align left" / "Align right" float and text wraps
  around the image, "Align center" centres, and the Dimensions panel's margins
  apply. An image you have not sized or aligned fills the card width.

Each Card's sidebar has: **Label** (the small eyebrow), **Width / Height** (its
size on the desktop canvas) and **Background** + optional **Header colour**.

**Add another card:** insert another Card block. It gets its own header swatch
automatically.

## Typography, colours and fonts

**Appearance → Editor → Styles**:

- Defaults: **System Serif** for all headings, **System Sans-Serif** for body text.
- Any registered family can be picked per-block or globally
  (System Serif / Sans-Serif / Monospace ship with the theme).
- **Google Fonts:** Styles → Typography → **Manage fonts** (the WordPress Font
  Library) installs any Google Font locally, and it then appears in the same
  font pickers. No CDN calls are hard-coded in the theme.
- The colour palette (paper, ink, muted, dim, and the card colours) is defined in
  `theme.json` and available in every colour picker.

## Controls (the floating chip)

- **GG** — the site logo (serif wordmark). Links home; on the canvas it **resets**
  the layout instead of navigating.
- **Colour swatches** — one per card, front-most first. Click to bring that card
  to the front (and to reveal the cards if they are hidden).
- **Eye button** — **show/hide all cards**, revealing the "thinking" notes
  underneath. Available on mobile too. (This replaced the old align controls.)
- **Per card:** drag by the top handle, resize from the bottom-right corner, and
  send to back with the layers icon.

Every one of these works at every screen size — mobile mirrors desktop. Without
JavaScript the cards degrade to a plain readable column.

## Files

```
style.css              Theme header
theme.json             Fonts, colours, sizes, layout, base styles (v3)
functions.php          Asset enqueue, block + pattern registration, control chip
templates/             front-page · canvas · page · single · index (block templates)
patterns/              Starter homepage pattern
blocks/card/           The Card block (block.json, index.js editor, render.php)
assets/css|js/         Front-end CSS + the canvas enhancement script
```

The control chip is rendered in PHP (`wp_body_open`) rather than as a template
part, so the controls can never be deleted by accident in the Site Editor.

## Audit fixes (specification.website, 28 Jul 2026)

### Fixed in the theme

| Audit item | Fix |
|---|---|
| ✗ `<meta description>` missing | Generated per view: an explicit **excerpt** wins; the homepage falls back to the **site tagline** (Settings → General), other pages to their content. |
| ✗ No Open Graph tags | `og:type/title/url/site_name/locale/description/image` + Twitter card. Automatically **stands down if Yoast / Rank Math / SEOPress is active**. |
| ✗ No `theme-color` / `color-scheme` | `theme-color: #DED8CC` (the paper background) and `color-scheme: light` — no dark-mode white flash. |
| ✗ Heading hierarchy (H1 last) | The redesign makes **`goncalo goncalves, 2026` the first H1** in the DOM, followed by H2s. Verified in the rendered source. |
| ? No structured data | **JSON-LD** `@graph` with `Person` + `WebSite`, plus `CreativeWork` on single pages. |
| ? No skip link | "Skip to content" is the **first focusable element**, visible on focus, targeting `#content` (all templates). Core's duplicate is removed. |
| ? Focus indicators unverified | Explicit `:focus-visible` outline on every interactive element, with real selector specificity so plugin CSS cannot silently remove it. |
| ? `prefers-reduced-motion` | All transitions/animations reduced under that media query. |
| ? Security headers | Theme sends `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-Frame-Options: SAMEORIGIN`. |
| ? robots.txt for AI | Explicit `Allow` rules for GPTBot, ClaudeBot, anthropic-ai, PerplexityBot, Google-Extended, CCBot, Applebot-Extended (flip to `Disallow` to opt out). |
| ? `/llms.txt` | Served at `/llms.txt` — a plain-text index of every published page with summaries, for AI agents. |
| ? Privacy policy link | A quiet 10px link, bottom-left, shown **only** when a privacy page is set (Settings → Privacy). |
| ⚠ `lang` attribute | Handled by core once the site language is set — confirmed rendering `<html lang="pt-PT">`. |
| ⚠ Non-English UI strings | Portuguese translations ship in `languages/` (skip link, all card/control labels). |

### Still needs action outside the theme

- **Favicons** — set **Settings → General → Site Icon**; WordPress then emits `favicon.ico`, `apple-touch-icon` and the tile image. A PWA manifest with a maskable icon would need a plugin.
- **HSTS + full CSP** — must be set at the host/server level (a theme cannot reliably send HSTS). Add `Strict-Transport-Security: max-age=31536000; includeSubDomains` and a CSP in your host panel / `.htaccess`, then re-check on securityheaders.com.
- **Cache-Control + brotli/gzip** — host/server configuration.
- **Alt text** — the 4 images flagged in the "In randomness…" section (Mega-Chill, Beal 50, A Urtiga, Kamba) need descriptions in the Media Library, or an explicitly empty alt if truly decorative.
- **Link text** — replace "see the case" / "case study" with e.g. "Ver caso NOAN".
- **JPEG → WebP/AVIF** — re-upload `dscarb-1.jpg` and `amigos-1-1.jpg` as WebP/AVIF.
- **Core Web Vitals** — measure on PageSpeed Insights once deployed.
- `/.well-known/security.txt` — a static file on the host.

## Upgrade notes (v2.0 → v2.1)

Nothing to migrate — the block name, attributes and saved markup are unchanged,
so existing cards keep rendering. What changed:

- **Any block can now go inside a Card.** v2.0 whitelisted nine core blocks via
  `allowedBlocks`; because that list also gates paste and block transforms, you
  could not paste a Quote into a card or turn a Paragraph into one. The list is
  now derived from the live block registry minus `goncalo/card`, so blocks added
  by a later WordPress release or a plugin are available without editing the
  theme.
- **Native alignment works.** `.card__content` was a flex column, and flex items
  ignore floats and auto margins — the Image block's align controls rendered but
  did nothing. The card body is now a core flow-layout container
  (`is-layout-flow`), which is what WordPress's own
  `.is-layout-flow > .alignleft` / `.aligncenter` rules key off.
- **The editor matches the front end.** The editor card used `min-height` and
  grew with its content while the front end used a fixed height with internal
  scrolling, so a tall gallery looked fine while authoring and was cut off when
  published. Both are now fixed height with the same scroll behaviour.
- **Images obey the native size and align controls.** The blanket
  `img { width: 100% }` is now the default only for an image with no explicit
  width and no alignment.
- **Removed a filter that never ran.** v2.0 tried to limit embeds inside cards to
  Vimeo with a `blocks.getBlockVariations` filter. WordPress has no such hook
  (see `blocks.*` in `wp-includes/js/dist/`), so the code was dead and every
  embed provider was offered anyway. It is gone, along with the now-unused
  `wp-hooks` script dependency; all embed providers are available, which is the
  native behaviour.

## Upgrade notes (v1 → v2)

- The `goncalo/card` block name and attributes are **unchanged**, so cards saved
  with v1 keep rendering (a card without `headerColor` falls back to `bgColor`).
- The classic PHP templates (`header.php`, `front-page.php`, …) were replaced by
  block templates in `templates/`.
- The paper-texture background was removed — the design now uses a flat
  `#DED8CC`. (Ask if you want the texture back.)
- v1's "thinking" **card** is now background notes. Move that card's text into
  the `pf-notes` group (the starter pattern shows the structure) and delete the
  old card when you're ready.

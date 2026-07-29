# Portfolio 2026 — Child theme

Activate **this** theme; it inherits everything from the parent
(`goncalo-portfolio-theme`) and is where your own customisations live, so they
are never lost when the parent is updated.

## Install

1. Upload/copy **both** folders into `wp-content/themes/`:
   - `goncalo-portfolio-theme` (parent — must be present, not activated)
   - `goncalo-portfolio-child` (this one — activate it)
2. **Appearance → Themes → Activate** "Portfolio 2026 (Child)".

Nothing else changes: the Card block, the canvas controls, the patterns, the
Site Editor Styles and the Font Library all come from the parent.

## Where to put things

| You want to… | Do this |
|---|---|
| Add/override CSS | Write it in this theme's `style.css` (loads after the parent's). |
| Change colours, fonts, sizes | **Appearance → Editor → Styles** (saved to the database — already update-safe). |
| Override a template | Copy the file from the parent's `templates/` into a `templates/` folder here and edit it. |
| Override a pattern | Same idea with `patterns/`. |
| Add PHP | Put it in this theme's `functions.php`. |
| Add theme.json overrides | Create a `theme.json` here — it is merged over the parent's. |

Do **not** edit the parent theme directly; those edits would be overwritten the
next time the parent is replaced.

# Weave Style Guide

An auto-generated styles page for block themes. One page at `/styles/` (noindex) made of nine server-rendered blocks that read the live theme when the page loads: nothing on it is typed in, so it cannot drift from the theme.

Sections, in the order the page uses them: version line, logo, colours, type, spacing, buttons and block styles, icons, contrast, patterns.

## Install

Copy or symlink the folder into `wp-content/plugins/`, activate, then create the page:

```
wp weave-style-guide create            # creates /styles/ if missing
wp weave-style-guide create --refresh  # rewrites the page content from the current pattern
```

Or through the Abilities API when weave-abilities is present: `weave/style-guide-create` with `{ "slug": "styles", "refresh": true }`.

The page is a pattern (`weave-style-guide/page`), so it can also be inserted by hand, and each block can be placed on its own anywhere.

## What each block reads

| Block | Source |
|---|---|
| version | Theme name and version, theme.json modified time, WordPress version |
| logo | Site Logo (customizer) plus `assets/logo/primary.svg`, `reversed.svg`, `mark.svg` in the theme, shown on their surfaces, with download links |
| colours | `settings.color.palette.theme`: swatch, name, hex, RGB, HSL, CSS custom property, click to copy; copy the palette as CSS or JSON |
| type | `fontFamilies`, `fontSizes` (desktop and mobile range for fluid sizes) on real text; h1 to h6 and body as global styles set them |
| spacing | `spacingSizes` as bars; `custom.radius` if present |
| blocks | Core Buttons in every registered style, List styles rendered, other block styles by block |
| icons | Every registered icon outside the core collection (WordPress 7.1 Icons API); click to copy the name |
| contrast | WCAG 2 ratios for every palette pairing, AA and AAA marked (`minimum` attribute hides pairs below a ratio) |
| patterns | Every non-core pattern rendered live with `do_blocks`, collapsed by default (`collapsed`, `category` attributes); patterns that render the current page or a template part are listed, not rendered |

## Conventions the theme supplies

- Logo variants as SVG files in `assets/logo/`.
- Icons registered through `wp_register_icon()` (the theme does this from `assets/icons/`).
- Motion block styles named `reveal*` are skipped in the buttons row.

## Deploying by copy (until the plugin has its own repo)

`rsync` the folder, `chown` to the site user, then reset opcache without restarting PHP: write a one-line `<?php opcache_reset();` file in the docroot, request it once, delete it. Then `wp cache flush` and the nginx purge. Skip `gp fix cached` while anyone is editing; it restarts PHP.

## Notes

- Styles are token-driven (`--wp--preset--*`, `--wp--custom--*`), so the page inherits the client's theme.
- The stylesheet is registered with `path`, so core inlines it; the copy script is deferred and tiny.
- Colour maths ported from gp-beaver-integration and weave-accessibility-colour-matrix.
- Planned: brand kit download (logos, palette JSON, one-page summary), helper-class section from a theme-supplied JSON.

---
name: 404-page
description: How to ensure clean 404 pages for calculator result pages (e.g. /factorielle-de-X/, /diviseurs-de-X/). Use this skill whenever a calculator result page needs to serve a 404 for out-of-range numbers. The 404 page must be clean — no header block, no search form, no pre-title, no H1 spin.
---

# 404 Page Skill for Calculator Result Pages

## What a Clean 404 Looks Like

- WordPress header (logo, nav) — **WITHOUT** the HeaderBlock (pre-title + search form + H1)
- Theme's standard 404 content: big "404 :(" graphic, "NOT FOUND", search bar, Voir aussi posts
- WordPress footer

---

## Root Cause: Why the Header Block Appears on 404 Pages

The Sahifa theme uses **custom action hooks** (`before_custom_header_block` / `after_custom_header_block`) that call `renderBeforeBlock()` and `renderAfterBlock()` **directly**. These fire inside `get_header()`.

When the template file calls `include(get_query_template('404'))`, the theme's 404 template calls `get_header()`, which fires those Sahifa hooks → `renderBeforeBlock()` and `renderAfterBlock()` execute → the pre-title, search form, and H1 spin render ABOVE the 404 content.

> ⚠️ The guard must be in **BOTH** `renderBeforeBlock()` AND `renderAfterBlock()`, not just `maybeRenderBlocks()`. The Sahifa hooks bypass `maybeRenderBlocks()` entirely.

---

## The Fix — 4 Places to Update

### Place 1 ⭐ `src/HeaderBlock.php` → `renderBeforeBlock()`: Add out-of-bounds guard

THIS IS THE MOST IMPORTANT. Add after the factorial guard (which uses the same pattern):

```php
// Suppress for out-of-bounds diviseur pages (> 1000000 or < 1)
$diviseur_id = $wp_query->get('mon_calcul_id');
if ($diviseur_id !== '' && ((int)$diviseur_id > MAX_LIMIT || (int)$diviseur_id < 1)) {
    return;
}
```

### Place 2 ⭐ `src/HeaderBlock.php` → `renderAfterBlock()`: Same guard

Add the **exact same guard** in `renderAfterBlock()` after its factorial guard:

```php
// Suppress for out-of-bounds diviseur pages (> 1000000 or < 1)
$diviseur_id_check = $wp_query->get('mon_calcul_id');
if ($diviseur_id_check !== '' && ((int)$diviseur_id_check > MAX_LIMIT || (int)$diviseur_id_check < 1)) {
    return;
}
```

### Place 3 — `src/HeaderBlock.php` → `maybeRenderBlocks()`: Same guard

For non-Sahifa themes that use `wp_body_open`, add the same guard after the factorial check.

### Place 4 — `src/TemplateLoader.php` → `fix404Flags()` + `loadTemplate()`

**`fix404Flags()`** — Add before the 200-OK block:
```php
$mon_calcul_id_raw = $wp_query->get('mon_calcul_id');
if ($cel_page === 'mon-calcul-de-x' && $mon_calcul_id_raw !== '') {
    if (!is_numeric($mon_calcul_id_raw) || intval($mon_calcul_id_raw) < 1 || intval($mon_calcul_id_raw) > MAX_LIMIT) {
        $wp_query->set_404();
        status_header(404);
        return;
    }
}
```

**`loadTemplate()`** — Range-guard the template:
```php
if ($cel_page === 'mon-calcul-de-x') {
    $x = $wp_query->get('mon_calcul_id');
    if ($x !== '' && is_numeric($x) && intval($x) >= 1 && intval($x) <= MAX_LIMIT) {
        $plugin_template = CEL_PLUGIN_DIR . 'templates/mon-calcul-de-x.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
}
```

---

## Template Safety Net (Optional but Recommended)

Keep the 404 check at the top of the template file as a second guard:

```php
if ($x > MAX_LIMIT) {
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include(get_query_template('404'));
    exit;
}
```

This fires BEFORE `get_header()` in the template, but after the Sahifa hooks have already fired via the initial page load. The HeaderBlock guards (Places 1-3) are what actually prevent the header from appearing.

---

## Reference: Existing MAX_LIMIT Values

| Calculator | MAX_LIMIT | Query var |
|---|---|---|
| `/factorielle-de-X/` | 10 000 | `factorial_id` |
| `/diviseurs-de-X/` | 1 000 000 | `diviseur_id` |

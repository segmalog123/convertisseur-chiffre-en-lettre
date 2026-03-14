---
name: 404-page
description: How to ensure clean 404 pages for calculator result pages (e.g. /factorielle-de-X/, /diviseurs-de-X/). Use this skill whenever a calculator result page needs to serve a 404 for out-of-range numbers. The 404 page must be clean — no header block, no search form, no pre-title, no H1 spin.
---

# 404 Page Skill for Calculator Result Pages

## What a Clean 404 Looks Like

The target 404 page shows only:
- WordPress header (logo, nav)
- The theme's standard 404 content: big "404 :(" graphic, "NOT FOUND", search bar, Voir aussi posts
- WordPress footer

**What must NOT appear on a 404:** the HeaderBlock (pre-title + search form + H1 spin). If it appears, it means WordPress still thinks the page is valid (200 OK) when the header renders.

---

## Root Cause of the "Header Appears on 404" Bug

The HeaderBlock renders via a WordPress action hook that fires **before** the template file is loaded. If WordPress thinks the page is valid (200 OK) at hook time, HeaderBlock renders — even if the template later calls `set_404()` and `include(get_query_template('404'))`.

This is why **the 404 must be declared in `fix404Flags()`**, not in the template file.

---

## The Fix — 3 Places to Update

### Place 1 — `src/HeaderBlock.php` `maybeRenderBlocks()`: Add out-of-bounds guard ⭐ MOST IMPORTANT

This is the **real cause** of the header appearing on 404 pages. HeaderBlock renders via a WordPress action hook fired **before** the template — even before `fix404Flags()` fully takes effect. The existing factorielle pattern (lines 60-63) must be mirrored for every new calculator:

```php
// In maybeRenderBlocks(), after the factorial guard:
$mon_calcul_id = $wp_query->get('mon_calcul_id');
if ($mon_calcul_id !== '' && ((int)$mon_calcul_id > MAX_LIMIT || (int)$mon_calcul_id < 1)) {
    return; // ← stops HeaderBlock from rendering
}
```

### Place 2 — `src/TemplateLoader.php` `fix404Flags()`: Declare 404 for out-of-range

Add before the 200-OK block:

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

### Place 3 — `src/TemplateLoader.php` `loadTemplate()`: Range-guard the template

```php
if ($cel_page === 'mon-calcul-de-x') {
    $x = $wp_query->get('mon_calcul_id');
    if ($x !== '' && is_numeric($x) && intval($x) >= 1 && intval($x) <= MAX_LIMIT) {
        $plugin_template = CEL_PLUGIN_DIR . 'templates/mon-calcul-de-x.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    // Out of range → falls through → WordPress serves clean 404
}
```

---

## You Can Remove 404 Logic from the Template File

Once `fix404Flags()` and `loadTemplate()` handle the 404 correctly, the PHP block at the top of the template that calls `set_404()` / `include(404)` becomes redundant (though harmless to keep as a safety net).

The factorielle template also has this safety net:
```php
if ($x > 10000) {
    $wp_query->set_404(); status_header(404); nocache_headers();
    include(get_query_template('404')); exit;
}
```

**Keep it** as a second guard, but know that the real fix is in TemplateLoader.

---

## Reference: Existing MAX_LIMIT Values

| Calculator | MAX_LIMIT | `fix404Flags()` condition |
|---|---|---|
| `/factorielle-de-X/` | 10 000 | `intval($x) < 0 \|\| intval($x) > 10000` |
| `/diviseurs-de-X/` | 1 000 000 | `intval($x) < 1 \|\| intval($x) > 1000000` |

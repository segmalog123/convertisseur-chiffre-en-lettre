---
name: calculator-landing-page
description: Layout, structure, design, content, and search bar logic for creating new calculator landing pages (like /calculatrice-factorielle/ or /calculatrice-diviseurs-pgcd-en-ligne/). Use this skill whenever creating a new calculator landing page or a new /calculatrice-*/ URL.
---

# Calculator Landing Page Skill

Read this skill BEFORE creating any new calculator landing page template (e.g. `/calculatrice-mon-calcul-en-ligne/`).

## Overview

Every calculator landing page in this project follows a strict pattern. The **reference implementations** are:
- `templates/calculatrice-factorielle.php` → `/calculatrice-factorielle/`
- `templates/calculatrice-diviseurs-pgcd.php` → `/calculatrice-diviseurs-pgcd-en-ligne/`

---

## 1. Files to Touch (All 6 Required)

When creating a new calculator (e.g. "Mon Calcul"), you must update these files:

### A) Create: `templates/calculatrice-MON-CALCUL.php`
Copy structure from `calculatrice-diviseurs-pgcd.php`.

### B) `src/RewriteRules.php`
```php
add_rewrite_rule('^calculatrice-mon-calcul-en-ligne/?$', 'index.php?cel_page=calculatrice-mon-calcul', 'top');
```

### C) `src/TemplateLoader.php`
In `fix404Flags()`, add to the condition:
```php
$cel_page === 'calculatrice-mon-calcul'
```
In `loadTemplate()`, add:
```php
if ($cel_page === 'calculatrice-mon-calcul') {
    $plugin_template = CEL_PLUGIN_DIR . 'templates/calculatrice-mon-calcul.php';
    if (file_exists($plugin_template)) { return $plugin_template; }
}
```

### D) `src/SeoController.php`
Add `isMonCalculPage()` method and inject into `filterTitle()`, `filterMetaDesc()`, `outputCanonical()`, `filterRobots()`.

### E) `src/SitemapController.php`
Add to `$pages` array inside `createCalculatorsSitemap()`:
```php
'/calculatrice-mon-calcul-en-ligne/'
```

### F) `src/HeaderBlock.php`
- `maybeRenderBlocks()`: add to `in_array($cel_page, [...])`.
- `renderBeforeBlock()`: add `elseif` for pre-title text.
- `renderBeforeBlock()`: add `if` for placeholder and button label.
- `renderAfterBlock()`: add `elseif` for the H1 text.

---

## 2. Template Structure

```php
<?php
/**
 * Template: Calculatrice [Nom] Landing Page (/calculatrice-[slug]-en-ligne/)
 */
if (!defined('ABSPATH')) { exit; }
get_header();
?>
<style>
  /* ── Hide sidebar, full-width ── */
  #sidebar { display: none !important; }
  .content { width: 100% !important; }
  /* ── Wrapper ── */
  .ntw-fc-wrap { max-width: 860px; margin: 0 auto; padding: 20px 15px 50px; line-height: 1.7; color: #333; }
  /* ── Tip box ── */
  .ntw-fc-tip { background: #fffbe6; border: 1px dashed #e0c000; border-radius: 6px; padding: 12px 16px; font-size: 14px; }
  .ntw-fc-tip strong { color: #7a5c00; }
  /* ── Headings ── */
  .ntw-fc-h2 { font-size: 1.35em; font-weight: 700; color: #1a5c30; border-bottom: 2px solid #c5e8d3; padding-bottom: 6px; margin: 36px 0 14px; }
  .ntw-fc-h3 { font-size: 1.1em; font-weight: 700; color: #2a7d4f; margin: 24px 0 10px; }
  /* ── Step list ── */
  .ntw-fc-steps { list-style: none; padding-left: 0; margin: 12px 0 18px; }
  .ntw-fc-steps li { padding: 8px 0; border-bottom: 1px solid #eee; }
  .ntw-fc-steps li:last-child { border: none; }
  /* ── Pill links ── */
  .ntw-fc-pills { display: flex; flex-wrap: wrap; gap: 10px; list-style: none; padding: 0; margin: 14px 0 24px; }
  .ntw-fc-pills li a { background: #2a7d4f; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 20px; font-size: 13px; }
  .ntw-fc-pills li a:hover { background: #1a5c30; }
</style>

<div class="content">
  <?php chiffre_breadcrumbs(); ?>
  <div class="ntw-fc-wrap">

    <!-- Intro paragraph -->
    <p>Notre outil gratuit vous permet de <strong>faire le calcul</strong> instantanément...</p>

    <!-- Optional tip box with cross-links -->
    <div class="ntw-fc-tip"><p>💡 <strong>Vous cherchez autre chose ?</strong> ...</p></div>

    <!-- H2 sections -->
    <h2 class="ntw-fc-h2">1) Comment utiliser notre calculateur ?</h2>
    <ul class="ntw-fc-steps">
      <li><strong>Mode A :</strong> Entrez un seul nombre...</li>
      <li><strong>Mode B :</strong> Entrez deux nombres...</li>
    </ul>

    <h2 class="ntw-fc-h2">2) Définition</h2>
    <p>Explications mathématiques...</p>

    <!-- Frequent examples as pill links -->
    <hr style="border:0; border-top:1px solid #e0e0e0; margin:30px 0;">
    <h3 class="ntw-fc-h3">Exemples de Calculs Fréquents</h3>
    <ul class="ntw-fc-pills">
      <li><a href="<?php echo esc_url(site_url('/mon-calcul-de-24/')); ?>">Calcul de 24</a></li>
      <li><a href="<?php echo esc_url(site_url('/mon-calcul-de-42/')); ?>">Calcul de 42</a></li>
    </ul>

    <!-- FAQ -->
    <h2 class="ntw-fc-h2">FAQ</h2>
    <h3 class="ntw-fc-h3">Question 1 ?</h3>
    <p>Réponse 1...</p>

  </div><!-- /.ntw-fc-wrap -->
</div><!-- .content -->

<!-- CRITICAL: Search bar redirect script -->
<script>
(function () {
    function doMyCalculRedirect() {
        var input = document.querySelector('.convert-input');
        if (!input) return;
        var val = input.value.trim();
        var err = document.querySelector('.error-input');
        if (!val) {
            if (err) err.textContent = 'Veuillez entrer une valeur.';
            return;
        }
        // ... parse, validate, show errors in err.textContent ...
        // ALWAYS redirect to YOUR calculator's child pages, NEVER to /ecrire/
        window.location.href = '<?php echo esc_js(home_url("/")); ?>mon-calcul-de-' + parsedValue + '/';
    }
    window.addEventListener('load', function () {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.convert-button[data-convert="my-calc"]');
            if (btn) { e.preventDefault(); e.stopImmediatePropagation(); doMyCalculRedirect(); }
        }, true);
        var inp = document.querySelector('.convert-input');
        if (inp) inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); doMyCalculRedirect(); }
        }, true);
    });
}());
</script>

<!-- JSON-LD FAQ Schema -->
<script type="application/ld+json">
{ "@context": "https://schema.org", "@type": "FAQPage", "mainEntity": [...] }
</script>

<?php get_footer(); ?>
```

---

## 3. Search Bar — Critical Rules

The `.convert-button` default behavior redirects to `/ecrire/`. You MUST override it.

### Always use `data-convert="[type]"` (set in HeaderBlock.php)

In `HeaderBlock::renderBeforeBlock()`:
```php
} elseif ($cel_page === 'calculatrice-mon-calcul') {
    $convert_to = 'my-calc';
}
```

In your template JS, intercept only YOUR button:
```javascript
var btn = e.target.closest('.convert-button[data-convert="my-calc"]');
```

### Input Validation & Error Messages (French)

| Case | Error Message |
|---|---|
| Empty input | `'Veuillez entrer un nombre.'` |
| Non-numeric | `'Veuillez entrer un entier naturel.'` |
| Negative / zero | `'Veuillez entrer un nombre supérieur à 0.'` |
| Exceeds limit | `'Les calculs sont limités aux nombres de 1 à [LIMIT].'` |
| Wrong format | `'Veuillez entrer deux nombres séparés par une virgule ou un espace.'` |

### Multi-Input Parsing (comma OR space separator)

```javascript
var parts = val.split(/[,\s]+/)
               .map(function(s) { return s.trim(); })
               .filter(function(s) { return s !== ''; });
```

### Always Sort Numbers in URL (smaller first)

```javascript
var num1 = Math.min(raw1, raw2);
var num2 = Math.max(raw1, raw2);
// → /pgcd-de-24-et-42/ (not /pgcd-de-42-et-24/)
```

### IMPORTANT: Close All Braces Correctly

A missing `}` inside `doDivisorsRedirect()` will silently break the JS and cause the page to fall through to the default `/ecrire/` redirect. Always verify the structure:

```javascript
function doMyRedirect() {
    if (!val) {
        if (err) err.textContent = '...';
        return;
    }  // ← this closing brace is MANDATORY before the next line
    var parts = val.split(...);
    ...
}
```

---

## 4. Content Checklist

Every landing page MUST have:
- [ ] Intro paragraph with bolded keywords
- [ ] Optional tip box (💡) with cross-links
- [ ] At least 2 H2 sections
- [ ] 6–10 pill links with frequent calculation examples
- [ ] FAQ with 3 questions minimum
- [ ] JSON-LD FAQPage schema
- [ ] JS redirect script intercepting `.convert-button[data-convert="YOUR_TYPE"]`
- [ ] Error handling for all bad input cases

---

## 5. Reference Examples

### PGCD Calculator

| Setting | Value |
|---|---|
| URL | `/calculatrice-diviseurs-pgcd-en-ligne/` |
| `cel_page` | `calculatrice-diviseurs-pgcd` |
| Template | `templates/calculatrice-diviseurs-pgcd.php` |
| `data-convert` | `divisors` |
| Pre-title | `Calculer les diviseurs ou le PGCD instantanément` |
| Placeholder | `Entrez un nombre (ex: 24) ou deux nombres (ex: 24, 42)` |
| Button | `CALCULER` |
| H1 | `Calculateur de Diviseurs et PGCD en Ligne` |
| Limit | 200 (PGCD mode) |
| Redirect (1 number) | `/diviseurs-de-{n}/` |
| Redirect (2 numbers) | `/pgcd-de-{min}-et-{max}/` |

### Factorial Calculator

| Setting | Value |
|---|---|
| URL | `/calculatrice-factorielle/` |
| `cel_page` | `calculatrice-factorielle` |
| Template | `templates/calculatrice-factorielle.php` |
| `data-convert` | `factorial` |
| Pre-title | `Calculer la factorielle (n!) de n'importe quel nombre instantanément` |
| Placeholder | `Entrez un entier positif (ex: 5)` |
| Button | `CALCULER` |
| H1 | `Calculatrice Factorielle : Calculer la Factorielle (n!)` |
| Limit | 10 000 |
| Redirect | `/factorielle-de-{n}/` |

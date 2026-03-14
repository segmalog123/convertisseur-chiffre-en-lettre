---
name: calculator-result-page
description: Layout, structure, design and content prototype for calculator child/result pages (e.g. /factorielle-de-X/, /diviseurs-de-X/, /pgcd-de-X-et-Y/). Use this skill whenever creating a new dynamic result template for a calculator. Every result page must include Recherches associées and FAQ sections.
---

# Calculator Child Result Page Skill

Read this skill BEFORE creating any new calculator **result/child page** template (e.g. `/mon-calcul-de-X/`).

## Overview

Child result pages are dynamically generated from a URL parameter (e.g. the `X` in `/factorielle-de-5/`).

**Reference implementations:**
- `templates/factorielle-de-x.php` → `/factorielle-de-{n}/`
- `templates/diviseurs-de-x.php` → `/diviseurs-de-{n}/`

---

## 1. Files to Touch

### A) Create: `templates/mon-calcul-de-x.php`

### B) `src/RewriteRules.php`
```php
add_rewrite_rule('^mon-calcul-de-([0-9]+)/?$', 'index.php?cel_page=mon-calcul-de-x&mon_calcul_id=$matches[1]', 'top');
add_filter('query_vars', function($vars) { $vars[] = 'mon_calcul_id'; return $vars; });
```

### C) `src/TemplateLoader.php`
- `fix404Flags()`: add `|| $cel_page === 'mon-calcul-de-x'`
- `loadTemplate()`: add template return block

### D) `src/SeoController.php`
- Add `getMonCalculId()` and inject into title, meta desc, canonical, robots filters.

### E) `src/HeaderBlock.php` — **5 places to update** (see Section 3 below)

---

## 2. Template Structure

```php
<?php
if (!defined('ABSPATH')) { exit; }

/* ── 1. Get & sanitise input ── */
global $wp_query;
$x = intval($wp_query->get('mon_calcul_id'));
if ($x < 1) { $x = 1; }

/* ── 2. 404 for out-of-bounds ── */
if ($x > MAX_LIMIT) {
    $wp_query->set_404(); status_header(404); nocache_headers();
    include(get_query_template('404')); exit;
}

/* ── 3. Compute result ── */
$result = /* your calculation */;

/* ── 4. Spin arrays — informational only, real rendering in HeaderBlock ── */
$array_a = [
    "Description A du calcul de {$x}",
    "Définition B du calcul de {$x}",
    "Formule C pour calculer {$x}",
    "Propriétés D du calcul de {$x}",
];
$array_b = [
    "Titre A du calcul de {$x} ?",
    "La liste B du calcul de {$x}",
    "Calcul C de l'entier {$x}",
    "Comment D calculer {$x} ?",
];
$spin = ($x * 7 + 13) % 4;

/* ── 5. Indexable nearby helper (mirrors SeoController rules) ── */
function cel_moncalcul_is_indexable(int $n): bool {
    if ($n < 1 || $n > MAX_LIMIT) return false;
    if ($n <= 1000) return true;
    if ($n <= 10000 && $n % 50 === 0) return true;
    if ($n <= 1000000 && $n % 500 === 0) return true;
    return false;
}

/* ── 6. Nearby indexable links (search ±600 to find 4 on each side) ── */
$nearby_below = [];
for ($i = $x - 1; $i >= max(1, $x - 600) && count($nearby_below) < 4; $i--) {
    if (cel_moncalcul_is_indexable($i)) { $nearby_below[] = $i; }
}
$nearby_below = array_reverse($nearby_below);
$nearby_above = [];
for ($i = $x + 1; $i <= min(MAX_LIMIT, $x + 600) && count($nearby_above) < 4; $i++) {
    if (cel_moncalcul_is_indexable($i)) { $nearby_above[] = $i; }
}
$nearby_links = array_merge($nearby_below, $nearby_above);

get_header();
?>
<style>
  #sidebar { display: none !important; }
  .content { width: 100% !important; }
  .nwf-wrap { max-width: 860px; margin: 0 auto; padding: 20px 15px 50px; line-height: 1.7; color: #333; }
  .nwf-result-box { background: #eafaf1; border: 1px solid #a2dbb8; border-radius: 8px; padding: 16px 22px; margin: 20px 0; }
  .nwf-result-label { font-size: 13px; color: #555; margin-bottom: 6px; }
  .nwf-result-value { font-size: 1.5em; font-weight: 700; color: #1a5c30; word-break: break-all; }
  .nwf-h2 { font-size: 1.3em; font-weight: 700; color: #1a5c30; border-bottom: 2px solid #c5e8d3; padding-bottom: 6px; margin: 36px 0 14px; }
  .nwf-h3 { font-size: 1.05em; font-weight: 700; color: #2a7d4f; margin: 22px 0 10px; }
  .nwf-pills { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 12px 0 24px; }
  .nwf-pills li a { background: #2a7d4f; color: #fff; text-decoration: none; padding: 5px 13px; border-radius: 20px; font-size: 13px; }
  .nwf-pills li a:hover { background: #1a5c30; }
  .nwf-faq-item { border-bottom: 1px solid #e0e0e0; padding: 14px 0; }
  .nwf-faq-q { font-weight: 700; color: #1a5c30; margin-bottom: 6px; }
  .nwf-faq-a { color: #444; font-size: 14px; }
</style>

<div class="content">
  <?php chiffre_breadcrumbs(); ?>
  <div class="nwf-wrap">

    <!-- RESULT BOX (always first) -->
    <div class="nwf-result-box">
      <div class="nwf-result-label">Résultat pour <?php echo $x; ?></div>
      <div class="nwf-result-value"><?php echo $result; ?></div>
    </div>

    <!-- EXPLANATION SECTIONS (minimum 2 H2) -->
    <h2 class="nwf-h2">Qu'est-ce que [calcul] de <?php echo $x; ?> ?</h2>
    <p>Explication...</p>

    <h2 class="nwf-h2">Comment calculer [calcul] de <?php echo $x; ?> ?</h2>
    <p>Méthode...</p>

    <!-- RECHERCHES ASSOCIÉES (MANDATORY) -->
    <h2 class="nwf-h2">🌐 Recherches associées</h2>
    <ul class="nwf-pills">
      <?php
      $nearby_anchors = ['Calcul de %d', 'Résultat de %d', 'Exemple %d', 'Valeur %d'];
      $i_a = 0;
      foreach ($nearby_links as $n):
          $anchor = sprintf($nearby_anchors[$i_a++ % 4], $n);
      ?>
      <li><a href="<?php echo esc_url(home_url('/mon-calcul-de-' . $n . '/')); ?>"><?php echo esc_html($anchor); ?></a></li>
      <?php endforeach; ?>

      <?php
      // ── CROSS-LINKS: NEVER link to a page that is 404 or noindex on the target calculator.
      // Each calculator and converter has its OWN limits and index rules. Check ALL of them.

      // --- /ecrire/X-en-lettre/ and /comment-on-dit/X-en-anglais/ ---
      // ⚠️ NOT always safe! These use the VIP number system (src/NumberVipList.php).
      // Only VIP numbers are indexed. Non-VIP pages exist but are NOINDEX — do NOT link to them.
      // VIP rules: 1–1000 all ; 1001–10000 step 50 ; 10001–100000 step 500 ; 100001–1000000 step 5000
      // + hardcoded: [2019–2026, 1500, 2500, 3500, 4500, 7500, 15000, 25000, 35000, 45000, 75000, 150000, 250000, 350000, 450000, 750000, 1000000]
      $vip_hardcoded = [2019,2020,2021,2022,2023,2024,2025,2026,1500,2500,3500,4500,7500,15000,25000,35000,45000,75000,150000,250000,350000,450000,750000,1000000];
      $x_is_vip = ($x <= 1000)
          || ($x <= 10000   && $x % 50   === 0)
          || ($x <= 100000  && $x % 500  === 0)
          || ($x <= 1000000 && $x % 5000 === 0)
          || in_array($x, $vip_hardcoded);
      if ($x_is_vip): ?>
      <li><a href="<?php echo esc_url(home_url('/ecrire/' . $x . '-en-lettre/')); ?>"><?php echo $x; ?> en lettres</a></li>
      <li><a href="<?php echo esc_url(home_url('/comment-on-dit/' . $x . '-en-anglais/')); ?>"><?php echo $x; ?> en anglais</a></li>
      <?php endif; ?>

      <?php
      // /factorielle-de-X/ → MAX 10000 (above = 404), indexed: x <= 200 OR (x % 50 === 0 AND x <= 10000)
      $fact_ok = ($x <= 200) || ($x > 200 && $x <= 10000 && $x % 50 === 0);
      if ($fact_ok): ?>
      <li><a href="<?php echo esc_url(home_url('/factorielle-de-' . $x . '/')); ?>">Factorielle de <?php echo $x; ?></a></li>
      <?php endif; ?>

      <?php
      // /diviseurs-de-X/ → MAX 1000000, indexed: x <= 1000 OR (x % 50 AND x <= 10000) OR (x % 500 AND x <= 1000000)
      $div_ok = ($x <= 1000) || ($x <= 10000 && $x % 50 === 0) || ($x <= 1000000 && $x % 500 === 0);
      if ($div_ok): ?>
      <li><a href="<?php echo esc_url(home_url('/diviseurs-de-' . $x . '/')); ?>">Diviseurs de <?php echo $x; ?></a></li>
      <?php endif; ?>

    </ul>

    <!-- FAQ (MANDATORY — minimum 3 questions) -->
    <h2 class="nwf-h2">FAQ</h2>

    <div class="nwf-faq-item">
      <div class="nwf-faq-q">Question 1 sur <?php echo $x; ?> ?</div>
      <div class="nwf-faq-a">Réponse...</div>
    </div>
    <div class="nwf-faq-item">
      <div class="nwf-faq-q">Question 2 ?</div>
      <div class="nwf-faq-a">Réponse...</div>
    </div>
    <div class="nwf-faq-item">
      <div class="nwf-faq-q">Question 3 ?</div>
      <div class="nwf-faq-a">Réponse...</div>
    </div>

  </div><!-- /.nwf-wrap -->
</div><!-- .content -->

<!-- SEARCH BAR REDIRECT — see Section 4 for mandatory rules -->
<script>
(function () {
    function doMyCalculRedirect() {
        var input = document.querySelector('.convert-input');
        if (!input) return;
        var val = input.value.trim();
        var err = document.querySelector('.error-input');
        if (!val) {
            if (err) err.textContent = 'Veuillez entrer un nombre.';
            return;
        }
        var num = parseInt(val, 10);
        if (isNaN(num) || num <= 0) {
            if (err) err.textContent = 'Veuillez entrer un entier naturel supérieur à 0.';
            return;
        }
        if (num > MAX_LIMIT_JS) {
            if (err) err.textContent = 'Les calculs sont limités aux nombres de 1 à LIMIT_FORMATTED.';
            return;
        }
        if (err) err.textContent = '';
        window.location.href = '<?php echo esc_js(home_url("/")); ?>mon-calcul-de-' + num + '/';
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

<!-- JSON-LD FAQPage schema -->
<script type="application/ld+json">
{ "@context": "https://schema.org", "@type": "FAQPage", "mainEntity": [...] }
</script>

<?php get_footer(); ?>
```

---

## 3. HeaderBlock.php — 5 Mandatory Places to Update

> ⚠️ **Most common source of bugs.** If you skip any of these 5 places, the page will show the generic French converter header, have an empty search bar, and redirect to `/ecrire/` instead of your calculator.

### Place 1 — `maybeRenderBlocks()`: Add to the allowed list
```php
!in_array($cel_page, ['convertisseur-anglais', 'calculatrice-factorielle', 'factorielle-de-x',
    'calculatrice-diviseurs-pgcd', 'diviseurs-de-x', /* → */ 'mon-calcul-de-x'])
```

### Place 2 — `renderBeforeBlock()`: Set `data-convert` to prevent /ecrire/ redirect
```php
} elseif ($cel_page === 'mon-calcul-de-x') {
    $convert_to = 'my-calc'; // must match [data-convert="my-calc"] in template JS
}
```

### Place 3 — `renderBeforeBlock()`: Inject Array A spin as pre-title
```php
} elseif ($cel_page === 'mon-calcul-de-x') {
    $id = $wp_query->get('mon_calcul_id');
    $array_a = [
        "Description A du calcul de {$id}",
        "Définition B du calcul de {$id}",
        "Formule C pour calculer {$id}",
        "Propriétés D du calcul de {$id}",
    ];
    echo esc_html($array_a[((int)$id * 7 + 13) % 4]);
}
```

### Place 4 — `renderBeforeBlock()`: Set placeholder, button, and pre-fill search_val
```php
if ($cel_page === 'mon-calcul-de-x') {
    $placeholder = 'Entrez un nombre (ex: 24)';
    $btn_text = 'CALCULER';
}
$id_val = ($cel_page === 'mon-calcul-de-x') ? $wp_query->get('mon_calcul_id') : '';
$search_val = $is_factorial ? $factorial_id : ($id_val !== '' ? $id_val : $number_to_convert);
```

### Place 5 — `renderAfterBlock()`: Inject Array B spin as H1
```php
} elseif ($cel_page_after === 'mon-calcul-de-x') {
    $id = $wp_query->get('mon_calcul_id');
    $array_b = [
        "Titre A du calcul de {$id} ?",
        "La liste B du calcul de {$id}",
        "Calcul C de l'entier {$id}",
        "Comment D calculer {$id} ?",
    ];
    ?>
    <h1><?php echo esc_html($array_b[((int)$id * 7 + 13) % 4]); ?></h1>
    <?php
}
```

---

## 4. Search Bar — Critical Rules

### ⛔ The Trap
The site-wide `.convert-button` defaults redirect to `/ecrire/`. Your JS **MUST** use `e.stopImmediatePropagation()` to cancel this, and `e.target.closest('.convert-button[data-convert="MY_TYPE"]')` to only intercept YOUR button.

### ✅ Required Pattern
```javascript
var btn = e.target.closest('.convert-button[data-convert="my-calc"]');
// ↑ data-convert value must match Place 2 in HeaderBlock
if (btn) {
    e.preventDefault();
    e.stopImmediatePropagation(); // ← MANDATORY — cancels /ecrire/ redirect
    doMyCalculRedirect();
}
```

### Error Messages (French) — Required Table

| Case | Message |
|---|---|
| Empty input | `'Veuillez entrer un nombre.'` |
| Non-numeric or ≤ 0 | `'Veuillez entrer un entier naturel supérieur à 0.'` |
| Exceeds limit | `'Les calculs sont limités aux nombres de 1 à X.'` (use `\u00a0` for non-breaking space in thousands) |
| PGCD > 200 | `'Les calculs de PGCD sont limités aux nombres de 1 à 200.'` |
| Two numbers needed | `'Veuillez entrer deux nombres séparés par une virgule ou un espace.'` |

All errors shown in `document.querySelector('.error-input')`.

### Multi-Input Parsing (comma OR space)
```javascript
var parts = val.split(/[,\s]+/).map(function(s){ return s.trim(); }).filter(function(s){ return s !== ''; });
if (parts.length === 1) {
    // single number → /mon-calcul-de-X/
} else if (parts.length >= 2) {
    // two numbers → e.g. /pgcd-de-min-et-max/
    var mn = Math.min(raw1, raw2);
    var mx = Math.max(raw1, raw2); // always smaller first in URL
}
```

---

## 5. Mandatory Sections Checklist

- [ ] **Result box** — `nwf-result-box`, always first
- [ ] **2+ H2 sections** — definition + calculation method
- [ ] **🌐 Recherches associées** — 4 indexable below + 4 above (search ±600), rotated anchors, cross-links
- [ ] **FAQ** — 3+ dynamic questions about X
- [ ] **JSON-LD FAQPage schema**
- [ ] **JS redirect** with `stopImmediatePropagation()`, `data-convert` match, all error cases
- [ ] **HeaderBlock.php** updated in all 5 places

---

## 6. Index/NoIndex Rules — ASK BEFORE IMPLEMENTING

> ⚠️ **Do NOT assume or reuse rules from another calculator.** Each calculator has its own indexing strategy decided by the admin.

**Before writing any `filterRobots()` logic or `cel_is_indexable()` helper, always ask:**

> *"Quelles sont les conditions d'indexation (index / noindex) pour les pages /mon-calcul-de-X/ ? Par exemple : tous les X jusqu'à 1000, puis certains multiples, etc."*

Once the user provides the rules, implement them in **two places** (they must stay in sync):

1. **`src/SeoController.php`** — `filterRobots()` method for the new page type:
```php
if ($x <= RULE_A) {
    return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
} elseif ($x <= RULE_B && $x % STEP === 0) {
    return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
} else {
    return 'noindex, follow';
}
```

2. **`templates/mon-calcul-de-x.php`** — `cel_moncalcul_is_indexable()` helper used for nearby links:
```php
function cel_moncalcul_is_indexable(int $n): bool {
    // Mirror EXACTLY the same rules as SeoController filterRobots()
    if ($n < 1 || $n > MAX_LIMIT) return false;
    if ($n <= RULE_A) return true;
    if ($n <= RULE_B && $n % STEP === 0) return true;
    return false;
}
```

**Reference rules for existing calculators (for context only — do not copy blindly):**

| Calculator | Rules |
|---|---|
| `/diviseurs-de-X/` | ≤ 1 000 → index ; 1 001–10 000 (pas 50) → index ; 10 001–1 000 000 (pas 500) → index |
| `/factorielle-de-X/` | ≤ 200 → index ; 201–10 000 (pas 50) → index |

---

## 7. Reference Examples

### `/diviseurs-de-X/`

| Setting | Value |
|---|---|
| `cel_page` | `diviseurs-de-x` |
| Query var | `diviseur_id` |
| `data-convert` | `divisors` |
| Max limit | 1 000 000 |
| Array A (pre-title, 4 variants) | "Trouver tous les diviseurs de {X}", "Calculateur des diviseurs...", etc. |
| Array B (H1, 4 variants) | "Quels sont tous les diviseurs de {X} ?", "La liste complète...", etc. |
| Sections | Result box → Classification table → Propriétés → Méthode → Recherches associées → FAQ |

### `/factorielle-de-X/`

| Setting | Value |
|---|---|
| `cel_page` | `factorielle-de-x` |
| Query var | `factorial_id` |
| `data-convert` | `factorial` |
| Max limit | 10 000 |
| Sections | Result box → Définition → Comment calculer → Propriétés → Stirling → Recherches associées → FAQ |

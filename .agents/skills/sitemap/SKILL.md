---
name: sitemap
description: How to add new calculator pages to the Yoast XML sitemap. Use this skill whenever creating a new calculator landing page OR a new calculator result page. Landing pages go into calculateurs-sitemap.xml. Result pages get their own dedicated sitemap with only the indexable URLs.
---

# Sitemap Skill

Read this skill whenever you create a new calculator landing page or result page and need to register it in the Yoast XML sitemap system.

## How the Sitemap System Works

Sitemaps are generated **dynamically** by `src/SitemapController.php` via Yoast SEO hooks — **there are no XML files on disk**. Yoast generates them on-the-fly when the URL is requested (e.g. `/factorielle-sitemap.xml`).

The root sitemap index is `sitemap_index.xml` (Yoast default). Each sub-sitemap is registered as a child entry.

---

## Case 1 — New Calculator Landing Page

Landing pages (e.g. `/calculatrice-factorielle/`, `/calculatrice-diviseurs-pgcd-en-ligne/`) are grouped together in **`calculateurs-sitemap.xml`**.

### What to do: Add the URL to `createCalculatorsSitemap()` in `SitemapController.php`

```php
$pages = [
    '/convertisseur-anglais/',
    '/calculatrice-factorielle/',
    '/calculatrice-diviseurs-pgcd-en-ligne/',
    '/mon-nouveau-calculateur/',  // ← ADD YOUR NEW PAGE HERE
];
```

That's it — no other file needs to change for landing pages.

---

## Case 2 — New Calculator Result Page

Each result page type gets its **own dedicated sitemap** (e.g. `factorielle-sitemap.xml`, `diviseurs-sitemap.xml`).

> ⚠️ **Before creating the sitemap**, you must know the index/noindex conditions for that page type. Always ask the user if not already defined (see `calculator-result-page` skill Section 6).

### 4 places to update in `src/SitemapController.php`

#### Place 1 — `init()`: Register the index filter
```php
add_filter('wpseo_sitemap_index', [$this, 'addMonCalculSitemapIndex']);
```

#### Place 2 — `registerSitemaps()`: Register the sitemap with Yoast
```php
$wpseo_sitemaps->register_sitemap('moncalcul', [$this, 'createMonCalculSitemap']);
```
> The string `'moncalcul'` determines the URL: `/moncalcul-sitemap.xml`

#### Place 3 — `registerSitemapActions()`: Register the action hook
```php
add_action('wp_seo_do_sitemap_our-moncalcul', [$this, 'createMonCalculSitemap']);
```

#### Place 4 — Add the two new methods
```php
/**
 * Create the MonCalcul result pages sitemap.
 * Rules (must mirror SeoController filterRobots logic exactly):
 * - n <= RULE_A         : all index
 * - RULE_A < n <= RULE_B : index if n % STEP_1 === 0
 * - RULE_B < n <= MAX   : index if n % STEP_2 === 0
 */
public function createMonCalculSitemap()
{
    global $wpseo_sitemaps;
    $output = '';

    for ($i = 1; $i <= MAX_LIMIT; $i++) {
        $index = false;
        if ($i <= RULE_A) {
            $index = true;
        } elseif ($i <= RULE_B && $i % STEP_1 === 0) {
            $index = true;
        } elseif ($i <= MAX_LIMIT && $i % STEP_2 === 0) {
            $index = true;
        }
        if ($index) {
            $url = [];
            $url['loc'] = site_url() . '/mon-calcul-de-' . $i . '/';
            $url['mod'] = date('c', time());
            $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
        }
    }

    $sitemap  = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
    $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
    $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $sitemap .= $output . '</urlset>';

    $wpseo_sitemaps->set_sitemap($sitemap);
}

/**
 * Add the MonCalcul sitemap to the sitemap index.
 */
public function addMonCalculSitemapIndex($items)
{
    $items .= '    <sitemap>
        <loc>' . site_url() . '/moncalcul-sitemap.xml</loc>
        <lastmod>' . date('c', time()) . '</lastmod>
    </sitemap>
';
    return $items;
}
```

> ⚠️ **The index rules in `createMonCalculSitemap()` must mirror exactly the rules in `SeoController::filterRobots()` and the `cel_is_indexable()` helper in the template.** All three must stay in sync.

---

## Reference: Existing Sitemaps

| Sitemap URL | Method | Index Rules |
|---|---|---|
| `/calculateurs-sitemap.xml` | `createCalculatorsSitemap()` | Fixed list of landing pages |
| `/factorielle-sitemap.xml` | `createFactorialSitemap()` | n ≤ 200 all ; 201–10 000 step 50 |
| `/diviseurs-sitemap.xml` | `createDiviseursSitemap()` | n ≤ 1 000 all ; 1 001–10 000 step 50 ; 10 001–1 000 000 step 500 |
| `/ecrirechiffre-sitemap.xml` | `createEcrireSitemap()` | VIP number list |
| `/commentonditchiffre-sitemap.xml` | `createCommentOnDitSitemap()` | VIP number list |

---

## URL Count Estimates

> Large sitemaps (> 50,000 URLs) may cause slow generation. Consider splitting into multiple sitemaps if needed.

| Sitemap | Approx. URLs |
|---|---|
| factorielle | ~361 (201 + 160 multiples of 50) |
| diviseurs | ~3 160 (1 000 + 180 + 1 980) |

# Bouts de ficelle — thème WordPress

Thème sur mesure de l'asbl Bouts de ficelle ([boutsdeficelle.be](https://boutsdeficelle.be)).

Le projet est basé sur le boilerplate [WP Twalp](https://github.com/basilevanha/wp-twalp) : Timber (Twig), Vite, Tailwind CSS, Alpine.js et Docker. Le code source vit dans `src/`, WordPress tourne dans `public/` (ignoré par git), et le build produit un thème autonome à déposer sur le serveur.

> Le thème a été migré depuis l'ancien projet DevKinsta (Laravel Mix + SCSS). La migration est faite en deux phases, voir [État de la migration](#état-de-la-migration).

---

## Démarrer

Prérequis : Node.js 18+, pnpm, Composer, Docker Desktop.

```bash
pnpm install && composer install   # si besoin
pnpm run dev                        # Docker + synchro src/ → thème + Vite (HMR)
```

Les URLs (WordPress, Vite, phpMyAdmin) s'affichent au démarrage. Les ports sont définis dans `.env` (`WP_PORT`, `PMA_PORT`). Ils peuvent changer si un port est déjà occupé.

### Première installation sur une nouvelle machine

1. Exporter la base de prod (phpMyAdmin, export **Custom**, compression gzip).
2. Renommer le préfixe des tables, qui n'est pas `wp_` en prod :
   ```bash
   gunzip -c ~/Downloads/<export>.sql.gz > database/dump-prod.sql
   sed -i '' 's/wp_1105764_/wp_/g' database/dump-prod.sql
   ```
3. `pnpm run setup` : slug `bouts-de-ficelle`, choisir de restaurer `dump-prod.sql`, ACF oui, Loco non.
4. Vérifier après le setup :
   - la version de WordPress doit être au moins celle de la prod, sinon :
     `docker compose -f docker/docker-compose.yml --env-file .env exec wordpress wp --allow-root core update`
   - la page d'accueil doit être la page **Accueil** d'origine (ID 10). Le setup en crée une vide :
     `… wp --allow-root option update page_on_front 10`
5. Récupérer `wp-content/uploads/` depuis le serveur (FTP) et le placer dans `public/wp-content/uploads/`.
6. Installer les plugins :
   ```bash
   docker compose -f docker/docker-compose.yml --env-file .env exec wordpress wp --allow-root \
     plugin install wp-event-manager ninja-forms wordpress-seo classic-editor foogallery honeypot
   ```
   Ne pas installer **Fluent SMTP** en local : il enverrait de vrais emails.

### Commandes

| Commande | Rôle |
| --- | --- |
| `pnpm run dev` | Serveur de dev (Docker + synchro + Vite) |
| `pnpm run build` | Build de production dans `public/wp-content/themes/bouts-de-ficelle/` |
| `pnpm run sprite` | Regénère le sprite SVG depuis `src/icons/` |
| `pnpm run dump` | Exporte la base locale dans `database/` |
| `pnpm run import` | Importe un dump (voir les limites ci-dessous) |
| `pnpm run stop` | Arrête les conteneurs |

---

## Structure

```
src/
├── theme/              # PHP du thème (copié dans public/wp-content/themes/bouts-de-ficelle/)
│   ├── functions.php
│   ├── inc/
│   │   ├── context.php     # Contexte Timber global + recherche de pages par template
│   │   ├── events.php      # WP Event Manager : synchro des metas, événements liés
│   │   ├── analytics.php   # Google Tag Manager (désactivé en dev)
│   │   └── vite.php, acf.php, icon.php, timber.php, cleanup.php, i18n.php  # WP Twalp
│   ├── src/StarterSite.php # Supports du thème, menus, options Twig
│   └── page-*.php, single-*.php, …
├── views/              # Twig
│   ├── layouts/base.twig
│   ├── templates/      # Un fichier par template PHP
│   ├── partials/       # Header, footer, sections (image-text, cards-list…)
│   ├── modules/        # Blocs (card, highlight, modal, paragraph…)
│   ├── components/     # Bouton
│   └── atoms/          # Image responsive, macro d'icône
├── scss/               # Styles historiques (BEM + sass-mq), compilés par Vite
├── css/main.css        # Tailwind (thème + utilitaires, sans preflight)
├── js/                 # main.js + composants historiques (Manager)
├── acf-json/           # Groupes de champs et types de contenu ACF (versionnés)
├── icons/              # SVG → sprite
├── fonts/ images/
```

---

## Conventions à connaître

### Pages retrouvées par leur template, pas par ID

Le code ne contient plus d'ID de page ni de menu. Les pages clés sont retrouvées par leur **template de page** avec `get_page_id_by_template()` ([inc/context.php](src/theme/inc/context.php)) :

| Template | Utilisé pour |
| --- | --- |
| `page-accueil.php` (Accueil) | Page d'accueil **et** champs globaux : logo, réseaux sociaux, footer, événement à la une, copyrights |
| `page-ateliers.php` (Ateliers) | Liens « Ateliers » (fil d'Ariane, tags d'événement) |
| `page-spectacles.php` (Spectacles) | Liens « Spectacles » |

⚠️ **Une seule page par template.** S'il y en a plusieurs, c'est la plus ancienne (le plus petit ID) qui est utilisée.

### Menus

Deux emplacements sont déclarés : `header` et `footer`. Tant qu'aucun menu n'y est assigné, le thème prend les menus par leur slug, **Header** (`header`) et **Navigation** (`navigation`).

→ Assigne-les dans *Apparence → Menus* (en local et en prod). Sans ça, un menu renommé disparaît.

### ACF

- Les groupes de champs et les types de contenu (Ateliers, Spectacles, Membres de BDF) sont dans `src/acf-json/`. Ces fichiers ont **priorité** sur la base.
- Les règles d'affichage utilisent le **template de page** (`page_template == page-accueil.php`), pas un ID.
- **Modifier les champs uniquement en local.** ACF réécrit alors le JSON dans `src/acf-json/` : il reste à le commiter. Modifier un groupe dans l'admin de prod écrirait dans le thème en ligne et l'écarterait du dépôt.
- En prod, ACF peut afficher « Synchronisation disponible » : ce n'est pas bloquant, le site lit les JSON.

### Événements (WP Event Manager)

- Un événement est rattaché à un atelier ou un spectacle par le champ ACF `type_name` (lien), et son type est défini par `event-type` (`atelier` / `spectacle`).
- À l'enregistrement, `inc/events.php` recopie les dates ACF dans les metas de WP Event Manager (heures, expiration, bannière).

### Twig

- `autoescape` est **désactivé** (voir `StarterSite::update_twig_environment_options`). Les templates affichent du HTML WordPress/ACF tel quel. Avec l'échappement activé, les titres déjà encodés (`&#8217;`) seraient encodés une seconde fois.
- Les liens vers les contenus utilisent `post.link` (permalien), jamais `post.guid`.

### Icônes

- Les SVG de `src/icons/` sont assemblés en un sprite `assets/icons/sprite.svg`, avec des ids `icon-<nom>`.
- Les templates historiques utilisent la macro `atoms/icon.twig` : `icon.sprite('time', 20, 20, 'label', site.theme.link)`.
- Pour le nouveau code, utiliser plutôt la fonction WP Twalp : `{{ icon('time', 'h-5 w-5 text-primary') }}`.

### Styles

- Le site est encore stylé par le **SCSS historique** (`src/scss/app.scss`), qui contient son propre reset.
- Tailwind est chargé **sans preflight** : seulement les variables du thème et les utilitaires. Les couleurs de la charte sont déclarées dans `src/css/main.css` (`primary`, `secondary`, `tertiary`, `dark`, `light`…).
- ⚠️ Le SCSS n'est dans aucun layer, donc il **gagne sur les utilitaires Tailwind**. Une classe Tailwind ne surcharge pas une propriété déjà définie en SCSS.
- Les `url()` du SCSS sont **absolues depuis `src/`** (`/fonts/…`, `/images/…`) : Vite les résout et les intègre au build.

### Google Tag Manager

L'ID est dans [inc/analytics.php](src/theme/inc/analytics.php). Le script n'est pas injecté quand le serveur Vite tourne (`dist/hot` présent), pour ne pas fausser les statistiques.

---

## Déployer

1. **Arrêter `pnpm run dev`.** Sinon `dist/hot` reste présent et le site chargerait ses assets depuis `localhost:5173`.
2. `pnpm run build`
3. Vérifier `public/wp-content/themes/bouts-de-ficelle/`. La synchro ne supprime jamais rien : retirer les fichiers qui n'existent plus dans `src/`. Le dossier ne doit pas contenir `autoload-path.php` (le build le supprime) ni `dist/hot`.
4. Garder une copie du thème en ligne, puis remplacer le dossier `wp-content/themes/bouts-de-ficelle/` du serveur (FTP).
5. Après un premier déploiement : assigner les menus aux emplacements, et vider le cache (WP Super Cache) si besoin.

Le thème déployé contient `vendor/` (Timber), `dist/` (CSS/JS hashés), `acf-json/`, `views/` et les assets. Aucun fichier de développement n'y est copié.

---

## État de la migration

### Phase 1 — fait

Le thème a été déplacé dans la structure WP Twalp, avec un rendu identique à la prod : texte, balisage HTML et sélecteurs CSS vérifiés page par page. Les changements par rapport à l'ancien dépôt :

- Les IDs écrits en dur (`DEV = 9 | STAGING = 10`, menus 35/36) sont remplacés par la recherche par template et les emplacements de menu.
- Les règles ACF passent de `page=ID` à `page_template`. L'export ACF vient de la base de prod, plus à jour que les anciens fichiers `jsons/`.
- Les liens basés sur le `guid` sont remplacés par des permaliens.
- `functions.php` est découpé dans `inc/`. Le code en double de `single-atelier.php` et `single-spectacle.php` est regroupé dans `get_linked_events()`.
- Le fuseau horaire vient de `wp_timezone()`. Des vérifications évitent les warnings PHP 8 quand aucun événement n'est mis à la une.
- Supprimés : `sidebar.php`, Laravel Mix, `static/`, les tests PHPUnit, les exports `jsons/`.

### Phase 2 — à faire

1. **JS → Alpine.js** : header (masqué au scroll, burger), accordéons du footer (`x-collapse`), modale (`x-trap` du plugin focus), filtre des cartes, images. Supprimer ensuite `src/js/views/` et le Manager. `evenements.js` ne sert à rien et peut être supprimé dès maintenant.
2. **SCSS → Tailwind**, partial par partial, en supprimant chaque fichier SCSS une fois converti. À la fin : `@import "tailwindcss";` (avec preflight), puis retirer `sass-embedded`, `sass-mq` et les options SCSS de `vite.config.js`.
3. **Champs globaux** : les déplacer de la page Accueil vers la page privée **Réglages du site** (`site-settings`, prévue par WP Twalp). Il faudra recopier les valeurs une fois.
4. **Nettoyage de la prod** : tables de plugins inutilisés (WooCommerce, Fluent Forms, WPForms, Formidable, Jetpack…).

---

## Limites connues des scripts WP Twalp

Ces points sont à corriger dans le template [wp-twalp](https://github.com/basilevanha/wp-twalp). Le premier est déjà corrigé ici.

- ✅ `sync.js --production` laissait `autoload-path.php` dans le thème : le site plantait une fois déployé.
- `pnpm run build` ne nettoie pas le dossier du thème : des fichiers supprimés de `src/` restent dans le build.
- `pnpm run import` masque les erreurs MySQL et ne vide pas la base. Un dump sans `DROP TABLE` échoue en silence, alors que le script affiche « imported ». Vider la base avant d'importer :
  ```bash
  docker compose -f docker/docker-compose.yml --env-file .env exec -T db sh -c \
    'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DROP DATABASE $MYSQL_DATABASE; CREATE DATABASE $MYSQL_DATABASE CHARACTER SET utf8mb4;"'
  ```
- `import.sh` peut affirmer que les conteneurs sont arrêtés alors qu'ils tournent : `set -o pipefail` combiné à `grep -q` fait échouer la vérification.
- `pnpm run reset` peut échouer sans rien dire (`docker compose down -v` sous `|| true`) : vérifier dans Docker Desktop que les conteneurs et le volume ont bien disparu.
- `pnpm run setup` crée une page d'accueil vide même quand un dump est restauré.
- `src/theme/acf-json/` n'est pas visible par Docker : seul `src/acf-json/` est monté dans le conteneur.
- L'image Docker fournit WordPress 6.7 : mettre à jour le cœur si la base vient d'une version plus récente.

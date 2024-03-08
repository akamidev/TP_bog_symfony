# Supatalks

## Création du projet Symfony

La commande suivante permet de créer un projet Symfony :

```bash
symfony new supatalks --webapp
```

## Lancez le serveur web

```bash
symfony server:start
```

autre option pour lancer le serveur sans les logs dans le terminal :

```bash
symfony server:start -d
```

## Création d'une page d'accueil

```bash
symfony console make:controller Home
```

On met à jour le fichier `src/Controller/HomeController.php` pour ajouter une méthode `index` :

```php
#[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Supatalks',
        ]);
    }
```

## Personalisation de la page d'accueil

Tout d'abord nous avons mis en place le framework css Bootstrap. En  important les fichiers CSS et JS dans le dossier "assets" afin de les configurer dans le système d'AssetMapper de Symfony.

```bash
assets/
├── css/
│   ├── bootstrap.min.css
│   └── bootstrap.min.css.map
└── js/
│   ├── bootstrap.bundle.min.js
    └── bootstrap.bundle.min.js.map
```

Dans le fichier `app.js` :
    
```javascript
    import './js/bootstrap.bundle.min.js';
    import './styles/bootstrap.min.css';
```

Concernant l'ajout des police d'écriture ou de librairie d'icônes, nous avons ajouté le tout dans le fichier `base.html.twig` :

```html
    <head>
        //...
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Unbounded:wght@200..900&display=swap" rel="stylesheet">
        //...
    </head>
```

## Préparation de l'architecture des entités

On se rend sur une app comme `https://dbdiagram.io/d/Supatalks-65e6dd07cd45b569fb8c90f0` pour créer un schéma de base de données.

On se retrouve pour le moment avec 2 entités : `Event` et `Speaker`.

## Création des entités

Pour créer les entité et les mettre en place dans la base de données, on utilise les commandes suivantes :

```bash
symfony console make:entity Event
symfony console make:entity Speaker
```

On suit les instructions pour créer les propriétés des entités.

Point important concernant la relation entre les entités `Event` et `Speaker` : un événement peut avoir plusieurs speakers et un speaker peut participer à plusieurs événements non simultanés. On se retrouve donc avec une relation `OneToMany` entre les deux entités. De préférence à du `ManyToMany` qui ne nous convient pas dans ce cas précis.

## Création de la base de données

Premièrement, nous avons besoin de configurer la base de données dans le fichier `.env`. Dans notre on a utilisé une base de données SQLite :

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/supatalks.db"
```

On créé la base de données avec la commande suivante `symfony console doctrine:database:create`. Ce qui nous permet de créer la base de données `supatalks.db` dans le dossier `var`.

## Migration des entités

Pour faire une migration, cela se passe en deux étapes :

1. Création de la migration :

```bash
symfony console make:migration
```

2. Exécution de la migration :

```bash
symfony console doctrine:migrations:migrate
```

Note importante : dès lors que l'on modifie ou ajoute une propriété à une entité, il faut refaire une migration pour mettre à jour la base de données. Car le shéma de la base de données est généré à partir des entités et que Doctrine ne pourra pas faire son travail d'ORM correctement.


## Création des fixtures

Pour les fixtures en dtail rdv ici : [SymGuide](https://docs.symguide.com/fixtures)

Le contenu de notre fichier `src/DataFixtures/AppFixtures.php` :

```php
<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Event;
use App\Entity\Speaker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Initialisation de Faker

        // Création de 40 speakers
        $speakerArray = [];
        for ($i=1; $i < 41; $i++) { 
            $speaker = new Speaker();
            $speaker->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setJob($faker->jobTitle)
                ->setCompany($faker->company)
                ->setExperience($faker->numberBetween(1, 20))
                ->setImage('https://randomuser.me/api/portraits/men/'. $i .'.jpg')
                ;
                array_push($speakerArray, $speaker);
                $manager->persist($speaker);
        }

        // Tableau de 20 événements
        $events = [
            'Frontend Masters',
            'Backend Masters',
            'Truth about PHP',
            'Symfony 7, what\'s new ?',
            'React, framework or library ?',
            'Vue.js, the new challenger ?',
            'Angular, the old one ?',
            'Web components, the future ?',
            'WebAssembly, how to use it ?',
            'GPDR, how to be compliant ?',
            'Docker, the new way to deploy ?',
            'Kubernetes, scale like a boss ?',
            'AWS, the cloud leader ?',
            'Wordpress, still alive ?',
            'PHP 8.3, what\'s new ?',
            'UI/UX, for frontend developers',
            'API, the essential for frontend',
            'GraphQL vs REST, which is better ?',
            'Web security, how to protect ?',
            'Web performance, how to optimize ?',
        ];

        // Boucle pour créer 20 événements
        foreach ($events as $item) {
            $event = new Event();
            $event->setName($item)
                ->setTheme('Web development')
                ->setDate($faker->dateTimeBetween('-6 months', '+6 months'))
                ->setLocation($faker->city)
                ->setAttendee($faker->numberBetween(10, 100))
                ->setPrice($faker->numberBetween(0, 250))
                ->addSpeaker($speakerArray[$faker->numberBetween(0, 39)])
                ;
            $manager->persist($event);
        }
        
        $manager->flush();
    }
}
```

Avec cette fixture, on crée 40 speakers et 20 événements. On utilise Faker pour générer des données aléatoires. Afin de charger les fixtures dans la base de données, on utilise la commande suivante :

```bash
symfony console doctrine:fixtures:load
```

## Mise en place du back-office

Pour mettre le back-office en place, on utilise EasyAdmin. L'instalaation se fait avec la commande suivante :

```bash
composer require easycorp/easyadmin-bundle
```

Suite à cela on configure le tableau de bord (Dashboard) avec la commande suivante :

```bash
symfony console make:admin:dashboard
```

Puis on se rend dans le  fichier de controlleur généré "DashboardController.php" :

```php
   #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 3. Vous pouvez rendre un modèle personnalisé pour afficher un tableau de bord approprié avec des widgets, etc. 
        // (astuce : c'est plus facile si votre modèle s'étend de @EasyAdmin/page/content.html.twig)
        // Alors créez un fichier twig dans le dossier templates/admin/dashboard.html.twig et étendez-le de @EasyAdmin/page/content.html.twig
        return $this->render('admin/dashboard.html.twig');
    }
```

### Lier les entités à EasyAdmin

Pour lier les eentités de votre choix avec le back-office, on utilise la commande suivante :

```bash
symfony console make:admin:crud
```

Il suffit de sélection l'entité dans la liste proposée et de suivre les instructions.
Une fois fait, afin d'afficher l'entité dans le dashboard, on modifie le "DashboardController.php", au niveau de la méthode `configureMenuItems()` :

```php
    // ...
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Event::class);
    // ...
```

### Personnalisation de l'inteface d'EasyAdmin
### Personnalisation des formulaires

## Mise en place d'un système d'utilisateur

Pour mettre en place un système d'utilisateur, on tout d'abord créé un entité `User` avec la commande suivante :

```bash
symfony console make:user
```

Note importante, il ne faut jamais créer une entité d'utilisateur autrement, si on souhaite mettre à jour l'entité `User` il faut utiliser la commande habituelle `symfony console make:entity User` et ajouter les propriétés que l'on souhaite.

## Mise en place d'un système d'authentification

Tout comme plusieurs outils dans un projet symfony, le CLI nous facilite l'expérience de développement avec des commandes dédiées. Pour mettre en place un système d'authentification, on utilise la commande suivante :

```bash
symfony console make:auth
```

On répond aux questions posées par la commande et on se retrouve avec un système d'authentification complet. Pour y accéder, on se rend sur la route `/login`. On y trouvera un formulaire de connexion complet.

Les fichier créés par la commande sont :

- `src/Security/AppAuthenticator.php`
- `src/Controller/SecurityController.php`
- `templates/security/login.html.twig`

Notez que la nomination des fichiers peut varier en fonction des réponses que vous avez donné lors de la création du système d'authentification. Exemple : `AppAuthenticator` peut être `UserAuthenticator`.

### Créer un utilisateur Admin

Dans le cas de Supatalks, il s'agit d'une application métier, ce qui signifie que la possibilité de créer n'est pas ouverte au public. On a donc besoin de créer un utilisateur admin. Pour cela, on dois hasher le mot de passe de l'utilisateur admin. On utilise la commande suivante :

```bash
symfony console security:hash-password
```

Attention, lors de la création du mot de passe hasher, le terminal n'affichera pas le mot de passe en clair. Il faudra ensuite le copier et le coller le "Password hash" dans le fichier `src/DataFixtures/AppFixtures.php` :

```php
    // Création d'un utilisateur admin
        $user = new User();
        $user->setEmail('admin@admin.fr')
            ->setPassword('$2y$13$NJpGg/WaTYG0ONkZkf6tvuPVmkuexwRQqozQKsp5b8yc9z9B3ziMG') // admin
            ->setRoles(['ROLE_ADMIN'])
            ;
        $manager->persist($user);
```

Désormais, vous pouvez lancer la commande `symfony console doctrine:fixtures:load` pour mettre à jour la base de données avec l'utilisateur admin. Puis vous pouvez vous connecter avec les identifiants renseignés.

Lors de votre première connexion, il se peut que vous rencontriez une erreur dûe au faite que la route de redirection n'ai pas été configuré dans le fichier `src/Security/AppAuthenticator.php`. Rendez-vous dans le fichier à la méthode `onAuthenticationSuccess()` et ajoutez la route de redirection :

```php
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('admin'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }
```

### Limiter l'accès à certaines pages

Afin de limiter l'accès à certaine de l'application pour un type d'utilisateur spécifique, on utilise les paramètres du fichier `security.yaml`. Dans la zone "access_control", on ajoute les routes que l'on souhaite protéger avec le rôle qui est autorisé à y accéder :

```yaml
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

Désormais, seuls les utilisateurs avec le rôle `ROLE_ADMIN` pourront accéder à la route `/admin`. Dans le cas où un utilisateur anonyme tente d'accéder à cette route, il sera redirigé vers la route `/login`. Concernant un utilisateur authentifié mais n'ayant pas le rôle `ROLE_ADMIN`, il tombera sur une route 403 (Forbidden).
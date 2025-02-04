<?php
$message = "";

$name    = "";
$email   = "";
$body    = "";

$address = "image@isima.fr";

if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");

function check_captcha()
{
    $secretKey = "6LcUrKAUAAAAABuH4BQwfv_ZwpxfZe38nfd3XNE2";
    $ip = $_SERVER['REMOTE_ADDR'];

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $secretKey, 'response' => $captcha);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseKeys = json_decode($response, true);

    return $responseKeys["success"] && $responseKeys["score"] > 0.6;
}

if ($_POST)
{
    $name     = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $body     = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
    $captcha  = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    if (trim($name) === '') {
        $message = 'Merci de préciser votre nom.';
    } else if (trim($email) === '') {
        $message = 'Merci de préciser votre adresse email.';
    } else if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
        $message = 'Cette adresse email semble invalide.';
    } else if (trim($body) === '') {
        $message = 'Merci de préciser votre message.';
    } else if (check_captcha()) {
        $message = "La vérification de votre comportement a échouée,";
    } else {
        $e_subject = 'Vous avez été contacté par ' . $name . '.';
        $e_body = "Vous avez été contacté sur le site par $name. Le message est le suivant." . PHP_EOL . PHP_EOL;
        $e_content = "\"$body\"" . PHP_EOL . PHP_EOL;
        $e_reply = "Vous pouvez contacter $name par mail, $email";
    
        $msg = wordwrap( $e_body . $e_content . $e_reply, 70 );
    
        $headers = "From: $email" . PHP_EOL;
        $headers .= "Reply-To: $email" . PHP_EOL;
        $headers .= "MIME-Version: 1.0" . PHP_EOL;
        $headers .= "Content-type: text/plain; charset=utf-8" . PHP_EOL;
        $headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;
    
        if (mail($address, $e_subject, $msg, $headers)) {
            $message = "Le mail a été envoyé avec succès.";
        } else {
            $message = "Une erreur s’est déroulée lors de l’envoie du mail.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0e509d">
    <meta name="description" content="Site officiel de image, l’association entreprise de l’ISIMA">
    
    <title>Association Im@ge</title>

    <link rel="icon" href="img/icon.svg" type="image/svg+xml">

    <link rel="stylesheet" type="text/css" href="style/main.css">
 
    <script src="https://www.google.com/recaptcha/api.js"></script>

    <script>
        let header = null;
        const HEADER_SCROLL_FACTOR = 1 / 4;

        function refreshScrollState() {
            if (header != null) {
                let scroll_to = window.innerHeight * HEADER_SCROLL_FACTOR;
                let scroll_pos = window.scrollY;

                let pos = Math.min(Math.max(0, scroll_pos / scroll_to), 1);

                header.style.setProperty("--offset", -pos + "s");
            }
        }

        window.addEventListener("load", () => {
            document.body.classList.add("with-js");

            header = document.getElementsByTagName("header")[0];

            refreshScrollState();
        });

        window.addEventListener("resize", refreshScrollState);
        window.addEventListener("scroll", refreshScrollState);

        // Recaptcha support
        function onSubmit(token) {
            document.getElementById("contact-form").submit();
        }
    </script>
</head>
<body>
    <header>
        <div class="size-limited">
            <svg viewBox="0 0 154 56" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <title>Association Image</title>
                <path id="main" d="M 7.2258064,27.277419 14.993548,22.941935 V 43.354838 H 7.2258064 Z M 31.070968,26.374193 c 0.903225,0.903226 0.903225,1.987097 0.903225,2.890323 l -1e-6,14.090322 h 5.419354 l 2e-6,-15.174193 c 2.167742,-2.167742 5.761081,-3.813112 7.767742,-1.806452 0.903226,0.903226 0.903226,1.987097 0.903226,2.890323 l -4e-6,14.090322 h 5.419355 l 4e-6,-13.548387 c 0,-6.864516 -3.256218,-7.936262 -4.696775,-8.490322 -2.348387,-0.903226 -6.864516,-0.722581 -10.11613,3.612903 -1.369628,-2.895133 -3.793547,-3.974193 -6.683869,-3.974193 -2.845789,0 -5.513105,1.519597 -7.225809,3.612903 L 22.4,21.496774 h -4.51613 v 21.858064 h 5.419354 l 2e-6,-15.174193 c 2.167742,-2.167742 5.761081,-3.813112 7.767742,-1.806452 z m 84.903222,19.329032 c -0.84577,3.171656 -3.79355,4.335484 -7.58709,4.335484 -3.07097,0 -4.69678,-0.36129 -6.50324,-0.903225 l 1e-5,4.154838 c 2.70968,1.083871 5.6,1.083871 7.58709,1.083871 5.60001,0 11.83227,-2.348387 11.83227,-10.116129 0,0 0,-22.76129 -1e-5,-22.76129 h -4.7871 l -0.18064,2.529032 c -1.44516,-1.625806 -3.07096,-3.070967 -6.14193,-3.070967 -5.23871,0 -9.5742,4.516129 -9.5742,11.199999 0,8.309678 4.33549,11.741936 9.03226,11.741936 2.89032,0 4.87742,-1.083871 6.32258,-2.348387 m -4.87742,-16.258065 c 1.80646,0 3.43226,0.903226 4.87742,2.348387 v 9.754839 c -0.72258,0.722581 -2.34838,2.167742 -4.87742,2.167742 -2.89032,0 -4.87742,-1.987097 -4.87742,-7.045161 0,-3.793549 1.26452,-7.225807 4.87742,-7.225807 z m 34.14193,13.187097 c -1.98709,0.541936 -4.33548,1.083871 -6.86451,1.083871 -4.87742,0 -6.86451,-3.070968 -6.86451,-5.6 h 14.27096 c 0.54194,-3.432258 0,-7.948387 -2.89032,-10.83871 -1.64163,-1.502371 -4.07351,-2.184 -6.354,-2.184 -3.07097,0 -5.11517,0.653531 -7.37503,3.087226 -2.34839,2.529033 -3.10681,5.419202 -3.09969,7.932129 0.0287,10.132387 6.17066,11.938839 11.40937,11.938839 4.17291,0 6.32258,-0.722581 7.76774,-1.264516 l -1e-5,-4.154839 m -13.36773,-8.670968 c 1e-5,-2.709677 2.16774,-4.696774 4.51613,-4.696774 2,0 3.97419,1.445162 3.97419,4.696774" />
                <path id="second" d="m 7.2258064,12.645161 h 7.7677416 v 5.96129 L 7.2258064,22.941935 Z M 79.818359,5.96875 C 76.921978,5.9558533 73.562736,6.5046675 69.636719,7.5566406 53.932649,11.764533 50.100702,18.403354 54.308594,34.107422 58.516486,49.811489 65.153354,53.643439 80.857422,49.435547 96.561491,45.227655 100.39343,38.590788 96.185547,22.886719 93.029628,11.108669 88.507504,6.00744 79.818359,5.96875 Z m -3.947265,5.230469 c 9.393539,0 16.077666,7.763433 16.078125,17.162109 2.03e-4,4.154835 -1.626111,8.850796 -2.890625,10.837891 h -9.935547 l -0.181641,-1.625 c -1.625805,1.264514 -3.430751,1.804386 -4.333984,1.804687 -8.129019,0.0027 -8.13086,-8.307788 -8.13086,-9.933594 0,-5.961284 3.433737,-10.627926 8.195313,-10.65039 1.741598,-0.0082 2.643693,0.352673 3.908203,1.617187 v -1.263671 h 4.517578 v 16.439453 h 3.070313 c 0.54194,-1.445161 1.263672,-3.433976 1.263672,-7.046875 0,-9.032249 -5.599263,-13.185547 -11.560547,-13.185547 -9.574184,0 -12.644532,8.670418 -12.644532,13.367187 0,7.58709 4.515431,13.910157 11.921876,13.910156 3.524726,0 4.335328,-0.361405 7.767578,-1.083984 v 4.335938 c -2.34838,0.541934 -4.878217,0.722656 -7.587891,0.722656 -10.658054,0 -16.619141,-8.671833 -16.619141,-17.523438 0,-9.935474 6.91825,-17.884765 17.16211,-17.884765 z m -0.361328,11.380859 c -3.25161,0 -4.154297,3.251339 -4.154297,6.683594 0,4.33548 1.083293,6.324219 3.792969,6.324219 1.445159,0 2.52842,-0.904373 3.43164,-1.626953 V 23.664062 c -0.903225,-0.903225 -1.986442,-1.083984 -3.070312,-1.083984 z" />
            </svg>
        
            <nav>
                <div id="button" tabindex="0" aria-label="Menu de la page">
                    <svg viewBox="0 0 24 24">
                        <title>Menu</title>
                        <rect x="0" y="0" width="24" height="2" />
                        <rect x="0" y="11" width="24" height="2" />
                        <rect x="0" y="22" width="24" height="2" />
                    </svg>
                </div>

                <ul>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#team">Notre Équipe</a></li>
                    <li><a href="#clients">Nos clients</a></li>
                    <li><a href="#partners">Nos partenaires</a></li>
                    <li><a href="#testimonials">Témoignages</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="banner">
        <h1>Bienvenue sur le site d’<abbr title="image">IM@GE</abbr></h1>

        <p>L’association entreprise de l’ISIMA Clermont-Ferrand</p>

        <a href="#about">En savoir plus</a>
    </section>

    <section id="about" class="alt">
        <div class="size-limited">
            <h1>Nous sommes à vos côtés pour tous vos projets!</h1>
    
            <p>Nous réalisons vos projets de sites web, d’applications web, de logiciels, d’applications mobiles, de Data
                Science, de formations… De façon efficace et professionnelle.</p>
        </div>

        <div id="image" class="grid-container">
            <section>
                <h2>Notre identité</h2>
                <p>Nous sommes une association-entreprise. C’est-à-dire que nous sommes une association à but non lucratif mais aussi une entreprise avec un numéro de SIRET.</p>
            </section>
            <section>
                <h2>Notre fonctionnement</h2>
                <p>En plus des membres permanents, nous embauchons des étudiants pour la réalisation technique de vos projets.</p>
            </section>
            <section>
                <h2>Mission pédagogique</h2>
                <p>Notre mission est une mission pédagogique. Nous apportons du concret à la formation d’excellence de l’école d’ingénieur ISIMA.</p>
            </section>
            <section>
                <h2>Marque de fabrique</h2>
                <p>Notre motivation est de rendre un travail le plus professionnel et de la meilleure qualité possible.</p>
            </section>
            <section>
                <h2>Événements</h2>
                <p>Nous sommes surtout une association étudiante. Nous organisons des évènements pour promouvoir le web, l’innovation, l’entreprenariat.</p>
            </section>
            <section>
                <h2>Notre fierté</h2>
                <p>Nous sommes fiers d’acquérir une première expérience professionnelle qui complète notre formation technique d’ingénieurs.</p>
            </section>
        </div>
    </section>

    <section id="team">
        <h1>Notre équipe</h1>

        <div class="grid-container">

            <figure>
                <img src="img/team/Hicham_El-Maadoudi.jpg" alt="">

                <figcaption>
                    <h2>Hicham  El-Maadoudi</h2>
                    <h3>Président</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/photo_placeholder.jpg" alt="">


                <figcaption>
                    <h2> Yassir Masfour</h2>
                    <h3>Vice-président</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/Rim_Nazih.png" alt="">

                <figcaption>
                    <h2>Rim Nazih</h2>
                    <h3>Secrétaire</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/Loïc_Costiou.jpg" alt="">

                <figcaption>
                    <h2>Loïc Costiou</h2>
                    <h3>Trésorier</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/photo_placeholder.jpg" alt="">

                <figcaption>
                    <h2>Axelle Combe</h2>
                    <h3>Vice-trésorière</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/photo_placeholder.jpg" alt="">

                <figcaption>
                    <h2>Esteban Melero</h2>
                    <h3>Responsable communication</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/team/photo_placeholder.jpg" alt="">

                <figcaption>
                    <h2>Maxence Chutaux</h2>
                    <h3>Responsable serveur</h3>
                </figcaption>
            </figure>
            <figure>
                <img src="img/team/walidpic_1.jpg" alt="">

                <figcaption>
                    <h2>Walid Chakib</h2>
                    <h3>Chef de projet</h3>
                </figcaption>
            </figure>
	    <figure>
		<img src="img/team/photo_placeholder.jpg" alt="">
		<figcaption>
		  <h2> Antoine Gueleraud </h2>
		  <h3> Membre de l'Anelis </h3>
		</figcaption>
	    </figure>
        </div>
    </section>

    <section id="clients">
        <h1>Ils nous font confiance</h1>

        <div class="grid-container">
            <figure>
                <img src="img/orgs/michelin.webp" alt="">

                <figcaption>
                    <h2>Michelin</h2>
                    <h3>Applications web</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/orgs/enedis.webp" alt="">

                <figcaption>
                    <h2>ENEDIS</h2>
                    <h3>Applications web</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/orgs/systemes-solaires.webp" alt="">

                <figcaption>
                    <h2>Systèmes solaires</h2>
                    <h3>Applications web</h3>
                </figcaption>
            </figure>

            <figure>
                <img style="background-color: #f4f4f4" src="img/orgs/cdad63.webp" alt="">

                <figcaption>
                    <h2>CDAD63</h2>
                    <h3>Site internet</h3>
                </figcaption>
            </figure>

            <figure>
                <img src="img/orgs/dafy.webp" alt="">

                <figcaption>
                    <h2>Dafy Moto</h2>
                    <h3>Plateforme web de gestion des véhicules</h3>
                </figcaption>
            </figure>
        </div>
    </section>

    <section id="partners">
        <h1>Ils nous aident à avancer</h1>

        <div class="grid-container">
            <a href="https://www.credit-agricole.fr/ca-centrefrance">
                <figure>
                    <img src="img/sponsors/cacf.webp" alt="">
    
                    <figcaption>
                        <h2>Crédit Agricole</h2>
                        <h3>Centre France</h3>
                    </figcaption>
                </figure>
            </a>

            <a href="http://www.iesf-auvergne.fr/">
                <figure>
                    <img src="img/sponsors/iesf.webp" alt="">
    
                    <figcaption>
                        <h2>IESF</h2>
                        <h3>Auvergne</h3>
                    </figcaption>
                </figure>
            </a>

            <a href="https://www.isima.fr">
                <figure>
                    <img src="img/sponsors/isima.webp" alt="">
    
                    <figcaption>
                        <h2>ISIMA</h2>
                        <h3>Clermont-Ferrand</h3>
                    </figcaption>
                </figure>
            </a>

            <a href="https://www.uca.fr">
                <figure>
                    <img src="img/sponsors/uca.webp" alt="">
    
                    <figcaption>
                        <h2>Université</h2>
                        <h3>Clermont Auvergne</h3>
                    </figcaption>
                </figure>
            </a>
        </div>
    </section>

    <section id="testimonials" class="alt">
        <h1>Ils témoignent</h1>

        <div class="carousel-container">
            <div class="carousel-elements">
                <figure>
                    <blockquote>Flavio et l’équipe d’IMAGE ont su comprendre notre besoin tout en étant réactif. Le projet a été mené avec succès !</blockquote>
                    <figcaption>
                        <span class="name">Cédric Gagnevin</span>
                        <span class="role">Chef de projet, Dafy Moto</span>
                    </figcaption>
                </figure>

                <figure>
                    <blockquote>L'image d'IM@GE, c'est l'image de l'Ecole !</blockquote>
                    <figcaption>
                        <span class="name">Alexandre Guitton</span>
                        <span class="role">Directeur de l'ISIMA Clermont-Ferrand</span>
                    </figcaption>
                </figure>

                <figure>
                    <blockquote>L’équipe de l’association IMAGE a fourni un travail de grande qualité lors de la refonte du site du Conseil Départemental de l’Accès au Droit 63</blockquote>
                    <figcaption>
                        <span class="name">Jean-Claude Pierru</span>
                        <span class="role">Président TGI de Clermont-Ferrand</span>
                    </figcaption>
                </figure>
    
                <figure>
                    <blockquote>Être membre d’IMAGE, c’est un moyen pour les étudiants de gagner une expérience riche dans le monde professionnel</blockquote>
                    <figcaption>
                        <span class="name">Andy Chalendard</span>
                        <span class="role">Chef de projet, Systèmes Informatiques</span>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section id="contact">
        <h1>Prenez contact avec vous</h1>

        <p>Vous avez un projet en informatique? Prenez contact avec nous afin d’obtenir de renseignements et, peut-être, nous pourrons construire le projet avec vous</p>

        <form action="#contact" method="post" id="contact-form">
            <div class="input">
                <input type="text" name="name" id="name" placeholder="Donnez le nom du contact afin de faciliter les échange" value="<?php echo $name; ?>">
                <label for="name">Votre nom</label>
            </div>

            <div class="input">
                <input type="text" name="email" id="email" placeholder="Donnez l’adresse mail que nous pourrons utiliser afin de vous recontacter" value="<?php echo $email; ?>">
                <label for="email">Votre adresse mail de contact</label>
            </div>

            <div class="input full">
                <textarea name="body" id="body" placeholder="Décrivez votre projet en quelque mots afin que nous puissions évaluer de l’intérêt de ce dernier"><?php echo $body; ?></textarea>
                <label for="body">Votre idée de projet</label>
            </div>
            
            <?php if ($message != "") {
                ?><div id="form-message" class="full"><?php echo $message; ?></div><?php
            } ?>

            <div class="buttonset">
                <button class="g-recaptcha"
                        data-sitekey="6LcUrKAUAAAAAE29ZZ0TURyvC4up2KssaVa_LFfN"
                        data-callback="onSubmit"
                        data-action="submit">Envoyer le message</button>
            </div>
        </form>
    </section>

    <footer>
        <nav>
            <section id="find">
                <h2>Nous trouver</h2>
    
                <address>
                    <iframe aria-hidden="true" title="Carte interactive de la position de l’association" src="https://www.openstreetmap.org/export/embed.html?bbox=3.1100133061409%2C45.75868163450387%2C3.112067878246308%2C45.75979693527641&amp;layer=mapnik&amp;marker=45.75923928767679%2C3.1110405921936035"></iframe>
                    <div>
                        <p>ISIMA – Campus Universitaire des Cézeaux</p>
                        <p>1 rue de la Chebarde</p>
                        <p>63173, Aubière</p>
                    </div>
                </address>
            </section>
    
            <section id="services">
                <h2>Nos services</h2>
    
                <ul>
                    <li>Sites Web</li>
                    <li>Applications web</li>
                    <li>Formations</li>
                    <li>Logiciels</li>
                    <li>Data Science</li>
                </ul>
            </section>
        </nav>

        <section id="copy">
            <div class="size-limited">
                <p><abbr title="image">IM@GE</abbr> est une association loi 1901 associée à l’école ISIMA</p>

                <h2>Suivez-nous</h2>

                <ul>
                    <li><a href="https://www.linkedin.com/company/image-isima/">LinkedIn
                        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <title>LinkedIn</title>
                            <path d="m 20.454756,20.45468 h -3.607911 v -6.260547 c 0,-1.847 -0.785196,-2.416666 -1.798004,-2.416666 -1.070473,0 -2.120263,0.805666 -2.120263,2.462706 V 20.45468 H 9.320602 V 8.992453 h 3.469829 v 1.58808 h 0.04603 c 0.349142,-0.705666 1.567861,-1.91036 3.429464,-1.91036 2.013217,0 4.188829,1.19428 4.188829,4.69532 z M 5.349151,7.395387 c -1.148938,0 -2.080215,-0.874387 -2.080215,-2.071054 0,-1.196666 0.931277,-2.072 2.080215,-2.072 1.148805,0 2.080148,0.875334 2.080148,2.072 0,1.196667 -0.931343,2.071054 -2.080148,2.071054 z m 1.803988,13.05064 H 3.545163 V 8.9836 H 7.153139 Z M 22.228377,0 H 1.771574 C 0.793078,0 0,0.79316 0,1.771667 V 22.22832 C 0,23.20684 0.793078,24 1.771574,24 H 22.228377 C 23.206853,24 24,23.20684 24,22.22832 V 1.771667 C 24,0.79316 23.206853,0 22.228377,0" />
                        </svg>
                    </a></li>
                    <li><a href="https://www.facebook.com/ImageIsima/">Facebook
                        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <title>Facebook</title>
                            <path d="M 24,12 C 24,5.37258 18.62742,0 12,0 5.37258,0 0,5.37258 0,12 0,17.98955 4.38823,22.95398 10.125,23.85422 V 15.46875 H 7.07813 V 12 H 10.125 V 9.35625 c 0,-3.0075 1.79152,-4.66875 4.53258,-4.66875 1.3129,0 2.68617,0.23437 2.68617,0.23437 V 7.875 h -1.51317 c -1.4907,0 -1.95558,0.92501 -1.95558,1.87399 V 12 h 3.32812 l -0.53203,3.46875 H 13.875 v 8.38547 C 19.61177,22.95398 24,17.98955 24,12" />
                        </svg>
                    </a></li>
                </ul>
            </div>
        </section>
    </footer>
</body>
</html>
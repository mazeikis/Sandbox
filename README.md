<h3>This is repository for image gallery application built with Symfony 2.</h3>

User information and encrypted passwords, image details ant votes are stored in SQL database using Doctrine as ORM. Login and encryption features are provided by Symfony 2 built-in tools.
Passwords are encrypted automaticly with custom Doctrine Event listeners.
Front end built with Bootstrap v3 (and jQuery as a part of it) and BraincraftedBootstrapBundle, using free templates.
Thanks to Bootstrap v3 mobile first approach, website works at mobile device resolutions just as well as at desktop ones.

If for some reason one would like to try and install this website, here's the short <strong>installation guide</strong>:
<ol>
<li> Clone this repository in a destination directory.</li>
<li> Install composer and run "composer install" in the root directory of the cloned repository.</li>
<li> Edit app/config.yml and app/parameters.yml to Your requirements, make sure Your SQL credentials are setup and server is running.</li>
<li> In the root directory of cloned repository run "php app/console doctrine:schema:update --force" command to create SQL schema.</li>
<li> To start Symfony 2 just run "php app/console server:run" in the root directory of the repository.</li>
</ol>

Links to bundles used:
<ul>
<li><a href="https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle">WhiteOctoberPagerfantaBundle</a></li>
<li><a href="https://github.com/braincrafted/bootstrap-bundle">BraincraftedBootstrapBundle</a></li>
<li><a href="https://github.com/liip/LiipImagineBundle">LiipImagineBundle</a></li>
</ul>

Also, some badges:<br>
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d17fdef0-e292-4c1b-b002-9b2eebfb1ebe/big.png)](https://insight.sensiolabs.com/projects/d17fdef0-e292-4c1b-b002-9b2eebfb1ebe)

<a href="https://codeclimate.com/github/mazeikis/Sandbox"><img src="https://codeclimate.com/github/mazeikis/Sandbox/badges/gpa.svg" /></a>

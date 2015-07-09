Image gallery application built with Symfony 2
User information and encrypted passwords, image details ant votes are stored in SQL database using Doctrine as ORM. Login and encryption features are provided by Symfony 2 built-in tools.
Passwords are encrypted automaticly with custom Doctrine Event listeners.
Front end built with Bootstrap v3 (and jQuery as a part of it) and BraincraftedBootstrapBundle, using free templates.
Thanks to Bootstrap v3 mobile first approach, website works at mobile device resolutions just as well as at desktop ones.

If for some reason one would like to try and install this website, here's the short installation guide:

1) Clone this repository in a destination directory.
2) Install composer and run "composer install" in the root directory of the cloned repository.
3) Edit app/config.yml and app/parameters.yml to Your requirements, make sure Your SQL credentials are setup and server is running.
4) In the root directory of cloned repository run "php app/console doctrine:schema:update --force" command to create SQL schema.
5) To start Symfony 2 just run "php app/console server:run" in the root directory of the repository.


Also, some badges:
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d17fdef0-e292-4c1b-b002-9b2eebfb1ebe/big.png)](https://insight.sensiolabs.com/projects/d17fdef0-e292-4c1b-b002-9b2eebfb1ebe)

<a href="https://codeclimate.com/github/mazeikis/Sandbox"><img src="https://codeclimate.com/github/mazeikis/Sandbox/badges/gpa.svg" /></a>

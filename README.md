# snowtricks
Snowboad tricks blog

Installation instructions : 

1 - If the following tools are not installed on your computer then folow the links below to install them : 
  - Git : https://git-scm.com/ 
  - Composer : https://getcomposer.org/ 
  - Scoop : https://scoop.sh/
  - Symfony CLI : https://symfony.com/download

2 - Start the git bash application 

3 - In the directory of your choice on your web server, clone the github repository, here is the command : git clone https://github.com/gitcmartel/snowtricks.git

4 - Run the following command to get the project dependencies : composer update

5 - Create the mysql database 'snowtricks' and execute this command to create the table structure : php bin/console doctrine:migrations:migrate

6 - Insert the DataFixtures into the database : php bin/console doctrine:fixtures:load

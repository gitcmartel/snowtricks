<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\TricksGroup;
use App\Entity\Tricks;
use App\Entity\Media;
use App\Entity\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use \DateTime;


class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        //Creation of 10 users

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('User'.$i);
            $user->setEmail('user'.$i.'@gmail.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordtest!'.$i));
            $this->addReference('user'.$i, $user);
            $manager->persist($user);
        }

        //Creation of 10 tricks group

        for ($i = 0; $i < 10; $i++) {
            $tricksGroup = new TricksGroup();
            $tricksGroup->setName('TricksGroup'.$i);
            $tricksGroup->setDescription('Squalorem eculei parabat uncosque catenis quisquam Constantio movebantur 
                et haec paene poenales intendebantur tormenta coopertos maestitiam intendebantur nec absolutum 
                maestitiam.'.$i);
            $this->addReference('tricksGroup'.$i, $tricksGroup);
            $manager->persist($tricksGroup);
        }

        $manager->flush();

        //Creation of 40 tricks

        for ($i = 0; $i < 40; $i++) {
            $tricks = new Tricks();
            $user = $this->getReference('user'.rand(0, 9));
            $tricksGroup = $this->getReference('tricksGroup'.rand(0, 9));
            $tricks->setUser($user);
            $tricks->setTricksGroup($tricksGroup);
            $tricks->setName('Tricks'.$i);
            $tricks->setImage('assets/images/img_1.jpg');
            $tricks->setDescription('
                Quam quidem partem accusationis admiratus sum et moleste tuli potissimum esse Atratino datam. 
                Neque enim decebat neque aetas illa postulabat neque, id quod animadvertere poteratis, pudor 
                patiebatur optimi adulescentis in tali illum oratione versari. Vellem aliquis ex vobis robustioribus 
                hunc male dicendi locum suscepisset; aliquanto liberius et fortius et magis more nostro refutaremus 
                istam male dicendi licentiam. Tecum, Atratine, agam lenius, quod et pudor tuus moderatur orationi 
                meae et meum erga te parentemque tuum beneficium tueri debeo.
            '.$i);
            $this->addReference('tricks'.$i, $tricks);
            $manager->persist($tricks);
            
        }

        //Creation of 20 medias

        for ($i = 0; $i < 20; $i++) {
            $media = new Media();
            $tricks = $this->getReference('tricks'.$i);
            $media->setTricks($tricks);
            $media->setPath('assets/images/img_'.rand(2, 5).'.jpg');
            $media->setType('Image');
            $manager->persist($media);
        }

        //Creation of 20 messages

        for ($i = 0; $i < 20; $i++) {
            $message = new Message();
            $user = $this->getReference('user'.rand(0, 9));
            $message->setUser($user);
            $tricks = $this->getReference('tricks'.rand(0, 9));
            $message->setTricks($tricks);
            $message->setCreationDate(new DateTime());
            $message->setContent(
                'Haec et huius modi quaedam innumerabilia ultrix facinorum impiorum bonorumque praemiatrix 
                aliquotiens operatur Adrastia atque utinam semper quam vocabulo duplici etiam Nemesim appellamus: 
                ius quoddam sublime numinis efficacis, humanarum mentium opinione lunari circulo superpositum, 
                vel ut definiunt alii, substantialis tutela generali potentia partilibus praesidens fatis, quam 
                theologi veteres fingentes Iustitiae filiam ex abdita quadam aeternitate tradunt omnia despectare 
                terrena.
            '.$i);
            $manager->persist($message);
        }

        $manager->flush();
    }
}

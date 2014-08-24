<?php

/**
 * @Entity
 * @Table(name="User")
 */
class NewModel extends Base
{
    protected static $em;



    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="string") */
    private $name;

    // /** @ManyToOne(targetEntity="User") */
    // private $author;
    //
    // /** @OneToMany(targetEntity="Comment", mappedBy="article") */
    // private $comments;
    //
    // public function __construct()
    // {
    //     $this->comments = new ArrayCollection();
    // }
    //
    // public function getAuthor() { return $this->author; }
    // public function getComments() { return $this->comments; }
    
    
    
    protected static function em()
    {
        if (empty(self::$em)) {
            require_once "vendor/autoload.php";

            $paths = array("/path/to/entity-files");
            $isDevMode = false;

            // the connection configuration
            $dbParams = array(
                // 'driver'   => 'pdo_mysql',
                // 'user'     => 'root',
                // 'password' => '',
                // 'dbname'   => 'foo',
                'driver' => 'pdo_sqlite',
                'path' => 'db.sqlite',
            );

            $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
            self::$em = Doctrine\ORM\EntityManager::create($dbParams, $config);
        }
        return self::$em;
    }

    public static function one()
    {
        return self::em()->find('NewModel', 1);
    }
}
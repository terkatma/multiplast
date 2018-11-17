<?php

namespace DB;

use Nette;

/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository
{
    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $db)
    {
        $this->database = $db;
    }

    /**
     * Vrací objekt reprezentující databázovou tabulku.
     * @return Nette\Database\Table\Selection
     */
    protected function getTable()
    {
        // název tabulky odvodíme z názvu třídy
        preg_match('#(\w+)Repository$#', get_class($this), $m);
        return $this->database->table(lcfirst($m[1]));
    }


    /**
     * Vrací všechny řádky z tabulky.
     * @return Nette\Database\Table\Selection
     */
    public function findAll()
    {
        //die(var_dump($this->getTable()));
        return $this->getTable();
    }

    /**
     * Vrací řádky podle filtru, např. array('name' => 'John').
     * @return Nette\Database\Table\Selection
     */
    public function findBy(array $by)
    {
        return $this->getTable()->where($by);
    }

    /**
     * Vrací jeden řádek podle filtru, např. array('name' => 'John').
     * @return Nette\Database\Table\Selection
     */
    public function findOneBy(array $by)
    {
        return $this->findBy($by)->limit(1)->fetch();
    }

    /**
     * Vrací jeden řádek podle id.
     * @return Nette\Database\Table\Selection
     */
    public function findById($id)
    {
        return $this->getTable()->get($id);
    }

    public function truncate($table) {
    	try {
		    $this->database->query("TRUNCATE TABLE $table");

	    } catch (Nette\Neon\Exception $exception) {
			throw $exception;
	    }
    }

    public function insert($values)
    {
        return $this->getTable()->insert($values);
    }

    public function update($id, $values)
    {
        $this->findBy(['id' => $id])->update($values);
    }
}

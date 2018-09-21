<?php


namespace App\Components;

use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Class BaseGridComponent
 */
abstract class BaseGridComponent extends BaseComponent
{

	protected $translator;

	public function __construct($parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->translator = new SimpleTranslator([
			'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
			'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
			'ublaboo_datagrid.here' => 'zde',
			'ublaboo_datagrid.items' => 'Položky',
			'ublaboo_datagrid.all' => 'všechny',
			'ublaboo_datagrid.from' => 'z',
			'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
			'ublaboo_datagrid.group_actions' => 'Hromadné akce',
			'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
			'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
			'ublaboo_datagrid.action' => 'Akce',
			'ublaboo_datagrid.previous' => 'Předchozí',
			'ublaboo_datagrid.next' => 'Další',
			'ublaboo_datagrid.choose' => 'Vyberte',
			'ublaboo_datagrid.execute' => 'Provést',
			'ublaboo_datagrid.cancel' => 'Zrušit',
			'ublaboo_datagrid.save' => 'Uložit',
			'ublaboo_datagrid.filter_submit_button' => 'Filtrovat',
			'ublaboo_datagrid.show_default_columns' => 'Resetovat',
			'ublaboo_datagrid.edit' => 'Upravit',
			'ublaboo_datagrid.add' => 'Přidat',
            'ublaboo_datagrid.show_filter' => 'Filtrovat'
		]);
	}

	public function getGrid($name)
	{

		$grid = new DataGrid();
		$this->addComponent($grid, $name);
		$grid->setTranslator($this->translator);
		$grid->setRefreshUrl(False);
		return $grid;
	}

}

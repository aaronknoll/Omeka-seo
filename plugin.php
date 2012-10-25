<?php
add_plugin_hook('install', 'OmekaSeoPlugin::install');
add_plugin_hook('uninstall', 'OmekaSeoPlugin::uninstall');
add_filter('admin_items_form_tabs', 'omekaSeoPlugin::interiorForm');
add_plugin_hook('after_save_form_item', 'OmekaSeoPlugin::seoSave');

//add_plugin_hook('public_theme_header', 'my_plugin_public_theme_header');

//function callUpForm($tabs) {
//	echo $tabs;
//	$ourform	=	new OmekaSeoPlugin;
//	$ourform->interiorForm($tabs);
//}

class OmekaSeoPlugin 
{
	function __construct(){
		$this->id	=	$this->lookupId();
		$this->title	=	$this->getSeoTitle($this->id);
	}
	
	//public static :: methods
    public static function install()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE `{$db->prefix}seos` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      		`item_id` int(10) collate utf8_unicode_ci default NULL,
     		`title_field` varchar(200) collate utf8_unicode_ci,
            PRIMARY KEY (`id`),
            UNIQUE KEY `def_term` (`item_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
    }
    
    public static function uninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `{$db->prefix}seos`;";
        $db->query($sql);
    }
	
	
	public static function getSeoTitle($itemid)
	{
		$db = get_db();
		//insert the quetion relationships
		$mysql = 'SELECT title_field FROM '. $db->prefix .'seos WHERE item_id = "'. $itemid .'"';
		$findrow	= $db->fetchRow($mysql);	
		return $findrow[title_field];
	}
	
	public function interiorForm($tabs)
	{
	$ourform =	 new OmekaSeoPlugin;
    $ttabs = array();
	    foreach($tabs as $key => $html) {
	        if ($key == 'Miscellaneous') {
				//enter the form right here
	            $ht .= 	'<p>Enter the Title attribute information
	            in this box here.</p>
				<p>Note if you do nothing it will look like this: example</p>
				<div id="seoform"><textarea rows="3" cols="75" id="seoValue"  name="seoValue"/>'. $ourform->title. '</textarea>';
				$ht.= '<h3>Other Potential Examples:</h3><p>example 2</p><p>example 3</p>';
	          	$ttabs['SEO'] =  $ht;
	        }
	        $ttabs[$key] = $html;
	    }
    return $ttabs;
	}
	
	public function seoSave($item)
	{
	$db = get_db();
    $titlefield = $_POST['seoValue'];
	$seoobject = new Seo;
	$seoobject->item_id = $item->id;
	$seoobject->title_field = $titlefield;
	
	//insert the quetion relationships
	$data = array(
				'item_id'	=> $seoobject->item_id,
				'title_field' => $seoobject->title_field,
				);
	$db->insert('seo', $data);
	}
	
	public function findTitle($title)
	{
		$id	=	$this->lookupId();
		echo $title;
		echo $id;
	}

	private static function lookupId()
	{
		$request = Omeka_Context::getInstance()->getRequest();
		$id = $request->getParam('id');
		return $id;
	}

}
?>
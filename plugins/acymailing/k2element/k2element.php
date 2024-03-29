<?php
/**
 * @copyright    Copyright (C) 2009-2018 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingK2element extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'k2element');
			$this->params = new acyParameter($plugin->params);
		}
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	function acymailing_getPluginType(){

		$onePlugin = new stdClass();
		$onePlugin->name = 'K2 Content';
		$onePlugin->function = 'acymailingtagk2element_show';
		$onePlugin->help = 'plugin-k2element';

		return $onePlugin;
	}

	function acymailingtagk2element_show(){
		$config = acymailing_config();
		if(version_compare($config->get('version'), '5.5.0', '<')){
			acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			return;
		}

		// Load language file
		
		acymailing_loadLanguageFile('com_k2', JPATH_ADMINISTRATOR);

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.k2element';
		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = acymailing_getUserVar($paramBase.".filter_cat", 'filter_cat', '', 'int');
		$pageInfo->titlelink = acymailing_getUserVar($paramBase.".titlelink", 'titlelink', 'link', 'string');
		$pageInfo->lang = acymailing_getUserVar($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->author = acymailing_getUserVar($paramBase.".author", 'author', $this->params->get('default_author', '0'), 'string');
		$pageInfo->images = acymailing_getUserVar($paramBase.".images", 'images', '1', 'string');
		$pageInfo->contenttype = acymailing_getUserVar($paramBase.".contenttype", 'contenttype', 'intro', 'string');
		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->autotitlelink = acymailing_getUserVar($paramBase.".autotitlelink", 'autotitlelink', 'link', 'string');
		$pageInfo->autoimages = acymailing_getUserVar($paramBase.".autoimages", 'autoimages', '1', 'string');
		$pageInfo->automaxvalue = acymailing_getUserVar($paramBase.".max", 'max', '20', 'int');
		$pageInfo->contentfilter = acymailing_getUserVar($paramBase.".contentfilter", 'contentfilter', 'created', 'string');
		$pageInfo->contentorder = acymailing_getUserVar($paramBase.".contentorder", 'contentorder', 'id', 'string');
		$pageInfo->autocontenttype = acymailing_getUserVar($paramBase.".autocontenttype", 'autocontenttype', 'intro', 'string');
		$pageInfo->pict = acymailing_getUserVar($paramBase.".pict", 'pict', $this->params->get('default_pict', 1), 'string');
		$pageInfo->pictheight = acymailing_getUserVar($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = acymailing_getUserVar($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');

		$searchFields = array('a.id', 'a.alias', 'a.title', 'b.name', 'a.created_by');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		if($this->params->get('displayart', 'all') == 'onlypub'){
			$filters[] = "a.published = 1 AND a.`trash`=0";
		}elseif($this->params->get('displayart', 'all') == 'owned'){
			$filters[] = "a.created_by = ".intval(acymailing_currentUserId())." AND a.`trash`=0 AND a.`published` = 1";
		}else{
			$filters[] = "a.published != -2 AND a.`trash`=0";
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS a.*,b.name,b.username,c.name as catname,c.description as catdesc ';
		$query .= 'FROM `#__k2_items` as a';
		$query .= ' LEFT JOIN `#__users` as b ON a.created_by = b.id';
		$query .= ' LEFT JOIN `#__k2_categories` as c ON a.catid = c.id';
		if(!empty($pageInfo->filter_cat)) $filters[] = "a.catid = ".$pageInfo->filter_cat;

		if(!empty($filters)){
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);
		if(!empty($rows[0]) && !isset($rows[0]->acy_created)){
			acymailing_query('ALTER TABLE `#__k2_items` ADD `acy_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		}

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$pageInfo->elements->total = acymailing_loadResult('SELECT FOUND_ROWS()');
		$pageInfo->elements->page = count($rows);

		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$type = acymailing_getVar('string', 'type');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedContents = new Array();
			function applyContent(contentid, rowClass){
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1){
					window.document.getElementById('content' + contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content' + contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag(){
				var tag = '';
				var otherinfo = '';
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '|type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value.length > 1){
						otherinfo += '|' + document.adminForm.titlelink[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value.length > 1){
							otherinfo += '|' + document.adminForm.author[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '|pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '|maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '|maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '|lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{k2:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}

			var selectedCat = new Array();
			function applyAuto(catid, rowClass){
				if(selectedCat[catid]){
					window.document.getElementById('cat' + catid).className = rowClass;
					delete selectedCat[catid];
				}else{
					window.document.getElementById('cat' + catid).className = 'selectedrow';
					selectedCat[catid] = 'selectedone';
				}

				updateAutoTag();
			}

			function updateAutoTag(){
				tag = '{autok2:';

				for(var icat in selectedCat){
					if(selectedCat[icat] == 'selectedone'){
						tag += icat + '-';
					}
				}

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value != 0){
					tag += '|min:' + document.adminForm.min_article.value;
				}
				if(document.adminForm.max_article.value && document.adminForm.max_article.value != 0){
					tag += '|max:' + document.adminForm.max_article.value;
				}
				if(document.adminForm.contentorder.value){
					tag += document.adminForm.contentorder.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '|meta:' + document.adminForm.meta_article.value;
				}

				for(var i = 0; i < document.adminForm.contenttypeauto.length; i++){
					if(document.adminForm.contenttypeauto[i].checked){
						selectedtype = document.adminForm.contenttypeauto[i].value;
						tag += '|type:' + document.adminForm.contenttypeauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value.length > 1){
						tag += '|' + document.adminForm.titlelinkauto[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value.length > 1){
							tag += '|' + document.adminForm.authorauto[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '|pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '|maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '|maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
				}
				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '|cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '|lang:';
					tag += window.document.getElementById('jflangauto').value;
				}

				tag += '}';

				setTag(tag);
			}
			//-->
		</script>
		<?php

		$picts = array();
		$picts[] = acymailing_selectOption("1", acymailing_translation('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = acymailing_selectOption("resized", acymailing_translation('RESIZED'));
		$picts[] = acymailing_selectOption("0", acymailing_translation('JOOMEXT_NO'));

		//Content type
		$contenttype = array();
		$contenttype[] = acymailing_selectOption("title", acymailing_translation('TITLE_ONLY'));
		$contenttype[] = acymailing_selectOption("intro", acymailing_translation('INTRO_ONLY'));
		$contenttype[] = acymailing_selectOption("text", acymailing_translation('FIELD_TEXT'));
		$contenttype[] = acymailing_selectOption("full", acymailing_translation('FULL_TEXT'));

		//Title link params
		$titlelink = array();
		$titlelink[] = acymailing_selectOption("link", acymailing_translation('JOOMEXT_YES'));
		$titlelink[] = acymailing_selectOption("0", acymailing_translation('JOOMEXT_NO'));

		//Author name
		$authorname = array();
		$authorname[] = acymailing_selectOption("author", acymailing_translation('JOOMEXT_YES'));
		$authorname[] = acymailing_selectOption("0", acymailing_translation('JOOMEXT_NO'));

		$ordering = array();
		$ordering[] = acymailing_selectOption("|order:id,DESC", acymailing_translation('ACY_ID'));
		$ordering[] = acymailing_selectOption("|order:ordering,ASC", acymailing_translation('ACY_ORDERING'));
		$ordering[] = acymailing_selectOption("|order:created,DESC", acymailing_translation('CREATED_DATE'));
		$ordering[] = acymailing_selectOption("|order:modified,DESC", acymailing_translation('MODIFIED_DATE'));
		$ordering[] = acymailing_selectOption("|order:title,ASC", acymailing_translation('FIELD_TITLE'));
		$ordering[] = acymailing_selectOption("|order:rand", acymailing_translation('ACY_RANDOM'));

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('k2_tab');
		echo $tabs->startPanel(acymailing_translation('TAG_ELEMENTS'), 'k2_listings');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td><?php echo acymailing_translation('DISPLAY');?></td>
				<td colspan="2"><?php echo acymailing_radio($contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype); ?></td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateTag();"';
					echo $jflanguages->display('lang', $pageInfo->lang); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($titlelink, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo acymailing_translation('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($authorname, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo acymailing_translation('DISPLAY_PICTURES');?>
				</td>
				<td valign="top"><?php echo acymailing_radio($picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo acymailing_translation('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
					× <?php echo acymailing_translation('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;"/>
				</span>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="100%">
					<input placeholder="<?php echo acymailing_translation('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();"/>
					<button class="btn" onclick="this.form.submit();"><?php echo acymailing_translation('JOOMEXT_GO'); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo acymailing_translation('JOOMEXT_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->_categories($pageInfo->filter_cat); ?>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
			<tr>
				<th class="title">
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_AUTHOR'), 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('K2_CATEGORY'), 'c.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ORDERING'), 'a.ordering', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pagination->getListFooter(); ?>
					<?php echo $pagination->getResultsCounter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for($i = 0, $a = count($rows); $i < $a; $i++){
				$row =& $rows[$i];
				?>
				<tr id="content<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						$text = '<b>'.acymailing_translation('JOOMEXT_ALIAS').' : </b>'.$row->alias;
						echo acymailing_tooltip($text, $row->title, '', $row->title);
						?>
					</td>
					<td>
						<?php
						if(!empty($row->name)){
							$text = '<b>'.acymailing_translation('ACY_NAME', true).' : </b>'.$row->name;
							$text .= '<br /><b>'.acymailing_translation('ACY_USERNAME', true).' : </b>'.$row->username;
							$text .= '<br /><b>'.acymailing_translation('ACY_ID', true).' : </b>'.$row->created_by;
							echo acymailing_tooltip($text, $row->name, '', $row->name);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						echo acymailing_tooltip($row->catdesc, $row->catname, '', $row->catname);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->ordering; ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>"/>
		<?php

		echo $tabs->endPanel();
		echo $tabs->startPanel(acymailing_translation('TAG_CATEGORIES'), 'k2_auto');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo acymailing_translation('DISPLAY');?>
				</td>
				<td colspan="2">
					<?php echo acymailing_radio($contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro'));?>
				</td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateAutoTag();"';
					$jflanguages->id = 'jflangauto';
					echo $jflanguages->display('langauto'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($titlelink, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_titlelink', 'link'));?>
				</td>
				<td>
					<?php echo acymailing_translation('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($authorname, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$this->params->get('default_author', '0')); ?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo acymailing_translation('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo acymailing_radio($picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>

					<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo acymailing_translation('CAPTCHA_WIDTH') ?>
						<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150');?>" style="width:30px;"/>
					× <?php echo acymailing_translation('CAPTCHA_HEIGHT') ?>
						<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150');?>" style="width:30px;"/>
				</span>
				</td>
				<td valign="top"><?php echo acymailing_translation('FIELD_COLUMNS'); ?></td>
				<td valign="top">
					<select name="cols" style="width:150px" onchange="updateAutoTag();" size="1">
						<?php for($o = 1; $o < 11; $o++) echo '<option value="'.$o.'">'.$o.'</option>'; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
				</td>
				<td>
					<?php echo acymailing_translation('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo acymailing_select($ordering, 'contentorder', 'size="1" style="width:150px;" onchange="updateAutoTag();"', 'value', 'text', $pageInfo->contentorder); ?>
				</td>
			</tr>
			<?php if($type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo acymailing_translation('MIN_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo acymailing_translation('JOOMEXT_FILTER'); ?>
					</td>
					<td>
						<?php $filter = acymailing_get('type.contentfilter');
						$filter->onclick = "updateAutoTag();";
						echo $filter->display('contentfilter', '|filter:created'); ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($this->catvalues as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="cat<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyAuto(<?php echo $oneCat->value ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
	}

	private function _categories($filter_cat){
		//select all cats
		$mosetCats = acymailing_loadObjectList('SELECT id,alias,name,parent FROM `#__k2_categories` WHERE trash = 0 ORDER BY `ordering` ASC');
		$this->cats = array();
		foreach($mosetCats as $oneCat){
			$this->cats[$oneCat->parent][] = $oneCat;
		}

		$this->catvalues = array();
		$this->catvalues[] = acymailing_selectOption(0, acymailing_translation('ACY_ALL'));
		$this->_handleChildrens();
		return acymailing_select($this->catvalues, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_cat);
	}

	private function _handleChildrens($parentId = 0, $level = 0){
		if(!empty($this->cats[$parentId])){
			foreach($this->cats[$parentId] as $cat){
				//$cat->name = str_repeat($this->separator,$level).$cat->cat_name;
				$this->catvalues[] = acymailing_selectOption($cat->id, str_repeat(" - - ", $level).$cat->name);
				$this->_handleChildrens($cat->id, $level + 1);
			}
		}
	}


	public function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceOne($email);
	}

	private function _replaceOne(&$email){
		$match = '#{k2:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything...
		if(!$found) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$this->newslanguage = acymailing_loadObject('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.acymailing_escapeDB($email->language).' LIMIT 1');
		}

		//we set the current catid so it can work with several Newsletters...
		$this->currentcatid = -1;
		//Set the read more link:
		$this->readmore = empty($email->template->readmore) ? acymailing_translation('JOOMEXT_READ_MORE') : '<img src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.acymailing_translation('JOOMEXT_READ_MORE', true).'" />';

		//We will need the mailer class as well
		$this->mailerHelper = acymailing_get('helper.mailer');

		$htmlreplace = array();
		$textreplace = array();
		$subjectreplace = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				//Don't need to process twice a tag we already have!
				if(isset($htmlreplace[$oneTag])) continue;

				$content = $this->_replaceContent($allresults, $i);
				$htmlreplace[$oneTag] = $content;
				$textreplace[$oneTag] = $this->mailerHelper->textVersion($content, true);
				$subjectreplace[$oneTag] = strip_tags($content);
			}
		}

		$email->body = str_replace(array_keys($htmlreplace), $htmlreplace, $email->body);
		$email->altbody = str_replace(array_keys($textreplace), $textreplace, $email->altbody);
		$email->subject = str_replace(array_keys($subjectreplace), $subjectreplace, $email->subject);
	}

	private function _replaceContent(&$results, $i){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		//1 : Transform the tag properly...
		$arguments = explode('|', strip_tags($results[1][$i]));
		$tag = new stdClass();
		$tag->id = (int)$arguments[0];
		$tag->itemid = (int)$this->params->get('itemid');
		$tag->wrap = (int)$this->params->get('wordwrap', 0);
		for($i = 1, $a = count($arguments); $i < $a; $i++){
			$args = explode(':', $arguments[$i]);
			$arg0 = trim($args[0]);
			if(empty($arg0)) continue;
			if(isset($args[1])){
				$tag->$arg0 = $args[1];
			}else{
				$tag->$arg0 = true;
			}
		}
		//We used to call it "images" but to make it consistent with the joomlacontent plugin, we rename it into pict.
		if(isset($tag->images) && !isset($tag->pict)) $tag->pict = $tag->images;

		//2 : Load the Joomla article... with the author, the section and the categories to create nice links
		$query = 'SELECT a.*,c.name as cattitle, c.alias as catalias, u.name as authorname FROM `#__k2_items` as a ';
		$query .= ' LEFT JOIN `#__k2_categories` AS c ON c.id = a.catid ';
		$query .= ' LEFT JOIN `#__users` AS u ON u.id = a.created_by ';
		$query .= 'WHERE a.id = '.intval($tag->id).' LIMIT 1';

		$article = acymailing_loadObject($query);

		$result = '';

		//In case of we could not load the article for any reason...
		if(empty($article)){
			if(acymailing_isAdmin()) acymailing_enqueueMessage('The K2 content "'.$tag->id.'" could not be loaded', 'notice');
			return $result;
		}

		//We just loaded the article but we may need to translate it depending on tag->lang...
		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;

		$acypluginsHelper->translateItem($article, $tag, 'k2_items');
		if(!empty($tag->lang)){
			//We will load the correct article in the jf tables
			$langid = (int)substr($tag->lang, strpos($tag->lang, ',') + 1);
			if(!empty($langid) && (file_exists(JPATH_SITE.DS.'components'.DS.'com_joomfish'.DS.'helpers'.DS.'defines.php') || file_exists(JPATH_SITE.DS.'components'.DS.'com_falang'))){
				$translation = acymailing_loadResult("SELECT value FROM ".((ACYMAILING_J16 && file_exists(JPATH_SITE.DS.'components'.DS.'com_falang')) ? '`#__falang_content`' : '`#__jf_content`')." WHERE `published` = 1 AND `reference_table` = 'k2_categories' AND `language_id` = $langid AND `reference_field` = 'name' AND `reference_id` = ".$article->catid);
				if(!empty($translation)) $article->cattitle = $translation;
			}
		}

		$varFields = array();
		foreach($article as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		//When we load an artice, we may have a wrong content... we clean it.
		$acypluginsHelper->cleanHtml($article->introtext);
		$acypluginsHelper->cleanHtml($article->fulltext);

		require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
		$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias), $article->catid);
		if(!empty($tag->itemid)) $link .= (strpos($link, '?') ? '&' : '?').'Itemid='.$tag->itemid;
		if(!empty($tag->lang)){
			$lang = substr($tag->lang, 0, strpos($tag->lang, ACYMAILING_J16 ? '-' : ','));
			if(empty($lang)) $lang = $tag->lang;
			$link .= (strpos($link, '?') ? '&' : '?').'lang='.$lang;
		}
		if(!empty($tag->autologin)) $link .= (strpos($link, '?') ? '&' : '?').'user={usertag:username|urlencode}&passw={usertag:password|urlencode}';

		if(empty($tag->lang) && !empty($article->language) && $article->language != '*'){
			//We need to add the language code in the url if this article is only for one language file...
			//Let's load the right language code from the
			if(!isset($this->langcodes[$article->language])){
				//We load the right lang code from the language table.
				$this->langcodes[$article->language] = acymailing_loadResult('SELECT sef FROM #__languages WHERE lang_code = '.acymailing_escapeDB($article->language).' ORDER BY `published` DESC LIMIT 1');
				if(empty($this->langcodes[$article->language])) $this->langcodes[$article->language] = $article->language;
			}
			$link .= (strpos($link, '?') ? '&' : '?').'lang='.$this->langcodes[$article->language];
		}

		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		$article->extra_fields = json_decode($article->extra_fields);
		if(!empty($article->extra_fields)){
			$newFields = array();
			foreach($article->extra_fields as $oneField){
				$newFields[$oneField->id] = $oneField;
			}
			$article->extra_fields = $newFields;
		}

		//Add the title with a link or not on it.
		//If we add a link, we add in the same time a name="content-CONTENTID" so that we will be able to parse the content to create a nice summary
		$styleTitle = '';
		$styleTitleEnd = '';
		if($tag->type != "title"){
			$styleTitle = '<h2 class="acymailing_title">';
			$styleTitleEnd = '</h2>';
		}

		$resultTitle = '';

		if(empty($tag->notitle)){
			if(!empty($tag->link)){
				$resultTitle = '<a href="'.$link.'" ';
				if($tag->type != "title") $resultTitle .= 'style="text-decoration:none" name="k2content-'.$article->id.'" ';
				$resultTitle .= 'target="_blank" >'.$article->title.'</a>';
			}else{
				$resultTitle = $article->title;
			}
			$resultTitle = $styleTitle.$resultTitle.$styleTitleEnd;
		}

		//Add the author...
		if(!empty($tag->author)){
			$authorName = empty($article->created_by_alias) ? $article->authorname : $article->created_by_alias;
			if($tag->type == 'title') $result .= '<br />';
			$result .= '<span class="acymailing_authorname">'.$authorName.'</span><br />';
			$varFields['{author}'] = $authorName;
		}

		if(!empty($tag->created)){
			if($tag->type == 'title') $result .= '<br />';
			$dateFormat = empty($tag->dateformat) ? acymailing_translation('DATE_FORMAT_LC2') : $tag->dateformat;
			$result .= '<span class="createddate">'.acymailing_date($article->created, $dateFormat).'</span><br />';
			$varFields['{created}'] = acymailing_date($article->created, $dateFormat);
		}

		//We add the intro text
		if($tag->type != "title"){

			if($tag->type == "intro"){
				$forceReadMore = false;
				$article->introtext = $acypluginsHelper->wrapText($article->introtext, $tag);
				if(!empty($acypluginsHelper->wraped)) $forceReadMore = true;
			}
			if(empty($article->fulltext) OR $tag->type != "text"){
				$result .= $article->introtext;
			}

			//We add the full text
			if($tag->type == "intro"){
				//We add the read more link but only if we have a fulltext after...
				if(empty($tag->noreadmore) && (!empty($article->fulltext) OR $forceReadMore)){
					$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
					$result .= '<a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$readMoreText.'</span></a>';
				}
			}elseif(!empty($article->fulltext)){
				if($tag->type != "text" && !empty($article->introtext) && !preg_match('#^<[div|p]#i', trim($article->fulltext))) $result .= '<br />';
				$result .= $article->fulltext;
			}

			//Display custom fields...
			if(!empty($tag->customfields) && !empty($article->extra_fields)){
				//Load the custom fields if we don't already have them...
				if(empty($this->customfields)){
					//Load the extra fields once
					$this->customfields = acymailing_loadObjectList("SELECT * FROM #__k2_extra_fields WHERE `published` = 1 AND `type` NOT IN ('csv','labels') ORDER BY `ordering` ASC");
				}

				$excluded = empty($tag->excludedcf) ? array() : explode(',', $tag->excludedcf);
				foreach($this->customfields as $oneField){
					//We set it... as we may not go into that loop if there is no value.
					$varFields['{cf:'.$oneField->name.'}'] = '';

					if(in_array($oneField->id, $excluded)) continue;

					if(!isset($article->extra_fields[$oneField->id]) || !isset($article->extra_fields[$oneField->id]->value)) continue;
					$disValue = '';
					if($oneField->type == 'date'){
						$time = $article->extra_fields[$oneField->id]->value;
						$dateFormat = (!ACYMAILING_J16) ? '%A, %d %B %Y' : 'l, d F Y';
						$disValue = acymailing_date($time, $dateFormat, false);
					}elseif($oneField->type == 'link'){
						$disValue = '<a target="_blank" href="'.$article->extra_fields[$oneField->id]->value[1].'" >'.$article->extra_fields[$oneField->id]->value[0].'</a>';
					}elseif($oneField->type == 'multipleSelect' || $oneField->type == 'select' || $oneField->type == 'radio'){
						$object = json_decode($oneField->value);
						$myValues = $article->extra_fields[$oneField->id]->value;
						foreach($object as $oneObject){
							if((is_string($myValues) && $myValues == $oneObject->value) || is_array($myValues) && in_array($oneObject->value, $myValues)){
								$disValue .= $oneObject->name.', ';
							}
						}
						$disValue = trim($disValue, ', ');
					}elseif($oneField->type == 'image'){
						$disValue = '<img src="'.ltrim($article->extra_fields[$oneField->id]->value, '/').'" alt="" />';
					}elseif(is_string($article->extra_fields[$oneField->id]->value)){
						$disValue = nl2br($article->extra_fields[$oneField->id]->value);
					}else{
						continue;
					}
					if(strlen($disValue) < 1) continue;

					$article->extra_fields[$oneField->id]->disValue = $disValue;
					$varFields['{cf:'.$oneField->name.'}'] = $disValue; // Easier for custom templates
					$result .= '<br /><span class="fieldname">'.$oneField->name.'</span>: <span class="fieldvalue">'.$disValue.'</span>';
				}
			}

			//Display attachments...
			if(!empty($tag->attachments)){
				$attachments = acymailing_loadObjectList('SELECT * FROM #__k2_attachments WHERE `itemID` = '.intval($article->id));
				if(!empty($attachments)){
					$result .= '<fieldset><legend>'.acymailing_translation('ATTACHMENTS').'</legend>';
					foreach($attachments as $oneAttachment){
						$result .= '<a href="'.acymailing_rootURI().'media/k2/attachments/'.$oneAttachment->filename.'" target="_blank" >'.$oneAttachment->title.'</a><br />';
					}
					$result .= '</fieldset>';
				}
			}

			$md5picture = md5("Image".$article->id);
			//When we have a specific size, we will use the larger picture, not the small one.
			if(file_exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.$md5picture.'_S.jpg') && (empty($tag->pict) || $tag->pict != "resized")){
				$imagePath = acymailing_rootURI().'media/k2/items/cache/'.$md5picture.'_S.jpg';
			}elseif(file_exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.$md5picture.'_L.jpg')){
				$imagePath = acymailing_rootURI().'media/k2/items/cache/'.$md5picture.'_L.jpg';
			}

			if(empty($imagePath) || !empty($tag->removemainpict)){
				$result = $resultTitle.$result;
			}else{
				$varFields['{imagePath}'] = $imagePath;
				$imageLink = '<img src="'.$imagePath.'" alt="'.$article->image_caption.'" />';
				if(!empty($tag->link)) $imageLink = '<a href="'.$link.'" target="_blank" style="text-decoration:none" >'.$imageLink.'</a>';
				$varFields['{imageLink}'] = $imageLink;
				$result = $resultTitle.'<table cellspacing="5" cellpadding="0" border="0" ><tr><td valign="top">'.$imageLink.'</td><td valign="top">'.$result.'</td></tr></table>';
			}

			$result = '<div class="acymailing_content" style="clear:both">'.$result.'</div>';
		}else{
			$result = $resultTitle.$result;
		}

		//Add the cat title...
		if(!empty($tag->cattitle) && $this->currentcatid != $article->catid){
			$this->currentcatid = $article->catid;
			$result = '<h3 class="cattitle">'.$article->cattitle.'</h3>'.$result;
		}

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'k2element.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'k2element.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields), $varFields, $result);
		}

		$result = $acypluginsHelper->removeJS($result);
		$result = str_replace('{slider Opis}', '', $result);

		//We have our content... lets check the pictures options
		if(isset($tag->pict)){
			$pictureHelper = acymailing_get('helper.acypict');
			if($tag->pict == '0'){
				$result = $pictureHelper->removePictures($result);
			}elseif($tag->pict == 'resized'){
				$pictureHelper->maxHeight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
				$pictureHelper->maxWidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
				if($pictureHelper->available()){
					$result = $pictureHelper->resizePictures($result);
				}elseif(acymailing_isAdmin()){
					acymailing_enqueueMessage($pictureHelper->error, 'notice');
				}
			}
		}

		return $result;
	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags), $this->tags, $email->altbody);
			foreach($this->tags as $tag => $result){
				$email->subject = str_replace($tag, strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result)), $email->subject);
			}
		}
	}

	public function acymailing_generateautonews(&$email){

		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		$time = time();


		//Check if we should generate the SmartNewsletter or not...
		$match = '#{autok2:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything... so we won't try to stop the generation
		if(!$found) return $return;

		$this->tags = array();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($this->tags[$oneTag])) continue;

				$arguments = explode('|', strip_tags($allresults[1][$i]));
				//The first argument is a list of sections and cats...
				$allcats = explode('-', $arguments[0]);
				$parameter = new stdClass();
				for($i = 1; $i < count($arguments); $i++){
					$args = explode(':', $arguments[$i]);
					$arg0 = trim($args[0]);
					if(empty($arg0)) continue;
					if(isset($args[1])){
						$parameter->$arg0 = $args[1];
					}else{
						$parameter->$arg0 = true;
					}
				}
				//Load the articles based on all arguments...
				$selectedArea = array();
				foreach($allcats as $oneCat){
					if(empty($oneCat)) continue;
					$selectedArea[] = (int)$oneCat;
				}

				$query = 'SELECT item.id FROM `#__k2_items` as item';
				$where = array();

				if(!empty($parameter->tags)){
					//We have tags... we select articles based on their tags
					//tags:tennis,handball,basket means the article should match all tags.
					$alltags = explode(',', $parameter->tags);
					$tagcond = array();
					foreach($alltags as $onetag){
						if(empty($onetag)) continue;
						$tagcond[] = acymailing_escapeDB(trim($onetag));
					}
					if(!empty($tagcond)){
						$allTagIds = acymailing_loadResultArray('SELECT id FROM #__k2_tags WHERE name IN ('.implode(',', $tagcond).')');
						if(count($allTagIds) != count($tagcond)){
							acymailing_enqueueMessage(count($tagcond).' tags specified but we could only load '.count($allTagIds).' of them... Please make sure the tags you specified are valid', 'error');
						}
						foreach($allTagIds as $oneTagId){
							$query .= ' JOIN `#__k2_tags_xref` as tag'.$oneTagId.' ON item.id = tag'.$oneTagId.'.itemID AND tag'.$oneTagId.'.tagID = '.$oneTagId;
						}
					}
				}

				if(!empty($selectedArea)){
					$where[] = '`catid` IN ('.implode(',', $selectedArea).')';
				}

				if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
					$condition = '`publish_up` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
					// We need acy_created, the hour is not stored by K2 in the created date field
					$condition .= ' OR `acy_created` > DATE_FORMAT(CURRENT_TIMESTAMP()-SEC_TO_TIME('.intval(time() - $email->params['lastgenerateddate']).'), \'%Y-%m-%d %H:%i:%s\')';
					if($parameter->filter == 'modify'){
						$condition .= ' OR `modified` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
					}

					$where[] = $condition;
				}

				if(!empty($parameter->featured)){
					$where[] = '`featured` = 1';
				}elseif(!empty($parameter->nofeatured)){
					$where[] = '`featured` = 0';
				}

				$where[] = '`publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
				$where[] = '`publish_down` > \''.date('Y-m-d H:i:s', $time - date('Z')).'\' OR `publish_down` = 0';
				if(empty($parameter->nopublished)) $where[] = '`published` = 1';
				$where[] = '`trash`=0';

				//Handle a date range in the query
				if(!empty($parameter->maxcreated)){
					$date = strtotime($parameter->maxcreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->maxcreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` < '.acymailing_escapeDB(date('Y-m-d H:i:s', $date));
				}

				if(!empty($parameter->mincreated)){
					$date = strtotime($parameter->mincreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->mincreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` > '.acymailing_escapeDB(date('Y-m-d H:i:s', $date));
				}

				//Access for J1.5.0 only
				if(!ACYMAILING_J16){
					if(isset($parameter->access)){
						$where[] = 'access <= '.intval($parameter->access);
					}else{
						if($this->params->get('contentaccess', 'registered') == 'registered'){
							$where[] = 'access <= 1';
						}elseif($this->params->get('contentaccess', 'registered') == 'public') $where[] = 'access = 0';
					}
				}elseif(isset($parameter->access)){
					//We set it only if the access is defined in the tag
					$where[] = 'access = '.intval($parameter->access);
				}

				//Add filter on language...
				if(!empty($parameter->language)){
					//We may have several languages separated by a comma
					$allLanguages = explode(',', $parameter->language);
					$langWhere = 'language IN (';
					foreach($allLanguages as $oneLanguage){
						$langWhere .= acymailing_escapeDB(trim($oneLanguage)).',';
					}
					$where[] = trim($langWhere, ',').')';
				}

				$query .= ' WHERE ('.implode(') AND (', $where).')';

				if(!empty($parameter->order)){
					if($parameter->order == 'rand'){
						$query .= ' ORDER BY rand()';
					}else{
						$ordering = explode(',', $parameter->order);
						$query .= ' ORDER BY `'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}
				}

				$start = '';
				if(!empty($parameter->start)) $start = intval($parameter->start).',';
				if(empty($parameter->max)) $parameter->max = 100;
				//We add a limit for the preview otherwise we could break everything
				$query .= ' LIMIT '.$start.(int)$parameter->max;

				$allArticles = acymailing_loadResultArray($query);

				if(!empty($parameter->min) && count($allArticles) < $parameter->min){
					//We won't generate the Newsletter
					$return->status = false;
					$return->message = 'Not enough k2 content for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
				}

				$stringTag = empty($parameter->noentrytext) ? '' : $parameter->noentrytext;
				if(!empty($allArticles)){
					if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'autok2.php')){
						ob_start();
						require(ACYMAILING_MEDIA.'plugins'.DS.'autok2.php');
						$stringTag = ob_get_clean();
					}else{
						//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
						$arrayElements = array();
						unset($parameter->id);
						$numArticle = 1;
						foreach($allArticles as $oneArticleId){
							$args = array();
							$args[] = 'k2:'.$oneArticleId;
							foreach($parameter as $oneParam => $val){
								if(is_bool($val)){
									$args[] = $oneParam;
								}else $args[] = $oneParam.':'.$val;
							}
							$arrayElements[] = '{'.implode('|', $args).'}';
						}
						$stringTag = $this->acypluginsHelper->getFormattedResult($arrayElements, $parameter);
					}
				}

				$this->tags[$oneTag] = $stringTag;
			}
		}

		return $return;
	}
}//endclass
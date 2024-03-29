<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.10
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php if($this->values->filter){ ?>
	<input placeholder="<?php echo acymailing_translation('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $this->escape($this->pageInfo->search); ?>" class="inputbox"/>
	<button class="btn button buttongo" onclick="this.form.submit();"><?php echo acymailing_translation('JOOMEXT_GO'); ?></button>
	<button class="btn button buttonreset" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo acymailing_translation('JOOMEXT_RESET'); ?></button>
<?php }
echo $this->ordering;
$k = 1;
for($i = 0, $a = count($this->rows); $i < $a; $i++){
	$row =& $this->rows[$i];
	$row->subject = acyEmoji::Decode($row->subject);
	echo '<div class="archiveRow archiveRow'.$k.$this->values->suffix.'">';

	if(!empty($row->thumb)) echo '<img class="archiveItemPict" src="'.$row->thumb.'"/>';
	echo '<span class="acyarchivetitle">';
	$link = acymailing_completeLink('archive&task=view&listid='.$row->listid.'&mailid='.$row->mailid.'-'.strip_tags($row->alias).$this->item, (bool)$this->config->get('open_popup', 1));
	if($this->config->get('open_popup', 1) == 1){
		echo acymailing_popup($link, acymailing_dispSearch($row->subject, $this->pageInfo->search), '', intval($this->config->get('popup_width', 750)), intval($this->config->get('popup_height', 550)));
	}else{
		echo '<a href="'.$link.'">'.acymailing_dispSearch($row->subject, $this->pageInfo->search).'</a>';
	}
	if(in_array($row->listid, $this->manageableLists)){
		if(($this->config->get('frontend_modif', 1) || ($row->userid == $this->my->id)) && ($this->config->get('frontend_modif_sent', 1) || empty($row->senddate))){ ?>
			<span class="acyeditbutton"><a href="<?php echo acymailing_completeLink('frontnewsletter&task=edit&mailid='.$row->mailid.'&listid='.$row->listid); ?>" title="<?php echo acymailing_translation('ACY_EDIT', true) ?>"><img src="<?php echo ACYMAILING_IMAGES ?>icons/icon-16-edit.png" alt="<?php echo acymailing_translation('ACY_EDIT', true) ?>"/></a></span>
		<?php }

		if(!empty($row->senddate) && acymailing_isAllowed($this->config->get('acl_statistics_manage', 'all'))){
			echo '<span class="acystatsbutton">'.acymailing_popup(acymailing_completeLink('frontdiagram&task=mailing&mailid='.$row->mailid.'&listid='.$row->listid, true), '<img src="'.ACYMAILING_IMAGES.'icons/icon-16-stats.png" alt="'.acymailing_translation('STATISTICS', true).'"/>', '', 800, 590).'</span>';
		}
	}
	echo '</span>';
	if($this->values->show_senddate && !empty($row->senddate)){
		echo '<span class="sentondate">'.acymailing_translation_sprintf('ACY_SENT_ON', acymailing_getDate($row->senddate, acymailing_translation('DATE_FORMAT_LC3'))).'</span>';
	}
	if($this->values->show_receiveemail){ ?>
		<span class="receiveviaemail">
				<input onclick="changeReceiveEmail(this.checked)" type="checkbox" name="receivemail[]" value="<?php echo $row->mailid; ?>" id="receive_<?php echo $row->mailid; ?>"/> <label for="receive_<?php echo $row->mailid; ?>"><?php echo acymailing_translation('RECEIVE_VIA_EMAIL'); ?></label>
			</span>
		<?php
		if(!empty($row->summary)) echo '<br/>';
	}
	if(!empty($row->summary)) echo '<span class="archiveItemDesc">'.nl2br($row->summary).'</span>';
	echo '</div>';
	$k = 3 - $k;
}
?>
<div class="archivePagination">
	<?php echo $this->pagination->getListFooter();
	echo $this->pagination->getResultsCounter(); ?>
</div>

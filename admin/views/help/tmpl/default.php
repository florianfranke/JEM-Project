<?php
/**
 * @version 1.9.1
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$options = array(
    'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
    'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
    'useCookie' => true, // this must not be a string. Don't use quotes.
);


?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table border="1" class="adminform">
	<tr>
		<td colspan="2">
			<table style="width:100%">
				<tr>
					<td>
						<strong><?php echo JText::_( 'COM_JEM_SEARCH' ); ?></strong>
						<input class="text_area" type="hidden" name="option" value="com_jem" />
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->helpsearch;?>" class="inputbox" />
						<input type="submit" value="<?php echo JText::_( 'COM_JEM_GO' ); ?>" class="button" />
						<input type="button" value="<?php echo JText::_( 'COM_JEM_RESET' ); ?>" class="button" onclick="f=document.adminForm;f.filter_search.value='';f.submit()" />
					</td>
					<td style="text-align:right">
						<a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/intro.html'; ?>" target='helpFrame'><?php echo JText::_( 'COM_JEM_HOME' ); ?></a>
						|
						<a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/gethelp.html'; ?>" target='helpFrame'><?php echo JText::_( 'COM_JEM_GET_HELP' ); ?></a>
						|
						<a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/givehelp.html'; ?>" target='helpFrame'><?php echo JText::_( 'COM_JEM_GIVE_HELP' ); ?></a>
						|
						<a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/credits.html'; ?>" target='helpFrame'><?php echo JText::_( 'COM_JEM_CREDITS' ); ?></a>
						|
						<?php echo JHTML::_('link', 'http://www.gnu.org/licenses/gpl-2.0.html', JText::_( 'COM_JEM_LICENSE' ), array('target' => 'helpFrame')) ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table style="width:100%" class="adminlist">
	<tr valign="top">
		<td align="left">

			<?php
			echo JHtml::_('sliders.start', 'det-pane', $options);

			$title2 = JText::_( 'COM_JEM_SCREEN_HELP' );
			echo JHtml::_('sliders.panel', $title2, 'help');
			?>
			<table class="adminlist">
				<?php
				foreach ($this->toc as $k=>$v) {
					echo '<tr>';
					echo '<td>';
					echo JHTML::Link('components/com_jem/help/'.$this->langTag.'/'.$k, $v, array('target' => 'helpFrame'));
					echo '</td>';
					echo '</tr>';
				}
				?>
			</table>

			<?php
			echo JHtml::_('sliders.end');
		  	?>
		</td>
		<td width="75%">
			<iframe name="helpFrame" src="<?php echo 'components/com_jem/help/'.$this->langTag.'/intro.html'; ?>" class="helpFrame"></iframe>
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_jem" />
<input type="hidden" name="view" value="help" />
<input type="hidden" name="task" value="" />

</form>

<p class="copyright">
	<?php echo JEMAdmin::footer( ); ?>
</p>

<?php
//keep session alive
JHTML::_('behavior.keepalive');
?>
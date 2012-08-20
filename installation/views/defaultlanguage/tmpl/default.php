<?php
/**
 * @package    Joomla.Installation
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php echo JHtml::_('installation.stepbarlanguages'); ?>
<form action="index.php" method="post" id="adminForm" class="form-validate form-horizontal">
	<div class="btn-toolbar">
		<div class="btn-group pull-right">
			<a class="btn" href="#" onclick="return Install.goToPage('languages');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><i class="icon-arrow-left"></i> <?php echo JText::_('JPrevious'); ?></a>
			<?php
			// Check if ther is any languages to list, if not you cannot move forward
			if($this->items) :
				?>
				<a  class="btn btn-primary" href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><i class="icon-arrow-right icon-white"></i> <?php echo JText::_('JNext'); ?></a>
				<?php endif; ?>
		</div>
	</div>
	<h3><?php echo JText::_('INSTL_DEFAULTLANGUAGE'); ?></h3>
	<hr class="hr-condensed" />
	<p><?php echo JText::_('INSTL_DEFAULTLANGUAGE_DESC'); ?></p>
	<table class="table table-striped table-condensed">
		<THEAD>
		<tr>
			<th>
				<?php echo JText::_('INSTL_DEFAULLANGUAGE_COLUMN_HEADER_SELECT'); ?>
			</th>
			<th>
				<?php echo JText::_('INSTL_DEFAULLANGUAGE_COLUMN_HEADER_LANGUAGE'); ?>
			</th>
			<th>
				<?php echo JText::_('INSTL_DEFAULLANGUAGE_COLUMN_HEADER_TAG'); ?>
			</th>
		</tr>
		</THEAD>
		<TBODY>
			<?php foreach($this->items as $lang) : ?>
			<tr>
				<td>
					<input type="radio" name="lang" value="<?php echo $lang->language; ?>" <?php if ($lang->published) echo 'checked="checked"'; ?>/>
				</td>
				<td align="center">
					<?php echo $lang->name; ?>
				</td>
				<td align="center">
					<?php echo $lang->language; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</TBODY>
	</table>
	<input type="hidden" name="task" value="languages.setDefaultLanguage" />
	<?php echo JHtml::_('form.token'); ?>
</form>

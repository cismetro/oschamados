<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

$n         = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

Factory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");

?>
<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() ?? ""); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
		<fieldset class="filters d-flex justify-content-between mb-3">
			<?php if ($this->params->get('filter_field')) : ?>
				<div class="input-group">
					<label class="filter-search-lbl sr-only" for="filter-search">
						<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL') . '&#160;'; ?>
					</label>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="form-control uk-input" onchange="document.adminForm.submit();" title="<?php echo Text::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>">
					<span class="input-group-btn">
						<button type="button" name="filter-search-button" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="document.adminForm.submit();" class="uk-button uk-button-secondary">
							<span class="fas fa-search" aria-hidden="true"></span>
						</button>
						<button type="reset" name="filter-clear-button" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" class="uk-button uk-button-secondary" onclick="resetFilter(); document.adminForm.submit();">
							<span class="fas fa-times" aria-hidden="true"></span>
						</button>
					</span>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('show_pagination_limit')) : ?>
				<div class="btn-group float-right">
					<label for="limit" class="sr-only">
						<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif; ?>

			<input type="hidden" name="filter_order" value="">
			<input type="hidden" name="filter_order_Dir" value="">
			<input type="hidden" name="limitstart" value="">
			<input type="hidden" name="task" value="">
		</fieldset>
	<?php endif; ?>

	<?php if ($this->items === false || $n === 0) : ?>
		<p><?php echo Text::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>
		<table class="category table table-striped table-bordered table-hover">
			<?php if ($this->params->get('show_headings')) : ?>
				<thead>
					<tr>
						<th id="categorylist_header_title">
							<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'c.core_title', $listDirn, $listOrder); ?>
						</th>
						<?php if ($date = $this->params->get('tag_list_show_date')) : ?>
							<th id="categorylist_header_date">
								<?php if ($date === 'created') : ?>
									<?php echo HTMLHelper::_('grid.sort', 'COM_TAGS_' . $date . '_DATE', 'c.core_created_time', $listDirn, $listOrder); ?>
								<?php elseif ($date === 'modified') : ?>
									<?php echo HTMLHelper::_('grid.sort', 'COM_TAGS_' . $date . '_DATE', 'c.core_modified_time', $listDirn, $listOrder); ?>
								<?php elseif ($date === 'published') : ?>
									<?php echo HTMLHelper::_('grid.sort', 'COM_TAGS_' . $date . '_DATE', 'c.core_publish_up', $listDirn, $listOrder); ?>
								<?php endif; ?>
							</th>
						<?php endif; ?>

					</tr>
				</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if ($this->items[$i]->core_state == 0) : ?>
						<tr class="table-danger">
					<?php else : ?>
						<tr>
					<?php endif; ?>
						<td <?php if ($this->params->get('show_headings')) echo "headers=\"categorylist_header_title\""; ?> class="list-title">
							<a href="<?php echo Route::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
								<?php echo $this->escape($item->core_title); ?>
							</a>
							<?php if ($item->core_state == 0) : ?>
								<span class="list-published badge badge-warning">
									<?php echo Text::_('JUNPUBLISHED'); ?>
								</span>
							<?php endif; ?>
						</td>
						<?php if ($this->params->get('tag_list_show_date')) : ?>
							<td headers="categorylist_header_date" class="list-date small">
								<?php
								echo HTMLHelper::_(
									'date', $item->displayDate,
									$this->escape($this->params->get('date_format', Text::_('DATE_FORMAT_LC3')))
								); ?>
							</td>
						<?php endif; ?>

					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<?php // Add pagination links ?>
	<?php if (!empty($this->items)) : ?>
		<?php if (($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
			<div class="w-100">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="counter float-right pt-3 pr-2">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</form>
